<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['pelanggan', 'slotPendakian'])->get();

        return response()->json([
            'message' => 'Daftar booking berhasil diambil',
            'data' => $bookings
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * Auto-generate id_tiket (BOOK-0001, BOOK-0002, dst)
     * Auto-hitung total_harga = jumlah_tiket x harga_tiket
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'required|exists:tbl_pelanggan,id_pelanggan',
            'id_slot' => 'nullable|exists:tbl_slot_pendakian,id_slot',
            'jenis_tiket' => 'required|string|max:50',
            'jumlah_tiket' => 'required|integer|min:1',
            'harga_tiket' => 'required|numeric|min:0',
            'status_booking' => 'nullable|in:Menunggu Pembayaran,Diproses,Dikonfirmasi,Selesai,Batal',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi.',
            'id_pelanggan.exists' => 'Pelanggan tidak ditemukan.',
            'id_slot.exists' => 'Slot pendakian tidak ditemukan.',
            'jenis_tiket.required' => 'Jenis tiket wajib diisi.',
            'jumlah_tiket.required' => 'Jumlah tiket wajib diisi.',
            'jumlah_tiket.integer' => 'Jumlah tiket harus berupa angka.',
            'jumlah_tiket.min' => 'Jumlah tiket minimal 1.',
            'harga_tiket.required' => 'Harga tiket wajib diisi.',
            'harga_tiket.numeric' => 'Harga tiket harus berupa angka.',
            'status_booking.in' => 'Status booking yang dimasukkan tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Auto-generate ID Tiket: BOOK-0001, BOOK-0002, dst
        $lastBooking = Booking::select('id_tiket')
            ->whereNotNull('id_tiket')
            ->orderByDesc('id_booking')
            ->first();

        $nextNumber = 1;
        if ($lastBooking && $lastBooking->id_tiket) {
            // Ambil angka dari format BOOK-XXXX
            $lastNumber = (int) str_replace('BOOK-', '', $lastBooking->id_tiket);
            $nextNumber = $lastNumber + 1;
        }
        $idTiket = 'BOOK-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Auto-hitung total harga
        $jumlahTiket = (int) $request->jumlah_tiket;
        $hargaTiket = (float) $request->harga_tiket;
        $totalHarga = $jumlahTiket * $hargaTiket;

        $booking = Booking::create([
            'id_tiket' => $idTiket,
            'id_pelanggan' => $request->id_pelanggan,
            'id_slot' => $request->id_slot,
            'jenis_tiket' => $request->jenis_tiket,
            'jumlah_tiket' => $jumlahTiket,
            'harga_tiket' => $hargaTiket,
            'total_harga' => $totalHarga,
            // Backward compatibility: isi juga kolom lama
            'jml_tiket' => $jumlahTiket,
            'total_payar' => $totalHarga,
            'status_booking' => $request->status_booking ?? 'Menunggu Pembayaran',
        ]);

        return response()->json([
            'message' => 'Booking berhasil',
            'data' => [
                'id_booking' => $booking->id_booking,
                'id_tiket' => $booking->id_tiket,
                'id_pelanggan' => $booking->id_pelanggan,
                'jenis_tiket' => $booking->jenis_tiket,
                'jumlah_tiket' => $booking->jumlah_tiket,
                'harga_tiket' => $booking->harga_tiket,
                'total_harga' => $booking->total_harga,
                'status_booking' => $booking->status_booking,
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking = Booking::with(['pelanggan', 'slotPendakian', 'pembayaran'])->find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail booking berhasil diambil',
            'data' => $booking
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'sometimes|required|exists:tbl_pelanggan,id_pelanggan',
            'id_slot' => 'sometimes|nullable|exists:tbl_slot_pendakian,id_slot',
            'jenis_tiket' => 'sometimes|required|string|max:50',
            'jumlah_tiket' => 'sometimes|required|integer|min:1',
            'harga_tiket' => 'sometimes|required|numeric|min:0',
            'status_booking' => 'sometimes|required|in:Menunggu Pembayaran,Diproses,Dikonfirmasi,Selesai,Batal',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi.',
            'id_pelanggan.exists' => 'Pelanggan tidak ditemukan.',
            'id_slot.exists' => 'Slot pendakian tidak ditemukan.',
            'jenis_tiket.required' => 'Jenis tiket wajib diisi.',
            'jumlah_tiket.required' => 'Jumlah tiket wajib diisi.',
            'jumlah_tiket.integer' => 'Jumlah tiket harus berupa angka.',
            'jumlah_tiket.min' => 'Jumlah tiket minimal 1.',
            'harga_tiket.required' => 'Harga tiket wajib diisi.',
            'harga_tiket.numeric' => 'Harga tiket harus berupa angka.',
            'status_booking.required' => 'Status booking wajib diisi.',
            'status_booking.in' => 'Status booking yang dimasukkan tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'id_pelanggan',
            'id_slot',
            'jenis_tiket',
            'jumlah_tiket',
            'harga_tiket',
            'status_booking'
        ]);

        // Jika jumlah_tiket atau harga_tiket diubah, hitung ulang total_harga
        $jumlahTiket = $data['jumlah_tiket'] ?? $booking->jumlah_tiket ?? $booking->jml_tiket;
        $hargaTiket = $data['harga_tiket'] ?? $booking->harga_tiket;

        if (isset($data['jumlah_tiket']) || isset($data['harga_tiket'])) {
            $data['total_harga'] = (int) $jumlahTiket * (float) $hargaTiket;
            // Backward compatibility
            $data['jml_tiket'] = (int) $jumlahTiket;
            $data['total_payar'] = $data['total_harga'];
        }

        $booking->update($data);

        return response()->json([
            'message' => 'Booking berhasil diperbarui',
            'data' => $booking
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        $idPelanggan = $booking->id_pelanggan;
        $booking->delete();

        // Bersihkan data pelanggan jika sudah tidak memiliki pesanan
        if (\App\Models\Booking::where('id_pelanggan', $idPelanggan)->count() === 0) {
            \App\Models\Pelanggan::where('id_pelanggan', $idPelanggan)->delete();
        }

        return response()->json([
            'message' => 'Booking berhasil dihapus'
        ], 200);
    }
}
