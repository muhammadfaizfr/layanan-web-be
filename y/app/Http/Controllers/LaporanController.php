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

            // 1. Total Pendapatan
            // Mengambil jumlah total_payar dari booking yang status pembayarannya 'Valid'
            $totalPendapatanQuery = Booking::whereHas('pembayaran', function ($query) {
                $query->where('status_pembayaran', 'Valid');
            });

            if ($startDate) {
                $totalPendapatanQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $totalPendapatanQuery->whereDate('created_at', '<=', $endDate);
            }

            $total_pendapatan = (float) $totalPendapatanQuery->sum('total_payar');

            // 2. Jumlah Tiket Terjual
            // Mengambil jumlah total tiket (jml_tiket) dari booking yang status pembayarannya 'Valid'
            $jumlahTiketQuery = Booking::whereHas('pembayaran', function ($query) {
                $query->where('status_pembayaran', 'Valid');
            });

            if ($startDate) {
                $jumlahTiketQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $jumlahTiketQuery->whereDate('created_at', '<=', $endDate);
            }

            $jumlah_tiket = (int) $jumlahTiketQuery->sum('jml_tiket');

            // 3. Jumlah Pengunjung
            // Setiap tiket melambangkan satu pengunjung, sehingga jumlah_pengunjung setara dengan jumlah_tiket terjual
            $jumlah_pengunjung = $jumlah_tiket;

            // 4. Rekap Pendapatan per Kategori Tiket (jenis_tiket)
            $kategoriQuery = Booking::whereHas('pembayaran', function ($query) {
                $query->where('status_pembayaran', 'Valid');
            });

            if ($startDate) {
                $kategoriQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $kategoriQuery->whereDate('created_at', '<=', $endDate);
            }

            $pendapatan_per_kategori = $kategoriQuery
                ->select('jenis_tiket')
                ->selectRaw('SUM(total_payar) as total_pendapatan')
                ->selectRaw('SUM(jml_tiket) as jumlah_tiket_terjual')
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
            $grafikQuery = Booking::whereHas('pembayaran', function ($query) {
                $query->where('status_pembayaran', 'Valid');
            });

            if ($startDate) {
                $grafikQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $grafikQuery->whereDate('created_at', '<=', $endDate);
            }

            $grafik_kunjungan = $grafikQuery
                ->join('tbl_slot_pendakian', 'tbl_booking.id_slot', '=', 'tbl_slot_pendakian.id_slot')
                ->select('tbl_slot_pendakian.tanggal_pendakian')
                ->selectRaw('SUM(tbl_booking.jml_tiket) as jumlah_pengunjung')
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
            $total_booking = Booking::count();
            $total_pembayaran = Pembayaran::count();
            $total_pelanggan = Pelanggan::count();

            // Mengembalikan response JSON yang sukses
            return response()->json([
                'total_pendapatan' => $total_pendapatan,
                'jumlah_tiket' => $jumlah_tiket,
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
