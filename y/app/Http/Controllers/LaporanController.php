<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan seluruh data laporan admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Mengambil input filter periode tanggal jika disediakan
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            // Default ke 30 hari terakhir jika tidak ada filter (riset per 30 hari)
            if (!$startDate && !$endDate) {
                $startDate = \Carbon\Carbon::now()->subDays(30)->toDateString();
                $endDate = \Carbon\Carbon::now()->toDateString();
            }

            // 1. Total Pendapatan
            // Mengambil jumlah total_harga (atau fallback total_payar) dari booking yang sudah dikonfirmasi
            $totalPendapatanQuery = Booking::whereIn('status_booking', ['Dikonfirmasi', 'Selesai', 'Diproses', 'Lunas']);

            if ($startDate) {
                $totalPendapatanQuery->whereDate('tbl_booking.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $totalPendapatanQuery->whereDate('tbl_booking.created_at', '<=', $endDate);
            }

            // Gunakan total_harga jika tersedia, fallback ke total_payar
            $total_pendapatan = (float) $totalPendapatanQuery->sum(\DB::raw('COALESCE(total_harga, total_payar, 0)'));

            // 2. Jumlah Tiket Terjual
            // Mengambil jumlah total tiket dari booking yang sudah dikonfirmasi
            $jumlahTiketQuery = Booking::whereIn('status_booking', ['Dikonfirmasi', 'Selesai', 'Diproses', 'Lunas']);

            if ($startDate) {
                $jumlahTiketQuery->whereDate('tbl_booking.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $jumlahTiketQuery->whereDate('tbl_booking.created_at', '<=', $endDate);
            }

            // Gunakan jumlah_tiket jika tersedia, fallback ke jml_tiket
            $total_tiket_terjual = (int) $jumlahTiketQuery->sum(\DB::raw('COALESCE(jumlah_tiket, jml_tiket, 0)'));

            // 3. Jumlah Pengunjung
            // Setiap tiket melambangkan satu pengunjung, sehingga jumlah_pengunjung setara dengan total_tiket_terjual
            $jumlah_pengunjung = $total_tiket_terjual;

            // 4. Rekap Pendapatan per Kategori Tiket (jenis_tiket)
            $kategoriQuery = Booking::whereIn('status_booking', ['Dikonfirmasi', 'Selesai', 'Diproses', 'Lunas']);

            if ($startDate) {
                $kategoriQuery->whereDate('tbl_booking.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $kategoriQuery->whereDate('tbl_booking.created_at', '<=', $endDate);
            }

            $pendapatan_per_kategori = $kategoriQuery
                ->select('jenis_tiket')
                ->selectRaw('SUM(COALESCE(total_harga, total_payar, 0)) as total_pendapatan')
                ->selectRaw('SUM(COALESCE(jumlah_tiket, jml_tiket, 0)) as jumlah_tiket_terjual')
                ->groupBy('jenis_tiket')
                ->get()
                ->map(function ($item) {
                    return [
                        'kategori' => $item->jenis_tiket,
                        'total_pendapatan' => (float) $item->total_pendapatan,
                        'jumlah_tiket_terjual' => (int) $item->jumlah_tiket_terjual,
                    ];
                });

            // 5. Grafik Kunjungan (Jumlah Pengunjung berdasarkan tanggal_pendakian)
            $grafikQuery = Booking::whereIn('status_booking', ['Dikonfirmasi', 'Selesai', 'Diproses', 'Lunas']);

            if ($startDate) {
                $grafikQuery->whereDate('tbl_booking.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $grafikQuery->whereDate('tbl_booking.created_at', '<=', $endDate);
            }

            $grafik_kunjungan = $grafikQuery
                ->join('tbl_slot_pendakian', 'tbl_booking.id_slot', '=', 'tbl_slot_pendakian.id_slot')
                ->select('tbl_slot_pendakian.tanggal_pendakian')
                ->selectRaw('SUM(COALESCE(tbl_booking.jumlah_tiket, tbl_booking.jml_tiket, 0)) as jumlah_pengunjung')
                ->groupBy('tbl_slot_pendakian.tanggal_pendakian')
                ->orderBy('tbl_slot_pendakian.tanggal_pendakian', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => $item->tanggal_pendakian,
                        'jumlah_pengunjung' => (int) $item->jumlah_pengunjung,
                    ];
                });

            // 6. Total Counts
            // Total Booking berdasarkan jumlah tiket yang dipesan (bukan jumlah transaksi)
            $totalBookingQuery = Booking::query();
            if ($startDate) $totalBookingQuery->whereDate('tbl_booking.created_at', '>=', $startDate);
            if ($endDate) $totalBookingQuery->whereDate('tbl_booking.created_at', '<=', $endDate);
            $total_booking = (int) $totalBookingQuery->sum(\DB::raw('COALESCE(jumlah_tiket, jml_tiket, 0)'));
            
            // Total Pembayaran muncul berdasarkan jumlah transaksi
            $totalPembayaranQuery = Pembayaran::query();
            if ($startDate) $totalPembayaranQuery->whereDate('tbl_pembayaran.created_at', '>=', $startDate);
            if ($endDate) $totalPembayaranQuery->whereDate('tbl_pembayaran.created_at', '<=', $endDate);
            $total_pembayaran = $totalPembayaranQuery->count();
            
            $totalPelangganQuery = Pelanggan::query();
            if ($startDate) $totalPelangganQuery->whereDate('tbl_pelanggan.created_at', '>=', $startDate);
            if ($endDate) $totalPelangganQuery->whereDate('tbl_pelanggan.created_at', '<=', $endDate);
            $total_pelanggan = $totalPelangganQuery->count();

            // Mengembalikan response JSON yang sukses
            return response()->json([
                'total_pendapatan' => $total_pendapatan,
                'total_tiket_terjual' => $total_tiket_terjual,
                'jumlah_tiket' => $total_tiket_terjual, // backward compat
                'jumlah_pengunjung' => $jumlah_pengunjung,
                'pendapatan_per_kategori' => $pendapatan_per_kategori,
                'grafik_kunjungan' => $grafik_kunjungan,
                'total_booking' => $total_booking,
                'total_pembayaran' => $total_pembayaran,
                'total_pelanggan' => $total_pelanggan,
            ], 200);

        } catch (\Exception $e) {
            // Mengembalikan response JSON error jika terjadi kendala database/query
            return response()->json([
                'message' => 'Terjadi kesalahan saat memuat data laporan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
