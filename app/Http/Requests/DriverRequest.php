<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            // 'divisi' => 'required',
            // 'sim_type' => 'required',
            'status' => 'required',
            // 'driver_position' => 'required',
            // 'sim_number' => 'required|max:14',  
            'no_certificate' => 'max:50',  
            // 'nama_pt' => 'max:50',  
            'phone' => 'max:14',  
            // 'start_date' => 'required|date', 
            'photo_sim' => 'image|file|max:1024',       
            'photo_ktp' => 'image|file|max:1024',       
            'photo_driver' => 'image|file|max:1024',       
            'photo_certificate_driver' => 'image|file|max:1024'       
        ];
    }
}
