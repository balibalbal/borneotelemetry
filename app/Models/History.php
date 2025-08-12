<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    use SoftDeletes;

    //untuk filter data apa saja yang bisa insert
    
    protected $fillable = [
        'id', 
        'device_id',
        'vehicle_id',
        'no_pol',
        'latitude','longitude','speed','time','course',
        'protocol',
        'distance',
        'engine_status',
        'total_distance', 
        'address', 
        'status',
        'vendor_gps'
    ];
}
