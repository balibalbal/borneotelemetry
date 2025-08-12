<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;

class Driver extends Model
{
    use HasFactory;
    use SoftDeletes;

    //untuk filter data apa saja yang bisa insert secara langsung
    
    protected $fillable = ['name', 
        'divisi', 
        'driver_code',  
        'start_date', 
        'contract_end_date', 
        'sim_number', 
        'sim_type', 
        'expired_sim', 
        'address', 
        'phone', 
        'driver_position',
        'debt',
        'note',
        'rekening_number',
        'rekening_name', 
        'photo_sim', 
        'photo_ktp', 
        'photo_driver', 
        'photo_certificate_driver', 
        'no_certificate', 
        'masa_berlaku_certificate', 
        'nama_pt', 
        'status'
    ];


    protected $hidden = [
        
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }


    public function traccar()
    {
        return $this->belongsTo(Traccar::class);
    }

}
