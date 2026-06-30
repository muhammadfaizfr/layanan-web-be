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
            'file' => 'required|image|mimes:jpeg,png,jpg|max:10240', // Max 10MB
            'id_admin' => 'required|exists:tbl_admin,id_admin',
        ], [
            'judul_konten.required' => 'Judul konten wajib diisi.',
            'file.required' => 'File gambar wajib diunggah.',
            'file.image' => 'File harus berupa gambar.',
            'file.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'id_admin.required' => 'ID Admin wajib diisi.',
            'id_admin.exists' => 'Admin tidak terdaftar dalam sistem.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('galeri', 'public');
        }

        $galeri = KontenGaleri::create([
            'judul_konten' => $request->judul_konten,
            'file' => $filePath,
            'id_admin' => $request->id_admin,
        ]);

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
        $galeri = KontenGaleri::where('id_konten', $id)->first();

        if (!$galeri) {
            return response()->json([
                'message' => 'Konten galeri tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul_konten' => 'sometimes|required|string|max:150',
            'file' => 'sometimes|image|mimes:jpeg,png,jpg|max:10240',
            'id_admin' => 'sometimes|required|exists:tbl_admin,id_admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('judul_konten')) {
            $galeri->judul_konten = $request->judul_konten;
        }

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($galeri->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($galeri->file)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($galeri->file);
            }
            $galeri->file = $request->file('file')->store('galeri', 'public');
        }

        $galeri->save();

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
        $galeri = KontenGaleri::where('id_konten', $id)->first();

        if (!$galeri) {
            return response()->json([
                'message' => 'Konten galeri tidak ditemukan'
            ], 404);
        }

        // Hapus file dari storage
        if ($galeri->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($galeri->file)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($galeri->file);
        }

        $galeri->delete();

        return response()->json([
            'message' => 'Konten galeri berhasil dihapus'
        ], 200);
    }
}
