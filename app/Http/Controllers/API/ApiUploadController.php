<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\UploadLog;

class ApiUploadController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg|max:5120',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string|max:255',
        ]);

        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $file = $request->file('image');
            $filename = $request->order_number . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads', $filename);
            $publicUrl = asset(Storage::url($path));

            // ✅ Simpan ke database jika ada tabel upload_logs
            UploadLog::create([
                'user_id' => $user->id,
                'order_number' => $request->order_number,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'file_url' => $publicUrl,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Upload berhasil',
                'order_number' => $request->order_number,
                'file_url' => $publicUrl,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'user' => $user->name ?? $user->email,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

}
