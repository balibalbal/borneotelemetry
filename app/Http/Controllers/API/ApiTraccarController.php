<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traccar;
use App\Models\Customer;

class ApiTraccarController extends Controller
{
    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id');
        
        // Validasi apakah customer_id di input di endpoint
        if (is_null($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => 'customer_id kosong, silahkan isi dulu',
                'code' => 400
            ], 400);
        }

        // Cek apakah customer_id ada di tabel customers
        $customerExists = Customer::where('id', $customer_id)->exists();
        
        if (!$customerExists) {
            return response()->json([
                'status' => false,
                'message' => 'customer_id tidak ditemukan',
                'code' => 404
            ], 404);
        }

        if ($customer_id == 1) {
            // Hitung jumlah kendaraan berdasarkan status
            $totalMati = Traccar::where('status', 'mati')
                    //->where('customer_id', $customer_id)
                    ->count();

            $totalBergerak = Traccar::where('status', 'bergerak')
                    //->where('customer_id', $customer_id)
                    ->count();

            $totalBerhenti = Traccar::where('status', 'berhenti')
                    //->where('customer_id', $customer_id)
                    ->count();

            $totalDiam = Traccar::where('status', 'diam')
                    //->where('customer_id', $customer_id)
                    ->count();

            $totalKendaraan = Traccar::whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    //->where('customer_id', $customer_id)
                    ->count(); 

            // Ambil data dari tabel Traccar
            //$data = Traccar::where('active', 1)->get();
            $data = Traccar::where('traccars.active', 1)
                ->join('vehicles', 'vehicles.id', '=', 'traccars.vehicle_id')
                ->select('traccars.*', 'vehicles.vehicle_type') // Ambil semua data traccars + vehicle_type
                ->get();
            
        } else {
            // Hitung jumlah kendaraan berdasarkan status
            $totalMati = Traccar::where('status', 'mati')
                    ->where('customer_id', $customer_id)
                    ->count();

            $totalBergerak = Traccar::where('status', 'bergerak')
                    ->where('customer_id', $customer_id)
                    ->count();

            $totalBerhenti = Traccar::where('status', 'berhenti')
                    ->where('customer_id', $customer_id)
                    ->count();

            $totalDiam = Traccar::where('status', 'diam')
                    ->where('customer_id', $customer_id)
                    ->count();

            $totalKendaraan = Traccar::whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->where('customer_id', $customer_id)
                    ->count(); 

            // Ambil data dari tabel Traccar
            //$data = Traccar::where('customer_id', $customer_id)->get();
            $data = Traccar::where('traccars.customer_id', $customer_id)
                ->where('traccars.active', 1)
                ->join('vehicles', 'vehicles.id', '=', 'traccars.vehicle_id')
                ->select('traccars.*', 'vehicles.vehicle_type') // Ambil semua data traccars + vehicle_type
                ->get();

        }

        // Gabungkan data perhitungan dengan data Traccar
        $responseData = [
            'totalMati' => $totalMati,
            'totalBergerak' => $totalBergerak,
            'totalBerhenti' => $totalBerhenti,
            'totalDiam' => $totalDiam,
            'totalKendaraan' => $totalKendaraan,
            'traccar' => $data
        ];

        return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => $responseData,
            'code' => 200
        ], 200);
    }
}
