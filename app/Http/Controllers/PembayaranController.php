<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembayaran = Pembayaran::with('booking')->get();

        return response()->json([
            'message' => 'Daftar pembayaran berhasil diambil',
            'data' => $pembayaran
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_booking' => 'required|exists:tbl_booking,id_booking',
            'metode_pembayaran' => 'required|string|max:50',
            'bukti_transfer' => 'nullable|string|max:255',
            'waktu_pembayaran' => 'nullable|date_format:Y-m-d H:i:s',
            'status_pembayaran' => 'nullable|in:Menunggu Verifikasi,Valid,DitolaK',
        ], [
            'id_booking.required' => 'ID Booking wajib diisi.',
            'id_booking.exists' => 'Booking tidak ditemukan.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib diisi.',
            'waktu_pembayaran.date_format' => 'Format waktu pembayaran salah (Y-m-d H:i:s).',
            'status_pembayaran.in' => 'Status pembayaran tidak valid (Pilihan: Menunggu Verifikasi, Valid, DitolaK).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pembayaran = Pembayaran::create($request->only([
            'id_booking',
            'metode_pembayaran',
            'bukti_transfer',
            'waktu_pembayaran',
            'status_pembayaran'
        ]));

        return response()->json([
            'message' => 'Pembayaran berhasil ditambahkan',
            'data' => $pembayaran
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with('booking')->find($id);

        if (!$pembayaran) {
            return response()->json([
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pembayaran berhasil diambil',
            'data' => $pembayaran
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_booking' => 'sometimes|required|exists:tbl_booking,id_booking',
            'metode_pembayaran' => 'sometimes|required|string|max:50',
            'bukti_transfer' => 'nullable|string|max:255',
            'waktu_pembayaran' => 'nullable|date_format:Y-m-d H:i:s',
            'status_pembayaran' => 'sometimes|required|in:Menunggu Verifikasi,Valid,DitolaK',
        ], [
            'id_booking.required' => 'ID Booking wajib diisi.',
            'id_booking.exists' => 'Booking tidak ditemukan.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib diisi.',
            'waktu_pembayaran.date_format' => 'Format waktu pembayaran salah (Y-m-d H:i:s).',
            'status_pembayaran.required' => 'Status pembayaran wajib diisi.',
            'status_pembayaran.in' => 'Status pembayaran tidak valid (Pilihan: Menunggu Verifikasi, Valid, DitolaK).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pembayaran->update($request->only([
            'id_booking',
            'metode_pembayaran',
            'bukti_transfer',
            'waktu_pembayaran',
            'status_pembayaran'
        ]));

        return response()->json([
            'message' => 'Pembayaran berhasil diperbarui',
            'data' => $pembayaran
        ], 200);
    }

    /**
     * Upload bukti pembayaran.
     */
    public function uploadBukti(Request $request, $id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,webp|max:20480',
        ], [
            'bukti_transfer.required' => 'File bukti transfer wajib diisi.',
            'bukti_transfer.image' => 'File harus berupa gambar.',
            'bukti_transfer.mimes' => 'Format file yang didukung: jpeg, png, jpg, webp.',
            'bukti_transfer.max' => 'Ukuran file maksimal 20MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $path = $file->store('bukti_transfer', 'public');
            
            $pembayaran->bukti_transfer = '/storage/' . $path;
            $pembayaran->save();

            return response()->json([
                'message' => 'Bukti pembayaran berhasil diupload',
                'data' => $pembayaran
            ], 200);
        }

        return response()->json([
            'message' => 'File bukti transfer tidak ditemukan'
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        $pembayaran->delete();

        return response()->json([
            'message' => 'Pembayaran berhasil dihapus'
        ], 200);
    }
}
