<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UiController extends Controller
{
    /**
     * Upload gambar UI dan simpan sebagai .jpg (tanpa peduli format asli).
     * Nama file selalu: {key}.jpg agar URL di frontend tetap konsisten.
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'  => 'required|string|max:100',
            'file' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $key  = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('key'));
        $file = $request->file('file');

        // Pastikan folder ui ada
        if (!Storage::disk('public')->exists('ui')) {
            Storage::disk('public')->makeDirectory('ui');
        }

        // Hapus SEMUA file lama dengan berbagai ekstensi
        foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $oldExt) {
            $oldPath = "ui/{$key}.{$oldExt}";
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
                Log::info("UI Upload: Deleted old file: {$oldPath}");
            }
        }

        // Selalu simpan sebagai .jpg agar URL frontend konsisten
        $filename = $key . '.jpg';
        
        // Baca konten file, lalu simpan ulang ke disk public
        $fileContent = file_get_contents($file->getRealPath());
        Storage::disk('public')->put("ui/{$filename}", $fileContent);

        $publicUrl = url('storage/ui/' . $filename);

        Log::info("UI Upload: Saved {$filename} to public storage. URL: {$publicUrl}");

        return response()->json([
            'message' => 'Gambar UI berhasil diperbarui',
            'data'    => [
                'key'      => $key,
                'filename' => $filename,
                'url'      => $publicUrl,
            ]
        ], 200);
    }

    /**
     * Get semua gambar UI yang sudah diupload.
     * Return: { key: url, ... }
     */
    public function index()
    {
        $files  = Storage::disk('public')->files('ui');
        $result = [];

        foreach ($files as $file) {
            $basename = basename($file);
            $key      = pathinfo($basename, PATHINFO_FILENAME);
            $result[$key] = url('storage/' . $file);
        }

        return response()->json([
            'message' => 'Daftar gambar UI',
            'data'    => $result
        ], 200);
    }
}
