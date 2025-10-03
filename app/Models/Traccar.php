<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traccar extends Model
{
    use HasFactory;
    use SoftDeletes;

    //untuk filter data apa saja yang bisa insert
    
    protected $fillable = [
        'id', 
        'vehicle_id', 
        'no_pol',
        'latitude','longitude','speed','time','course',
        'protocol',
        'distance',
        'engine_status',
        'ignition_status',
        'total_distance', 
        'geofence', 
        'device_id', 
        'enter_time', 
        'out_time', 
        'address', 
        //'geom',
        'geo_point',
        'status',
        'vendor_gps'
    ];

    public static function total_kendaraan()
    {
        return self::count(); // Menghitung total kendaraan dalam model Traccar
    }

   

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'vehicle_id', 'id');
    }
    
  
    public function driver()
    {
        return $this->hasOne(Driver::class,);
    }
    
}


