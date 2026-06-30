<?php

namespace App\Http\Controllers;

use App\Models\SlotPendakian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SlotPendakianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slots = SlotPendakian::with('admin')->get();

        return response()->json([
            'message' => 'Daftar slot pendakian berhasil diambil',
            'data' => $slots
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pendakian' => 'required|date',
            'kuota_maksimal' => 'required|integer|min:1',
            'kuota_tersedia' => 'required|integer|min:0|lte:kuota_maksimal',
            'id_admin' => 'required|exists:tbl_admin,id_admin',
        ], [
            'tanggal_pendakian.required' => 'Tanggal pendakian wajib diisi.',
            'tanggal_pendakian.date' => 'Format tanggal pendakian tidak valid.',
            'kuota_maksimal.required' => 'Kuota maksimal wajib diisi.',
            'kuota_maksimal.integer' => 'Kuota maksimal harus berupa angka.',
            'kuota_maksimal.min' => 'Kuota maksimal minimal bernilai 1.',
            'kuota_tersedia.required' => 'Kuota tersedia wajib diisi.',
            'kuota_tersedia.integer' => 'Kuota tersedia harus berupa angka.',
            'kuota_tersedia.min' => 'Kuota tersedia tidak boleh bernilai negatif.',
            'kuota_tersedia.lte' => 'Kuota tersedia tidak boleh melebihi kuota maksimal.',
            'id_admin.required' => 'ID Admin wajib diisi.',
            'id_admin.exists' => 'Admin tidak terdaftar dalam sistem.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $slot = SlotPendakian::create($request->only([
            'tanggal_pendakian',
            'kuota_maksimal',
            'kuota_tersedia',
            'id_admin'
        ]));

        return response()->json([
            'message' => 'Slot pendakian berhasil ditambahkan',
            'data' => $slot
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $slot = SlotPendakian::with('admin')->find($id);

        if (!$slot) {
            return response()->json([
                'message' => 'Slot pendakian tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail slot pendakian berhasil diambil',
            'data' => $slot
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $slot = SlotPendakian::find($id);

        if (!$slot) {
            return response()->json([
                'message' => 'Slot pendakian tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_pendakian' => 'sometimes|required|date',
            'kuota_maksimal' => 'sometimes|required|integer|min:1',
            'kuota_tersedia' => 'sometimes|required|integer|min:0|lte:kuota_maksimal',
            'id_admin' => 'sometimes|required|exists:tbl_admin,id_admin',
        ], [
            'tanggal_pendakian.required' => 'Tanggal pendakian wajib diisi.',
            'tanggal_pendakian.date' => 'Format tanggal pendakian tidak valid.',
            'kuota_maksimal.required' => 'Kuota maksimal wajib diisi.',
            'kuota_maksimal.integer' => 'Kuota maksimal harus berupa angka.',
            'kuota_maksimal.min' => 'Kuota maksimal minimal bernilai 1.',
            'kuota_tersedia.required' => 'Kuota tersedia wajib diisi.',
            'kuota_tersedia.integer' => 'Kuota tersedia harus berupa angka.',
            'kuota_tersedia.min' => 'Kuota tersedia tidak boleh bernilai negatif.',
            'kuota_tersedia.lte' => 'Kuota tersedia tidak boleh melebihi kuota maksimal.',
            'id_admin.required' => 'ID Admin wajib diisi.',
            'id_admin.exists' => 'Admin tidak terdaftar dalam sistem.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $slot->update($request->only([
            'tanggal_pendakian',
            'kuota_maksimal',
            'kuota_tersedia',
            'id_admin'
        ]));

        return response()->json([
            'message' => 'Slot pendakian berhasil diperbarui',
            'data' => $slot
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $slot = SlotPendakian::find($id);

        if (!$slot) {
            return response()->json([
                'message' => 'Slot pendakian tidak ditemukan'
            ], 404);
        }

        $slot->delete();

        return response()->json([
            'message' => 'Slot pendakian berhasil dihapus'
        ], 200);
    }
}
