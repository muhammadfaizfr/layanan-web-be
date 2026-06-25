<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'required|exists:tbl_pelanggan,id_pelanggan',
            'id_slot' => 'required|exists:tbl_slot_pendakian,id_slot',
            'jenis_tiket' => 'required|string|max:50',
            'jml_tiket' => 'required|integer|min:1',
            'total_payar' => 'required|numeric|min:0',
            'status_booking' => 'nullable|in:Menunggu Pembayaran,Diproses,Dikonfirmasi,Selesai,Batal',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi.',
            'id_pelanggan.exists' => 'Pelanggan tidak ditemukan.',
            'id_slot.required' => 'ID Slot wajib diisi.',
            'id_slot.exists' => 'Slot pendakian tidak ditemukan.',
            'jenis_tiket.required' => 'Jenis tiket wajib diisi.',
            'jml_tiket.required' => 'Jumlah tiket wajib diisi.',
            'jml_tiket.integer' => 'Jumlah tiket harus berupa angka.',
            'jml_tiket.min' => 'Jumlah tiket minimal 1.',
            'total_payar.required' => 'Total bayar wajib diisi.',
            'total_payar.numeric' => 'Total bayar harus berupa nilai desimal/angka.',
            'status_booking.in' => 'Status booking yang dimasukkan tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::create($request->only([
            'id_pelanggan',
            'id_slot',
            'jenis_tiket',
            'jml_tiket',
            'total_payar',
            'status_booking'
        ]));

        return response()->json([
            'message' => 'Booking berhasil ditambahkan',
            'data' => $booking
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
            'id_slot' => 'sometimes|required|exists:tbl_slot_pendakian,id_slot',
            'jenis_tiket' => 'sometimes|required|string|max:50',
            'jml_tiket' => 'sometimes|required|integer|min:1',
            'total_payar' => 'sometimes|required|numeric|min:0',
            'status_booking' => 'sometimes|required|in:Menunggu Pembayaran,Diproses,Dikonfirmasi,Selesai,Batal',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi.',
            'id_pelanggan.exists' => 'Pelanggan tidak ditemukan.',
            'id_slot.required' => 'ID Slot wajib diisi.',
            'id_slot.exists' => 'Slot pendakian tidak ditemukan.',
            'jenis_tiket.required' => 'Jenis tiket wajib diisi.',
            'jml_tiket.required' => 'Jumlah tiket wajib diisi.',
            'jml_tiket.integer' => 'Jumlah tiket harus berupa angka.',
            'jml_tiket.min' => 'Jumlah tiket minimal 1.',
            'total_payar.required' => 'Total bayar wajib diisi.',
            'total_payar.numeric' => 'Total bayar harus berupa nilai desimal/angka.',
            'status_booking.required' => 'Status booking wajib diisi.',
            'status_booking.in' => 'Status booking yang dimasukkan tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking->update($request->only([
            'id_pelanggan',
            'id_slot',
            'jenis_tiket',
            'jml_tiket',
            'total_payar',
            'status_booking'
        ]));

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

        $booking->delete();

        return response()->json([
            'message' => 'Booking berhasil dihapus'
        ], 200);
    }
}
