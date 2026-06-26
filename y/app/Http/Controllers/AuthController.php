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

    /**
     * Mengambil profil admin yang sedang login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json([
                'message' => 'Tidak terautentikasi.'
            ], 401);
        }

        return response()->json([
            'id_admin' => $admin->id_admin,
            'nama_admin' => $admin->nama_admin,
            'email' => $admin->email,
        ], 200);
    }

    /**
     * Memperbarui profil admin yang sedang login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json([
                'message' => 'Tidak terautentikasi.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'nama_admin' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:tbl_admin,email,' . $admin->id_admin . ',id_admin',
            'kata_sandi' => 'sometimes|string|min:6',
        ], [
            'nama_admin.string' => 'Nama admin harus berupa teks.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh admin lain.',
            'kata_sandi.min' => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('nama_admin')) {
            $admin->nama_admin = $request->nama_admin;
        }

        if ($request->has('email')) {
            $admin->email = $request->email;
        }

        if ($request->has('kata_sandi')) {
            $admin->kata_sandi = Hash::make($request->kata_sandi);
        }

        $admin->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'admin' => [
                'id_admin' => $admin->id_admin,
                'nama_admin' => $admin->nama_admin,
                'email' => $admin->email,
            ]
        ], 200);
    }
}
