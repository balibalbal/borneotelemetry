<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriesGeofence extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id', 'geofence_id', 'vehicle_id', 
        'geofence_name', 'no_pol', 'enter_time', 'exit_time', 
        'acc', 'status_geofence', 'status_kirim', 'note'
    ];
}
