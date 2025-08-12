<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $rules = [
            'name' => 'required|max:100',
            'password' => 'required|min:6|max:50',
            // 'customer_id' => 'required',
            'user_type' => 'required',
            'status' => 'required'
        ];

        // Ambil id pengguna dari URL
        $userId = $this->route('user');

        // Tambahkan aturan validasi unik untuk email dengan pengecualian saat update
        $rules['email'] = [
            'required',
            'email:dns',
            Rule::unique('users')->ignore($userId),
        ];

        return $rules;
    }
}
