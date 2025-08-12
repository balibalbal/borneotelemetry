<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsoLastPosition extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'asset_ID',
        'asset_code',
        'latitude',
        'longitude',
        'timestamp',
        'address',
        'info'
    ];
}
