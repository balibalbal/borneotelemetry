<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Device;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = ['name', 'address',  'phone', 'email', 'status'];


    protected $hidden = [
        
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'customer_id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
