<?php

namespace App\Http\Controllers;

use App\Models\KontenGaleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KontenGaleriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galeri = KontenGaleri::with('admin')->get();

        return response()->json([
            'message' => 'Daftar konten galeri berhasil diambil',
            'data' => $galeri
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_konten' => 'required|string|max:150',
            'file' => 'required|string|max:255',
            'id_admin' => 'required|exists:tbl_admin,id_admin',
        ], [
            'judul_konten.required' => 'Judul konten wajib diisi.',
            'file.required' => 'File path/name wajib diisi.',
            'id_admin.required' => 'ID Admin wajib diisi.',
            'id_admin.exists' => 'Admin tidak terdaftar dalam sistem.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $galeri = KontenGaleri::create($request->only([
            'judul_konten',
            'file',
            'id_admin'
        ]));

        return response()->json([
            'message' => 'Konten galeri berhasil ditambahkan',
            'data' => $galeri
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $galeri = KontenGaleri::with('admin')->find($id);

        if (!$galeri) {
            return response()->json([
                'message' => 'Konten galeri tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail konten galeri berhasil diambil',
            'data' => $galeri
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $galeri = KontenGaleri::find($id);

        if (!$galeri) {
            return response()->json([
                'message' => 'Konten galeri tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul_konten' => 'sometimes|required|string|max:150',
            'file' => 'sometimes|required|string|max:255',
            'id_admin' => 'sometimes|required|exists:tbl_admin,id_admin',
        ], [
            'judul_konten.required' => 'Judul konten wajib diisi.',
            'file.required' => 'File path/name wajib diisi.',
            'id_admin.required' => 'ID Admin wajib diisi.',
            'id_admin.exists' => 'Admin tidak terdaftar dalam sistem.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $galeri->update($request->only([
            'judul_konten',
            'file',
            'id_admin'
        ]));

        return response()->json([
            'message' => 'Konten galeri berhasil diperbarui',
            'data' => $galeri
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $galeri = KontenGaleri::find($id);

        if (!$galeri) {
            return response()->json([
                'message' => 'Konten galeri tidak ditemukan'
            ], 404);
        }

        $galeri->delete();

        return response()->json([
            'message' => 'Konten galeri berhasil dihapus'
        ], 200);
    }
}
