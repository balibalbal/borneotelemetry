<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;

    protected $fillable = [
        'histories_id', 'no_pol', 'acc',
        'latitude', 'longitude', 'off', 'on', 'duration', 'address', 'vehicle_id', 'status','info'
    ];
}
