<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsoGeofence extends Model
{
    use HasFactory;

    //protected $table = 'hso_geofence'; // Nama tabel

    protected $fillable = [
        'idpoi', 'transporter', 'name', 'TrackingDate', 'FenceCode', 'Acc', 'EnterDateTimeArea', 'OutDateTimeArea', 'info'
    ];

    // protected $dates = [
    //     'TrackingDate',
    //     'EnterDateTimeArea',
    //     'OutDateTimeArea',
    // ];
}
