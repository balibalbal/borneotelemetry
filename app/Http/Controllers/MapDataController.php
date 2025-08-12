<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapDataController extends Controller
{
    public function index()
    {
        return view('map'); // jika file = resources/views/map.blade.php
    }
    public function speeding()
    {
        $data = DB::select("
            WITH pergerakan AS (
                SELECT *,
                    LEAD(time) OVER (PARTITION BY vehicle_id ORDER BY time) AS next_time,
                    LEAD(geom) OVER (PARTITION BY vehicle_id ORDER BY time) AS next_geom
                FROM histories
            )
            SELECT 
                vehicle_id, 
                time, 
                ST_AsGeoJSON(geom)::json AS geometry,
                CASE
                    WHEN EXTRACT(EPOCH FROM (next_time - time)) > 0 THEN 
                        ST_Distance(geom::geography, next_geom::geography) /
                        EXTRACT(EPOCH FROM (next_time - time)) * 3.6
                    ELSE NULL
                END AS speed_kmh
            FROM pergerakan
            WHERE next_time IS NOT NULL
            AND EXTRACT(EPOCH FROM (next_time - time)) > 0
            AND ST_Distance(geom::geography, next_geom::geography) / 
                NULLIF(EXTRACT(EPOCH FROM (next_time - time)), 0) * 3.6 > 60
        ");

        $features = array_map(function($row) {
            return [
                'type' => 'Feature',
                'geometry' => json_decode($row->geometry),
                'properties' => [
                    'vehicle_id' => $row->vehicle_id,
                    'time' => $row->time,
                    'speed_kmh' => round($row->speed_kmh, 2)
                ]
            ];
        }, $data);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    public function stops()
    {
        $data = DB::select("
            WITH posisi AS (
                SELECT *,
                    LAG(time) OVER (PARTITION BY vehicle_id ORDER BY time) AS prev_time,
                    LAG(geom) OVER (PARTITION BY vehicle_id ORDER BY time) AS prev_geom
                FROM histories
            ),
            berhenti AS (
                SELECT *,
                    ST_Distance(geom::geography, prev_geom::geography) AS jarak_meter,
                    EXTRACT(EPOCH FROM (time - prev_time)) AS durasi_detik
                FROM posisi
            )
            SELECT vehicle_id, time, ST_AsGeoJSON(geom)::json AS geometry, durasi_detik
            FROM berhenti
            WHERE durasi_detik > 300 AND jarak_meter < 10
        ");

        $features = array_map(fn($row) => [
            'type' => 'Feature',
            'geometry' => json_decode($row->geometry),
            'properties' => [
                'vehicle_id' => $row->vehicle_id,
                'time' => $row->time,
                'durasi_detik' => round($row->durasi_detik)
            ]
        ], $data);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    public function jams()
    {
        $data = DB::select("
            WITH pergerakan AS (
                SELECT *,
                    LEAD(time) OVER (PARTITION BY vehicle_id ORDER BY time) AS next_time,
                    LEAD(geom) OVER (PARTITION BY vehicle_id ORDER BY time) AS next_geom
                FROM histories
            )
            SELECT 
                vehicle_id, 
                time, 
                ST_AsGeoJSON(geom)::json AS geometry,
                CASE 
                    WHEN EXTRACT(EPOCH FROM (next_time - time)) > 0 THEN 
                        ST_Distance(geom::geography, next_geom::geography) /
                        NULLIF(EXTRACT(EPOCH FROM (next_time - time)), 0) * 3.6
                    ELSE NULL
                END AS speed_kmh
            FROM pergerakan
            WHERE 
                next_time IS NOT NULL
                AND EXTRACT(EPOCH FROM (next_time - time)) > 0
                AND (
                    ST_Distance(geom::geography, next_geom::geography) /
                    NULLIF(EXTRACT(EPOCH FROM (next_time - time)), 0) * 3.6
                ) < 5

        ");

        $features = array_map(fn($row) => [
            'type' => 'Feature',
            'geometry' => json_decode($row->geometry),
            'properties' => [
                'vehicle_id' => $row->vehicle_id,
                'time' => $row->time,
                'speed_kmh' => round($row->speed_kmh, 2)
            ]
        ], $data);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }
    

}
