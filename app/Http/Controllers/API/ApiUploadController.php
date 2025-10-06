<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $user = auth()->user();

            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads', $filename);
            $publicUrl = asset(Storage::url($path));

            // Contoh: Simpan ke DB jika kamu punya tabel uploads
            // Upload::create([
            //     'user_id' => $user->id,
            //     'order_number' => $request->order_number,
            //     'file_path' => $path,
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Upload berhasil',
                'order_number' => $request->order_number,
                'file_url' => $publicUrl,
                'user' => $user->name ?? $user->email,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
