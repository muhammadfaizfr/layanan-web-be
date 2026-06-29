<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengaturanController extends Controller
{
    /**
     * Get current settings (always single row id=1)
     */
    public function index()
    {
        $pengaturan = Pengaturan::first();
        if (!$pengaturan) {
            $pengaturan = Pengaturan::create([
                'harga_lokal' => 15000,
                'harga_mancanegara' => 35000,
                'harga_camping' => 25000,
                'jam_buka' => '07:00',
                'jam_tutup' => '17:00',
                'kuota_harian' => 2500,
                'kebijakan_pembatalan_aktif' => true,
                'teks_kebijakan' => 'Pengembalian dana (refund) dapat dilakukan maksimal 24 jam sebelum jadwal kunjungan. \n\nDana akan dikembalikan sebesar 80% dari total harga tiket setelah dipotong biaya administrasi platform sebesar Rp 2.500 per transaksi.\n\nKebijakan ini tidak berlaku untuk tiket promo atau event khusus tertentu.'
            ]);
        }
        
        return response()->json([
            'message' => 'Pengaturan berhasil diambil',
            'data' => $pengaturan
        ], 200);
    }

    /**
     * Update settings (id=1)
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'harga_lokal' => 'sometimes|required|integer|min:0',
            'harga_mancanegara' => 'sometimes|required|integer|min:0',
            'harga_camping' => 'sometimes|required|integer|min:0',
            'jam_buka' => 'sometimes|required|string',
            'jam_tutup' => 'sometimes|required|string',
            'kuota_harian' => 'sometimes|required|integer|min:0',
            'kebijakan_pembatalan_aktif' => 'sometimes|required|boolean',
            'teks_kebijakan' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pengaturan = Pengaturan::first();
        if (!$pengaturan) {
            $pengaturan = new Pengaturan();
        }

        if ($request->has('harga_lokal')) {
            $pengaturan->harga_lokal = $request->harga_lokal;
        }
        if ($request->has('harga_mancanegara')) {
            $pengaturan->harga_mancanegara = $request->harga_mancanegara;
        }
        if ($request->has('harga_camping')) {
            $pengaturan->harga_camping = $request->harga_camping;
        }
        if ($request->has('jam_buka')) {
            $pengaturan->jam_buka = $request->jam_buka;
        }
        if ($request->has('jam_tutup')) {
            $pengaturan->jam_tutup = $request->jam_tutup;
        }
        if ($request->has('kuota_harian')) {
            $pengaturan->kuota_harian = $request->kuota_harian;
        }
        if ($request->has('kebijakan_pembatalan_aktif')) {
            $pengaturan->kebijakan_pembatalan_aktif = filter_var($request->kebijakan_pembatalan_aktif, FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('teks_kebijakan')) {
            $pengaturan->teks_kebijakan = $request->teks_kebijakan;
        }

        $pengaturan->save();

        return response()->json([
            'message' => 'Pengaturan berhasil diperbarui',
            'data' => $pengaturan
        ], 200);
    }
}
