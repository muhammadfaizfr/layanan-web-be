<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KontakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kontak = Kontak::all();

        return response()->json([
            'message' => 'Daftar pesan kontak berhasil diambil',
            'data' => $kontak
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'subjek' => 'nullable|string|max:100',
            'pesan' => 'required|string',
            'tanggal_kirim' => 'nullable|date_format:Y-m-d H:i:s',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'pesan.required' => 'Pesan wajib diisi.',
            'tanggal_kirim.date_format' => 'Format tanggal kirim salah (Y-m-d H:i:s).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Jika tanggal_kirim tidak disediakan, kita bisa mengisinya dengan null agar database menggunakan default CURRENT_TIMESTAMP
        // atau kita set manual menggunakan now().
        $data = $request->only(['nama', 'email', 'subjek', 'pesan']);
        if ($request->has('tanggal_kirim')) {
            $data['tanggal_kirim'] = $request->tanggal_kirim;
        }

        $kontak = Kontak::create($data);

        return response()->json([
            'message' => 'Pesan kontak berhasil dikirim',
            'data' => $kontak
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kontak = Kontak::find($id);

        if (!$kontak) {
            return response()->json([
                'message' => 'Pesan kontak tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pesan kontak berhasil diambil',
            'data' => $kontak
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kontak = Kontak::find($id);

        if (!$kontak) {
            return response()->json([
                'message' => 'Pesan kontak tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:100',
            'subjek' => 'sometimes|nullable|string|max:100',
            'pesan' => 'sometimes|required|string',
            'tanggal_kirim' => 'nullable|date_format:Y-m-d H:i:s',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'pesan.required' => 'Pesan wajib diisi.',
            'tanggal_kirim.date_format' => 'Format tanggal kirim salah (Y-m-d H:i:s).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kontak->update($request->only(['nama', 'email', 'subjek', 'pesan', 'tanggal_kirim']));

        return response()->json([
            'message' => 'Pesan kontak berhasil diperbarui',
            'data' => $kontak
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kontak = Kontak::find($id);

        if (!$kontak) {
            return response()->json([
                'message' => 'Pesan kontak tidak ditemukan'
            ], 404);
        }

        $kontak->delete();

        return response()->json([
            'message' => 'Pesan kontak berhasil dihapus'
        ], 200);
    }
}
