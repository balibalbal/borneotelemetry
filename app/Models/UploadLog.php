<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'latitude',
        'longitude',
        'address',
        'file_url',
    ];
}
