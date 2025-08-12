<?php

namespace App\Models;

use App\Events\NewAlarm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;

    protected $fillable = [
        'imei',
        'time',
        'vehicle_id',
        'no_pol',
        'ignition',
        'charging',
        'alarmType',
        'gpsTracking',
        'relayState',
        'voltageLevel',
        'gsmSigStrength',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($information) {
    //         if ($information->alarmType === 'normal') {
    //             event(new NewAlarm($information));
    //         }
    //     });
    // }
}
