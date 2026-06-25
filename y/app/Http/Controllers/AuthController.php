<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Menangani proses login admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Validasi Input Request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Jika validasi gagal, kembalikan response error dengan status code 422
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Mencari data Admin berdasarkan email
        $admin = Admin::where('email', $request->email)->first();

        // 3. Jika email tidak ditemukan, kembalikan response error 404
        if (!$admin) {
            return response()->json([
                'message' => 'Email tidak ditemukan.'
            ], 404);
        }

        // 4. Mengecek kecocokan password menggunakan Hash::check()
        // Menggunakan kolom kata_sandi (sesuai getAuthPassword() di Model Admin)
        if (!Hash::check($request->password, $admin->kata_sandi)) {
            return response()->json([
                'message' => 'Password salah.'
            ], 401);
        }

        // 5. Membuat token Laravel Sanctum
        // Method createToken() didapatkan dari trait HasApiTokens pada Model Admin
        $token = $admin->createToken('auth_token')->plainTextToken;

        // 6. Mengembalikan response JSON sukses dengan status code 200
        return response()->json([
            'message' => 'Login berhasil',
            'admin' => [
                'id_admin' => $admin->id_admin,
                'nama_admin' => $admin->nama_admin,
                'email' => $admin->email
            ],
            'token' => $token
        ], 200);
    }

    /**
     * Menangani proses logout admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Menghapus token aktif yang digunakan saat request ini
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }
}
