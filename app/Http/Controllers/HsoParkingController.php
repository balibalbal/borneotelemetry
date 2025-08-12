<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HsoParkingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Parking::orderBy('id', 'desc')->limit(500)->get();

        return view('pages.hso_monitoring.list_parking')->with([
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Parking::findOrFail($id);

        return view('pages.hso_monitoring.view_parking')->with([
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Parking::findOrFail($id);

        return view('pages.hso_monitoring.edit_parking')->with([
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi status yang ingin diperbarui
        $request->validate([
            'status' => 'required|integer', 
        ]);

        $item = Parking::findOrFail($id);
        $item->status = $request->input('status'); // Hanya mengupdate status
        $item->save(); // Simpan perubahan

        session()->flash('pesan', 'ID ' . $id . ' berhasil diupdate');
        return redirect()->route('hso_parking.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Temukan dan hapus customer berdasarkan ID
        $hso = Parking::findOrFail($id);
        $hso->delete();

        // Setelah selesai, kembalikan ke halaman index
        session()->flash('pesan', 'ID ' . $id . ' berhasil dihapus');
        return redirect()->route('hso_parking.index');
    }

    public function showLogSizeTracking()
    {
        $logFilePath = storage_path('logs/hso.log');

        // Cek apakah file log ada
        if (File::exists($logFilePath)) {
            // Dapatkan ukuran file dalam byte
            $logSize = File::size($logFilePath);
            // Ubah ukuran ke KB
            $logSizeKB = round($logSize / 1024, 2); // KB
        } else {
            $logSizeKB = 0; // Jika tidak ada file
        }

        $logFilePath2 = storage_path('logs/laravel.log');

        // Cek apakah file log ada
        if (File::exists($logFilePath2)) {
            // Dapatkan ukuran file dalam byte
            $logSize2 = File::size($logFilePath2);
            // Ubah ukuran ke KB
            $logSizeKB2 = round($logSize2 / 1024, 2); // KB
        } else {
            $logSizeKB2 = 0; // Jika tidak ada file
        }
        
        $logFilePath3 = storage_path('logs/gpslogin.log');

        // Cek apakah file log ada
        if (File::exists($logFilePath3)) {
            // Dapatkan ukuran file dalam byte
            $logSize3 = File::size($logFilePath3);
            // Ubah ukuran ke KB
            $logSizeKB3 = round($logSize3 / 1024, 2); // KB
        } else {
            $logSizeKB3 = 0; // Jika tidak ada file
        }

        // ----------------- menampilkan isi file log -------------------

        // Path ke file log
        $logFile = storage_path('logs/hso.log');

        // Cek apakah file log ada
        if (file_exists($logFile)) {
            // Membaca file log
            $logs = file($logFile);

            // Mengambil 20 baris terakhir
            $latestLogs = array_slice($logs, -20);
        } else {
            // Jika file tidak ada, atur logs menjadi array kosong
            $latestLogs = [];
        }


        // Path ke file log sistem laravel
        $logFileGpsLogin = storage_path('logs/gpslogin.log');

        // Cek apakah file log ada
        if (file_exists($logFileGpsLogin)) {
            // Membaca file log
            $logGpsLogins = file($logFileGpsLogin);

            // Mengambil 20 baris terakhir
            $latestLogGpsLogins = array_slice($logGpsLogins, -100);
        } else {
            // Jika file tidak ada, atur logs menjadi array kosong
            $latestLogGpsLogins = [];
        }

        // Path ke file log sistem laravel
        $logFileLaravel = storage_path('logs/laravel.log');

        // Cek apakah file log ada
        if (file_exists($logFileLaravel)) {
            // Membaca file log
            $logLavs = file($logFileLaravel);

            // Mengambil 20 baris terakhir
            $latestLogLaravels = array_slice($logLavs, -100);
        } else {
            // Jika file tidak ada, atur logs menjadi array kosong
            $latestLogLaravels = [];
        }

        //return view('your-view-name', compact('logSizeKB'));
        return view('pages.hso_monitoring.list_last_position')->with([
            'logSizeKB' => $logSizeKB,
            'logSizeKB2' => $logSizeKB2,
            'logSizeKB3' => $logSizeKB3,
            'logs' => $latestLogs,
            'logLaravels' => $latestLogLaravels,
            'logGpsLogins' => $latestLogGpsLogins,
        ]);
    }

    public function deleteLogSistem()
    {
        $logFilePath = storage_path('logs/laravel.log');

        // Cek apakah file log ada
        if (File::exists($logFilePath)) {
            File::delete($logFilePath);
            session()->flash('pesan', 'Berhasil dihapus');
        } else {
            session()->flash('pesan', 'File log tidak ditemukan');
        }

        return redirect()->route('hso.last-position');
    }

    public function deleteLogGpsLogin()
    {
        $logFilePath = storage_path('logs/gpslogin.log');

        // Cek apakah file log ada
        if (File::exists($logFilePath)) {
            File::delete($logFilePath);
            session()->flash('pesan', 'Berhasil dihapus');
        } else {
            session()->flash('pesan', 'File log tidak ditemukan');
        }

        return redirect()->route('hso.last-position');
    }
    public function deleteLogHso()
    {
        $logFilePath = storage_path('logs/hso.log');

        // Cek apakah file log ada
        if (File::exists($logFilePath)) {
            File::delete($logFilePath);
            session()->flash('pesan', 'Berhasil dihapus');
        } else {
            session()->flash('pesan', 'File log tidak ditemukan');
        }

        return redirect()->route('hso.last-position');
    }
}
