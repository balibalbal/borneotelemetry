<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
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
            'no_pol' => 'required',
            // 'customer_id' => 'required',
            // 'driver_id' => 'required',
            // 'nama_pt' => 'required',
            // 'divisi' => 'required',
            'vehicle_type' => 'required',
            'tahun_kendaraan' => 'max:4',
            'warna' => 'max:30',
            'type' => 'max:30',
            'no_rangka' => 'max:30',
            'no_mesin' => 'max:30',
            'head_kir' => 'max:30',
            'chasis_kir' => 'max:30',
            'nama_pt_chasis_kir' => 'max:50',
            'jenis_chasis' => 'max:50',
            'nama_gps' => 'max:100',
            'nomor_chasis' => 'max:50',
            'model_chasis' => 'max:50',
            'divisi_chasis' => 'max:50',
            'no_rekom_b3_klhk' => 'max:50',
            'no_single_tid' => 'max:30',
            'photo_stnk' => 'mimes:jpeg,jpg,png,gif,pdf|max:1024',       
            'photo_head_kir' => 'mimes:jpeg,jpg,png,gif,pdf|max:1024',       
            'photo_chasis_kir' => 'mimes:jpeg,jpg,png,gif,pdf|max:1024',       
            'photo_b3_klhk' => 'mimes:jpeg,jpg,png,gif,pdf|max:1024',
            'photo_kartu_pengawasan_kemenhub' => 'mimes:jpeg,jpg,png,gif,pdf|max:1024',
        ];
    }
}
