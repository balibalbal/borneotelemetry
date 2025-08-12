<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsoGeofenceEnter extends Model
{
    use HasFactory;

    protected $fillable = [
        'idpoi', 'transporter', 'name', 'TrackingDate', 'FenceCode', 'Acc', 'EnterDateTimeArea', 'OutDateTimeArea', 'info'
    ];
}
