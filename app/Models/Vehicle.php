<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;
        
    protected $fillable = [
        'id',
        'driver_id',
        'group_id',
        'no_pol',
        'nama_pt','address',
        'vehicle_type', 'divisi',
        'tahun_kendaraan','warna','type',
        'no_rangka','no_mesin',
        'expired_stnk','expired_pajak',
        'head_kir','expired_head_kir',
        'chasis_kir','expired_chasis_kir','nama_pt_chasis_kir',
        'jenis_chasis','nomor_chasis','model_chasis','divisi_chasis',
        'no_rekom_b3_klhk','expired_rekom',
        'expired_kartu_kemenhub','no_single_tid',
        'expired_single_tid',
        'nama_gps','keterangan',
        'photo_stnk', 'photo_head_kir', 'photo_chasis_kir',
        'photo_b3_klhk', 'photo_kartu_pengawasan_kemenhub',
        'latitude', 'longitude','vendor_gps', 'status'
    ];

    // public function area()
    // {
    //     return $this->belongsTo(Area::class);
    // }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function device()
    {
        return $this->hasOne(Device::class);
    }

    public function traccar()
    {
        return $this->belongsTo(Traccar::class, 'device_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
