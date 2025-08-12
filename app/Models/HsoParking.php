<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsoParking extends Model
{
    use HasFactory;

    protected $fillable = [
        'histories_id', 'customer_id', 'no_pol', 'acc',
        'latitude', 'longitude', 'off', 'on', 'duration', 'address', 'vehicle_id', 'status','info'
    ];
}
