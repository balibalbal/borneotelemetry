<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApiLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function login(Request $request)
    {
        $rule = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'login gagal',
                'data' => $validator->errors()
            ], 401);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $tokenUser = User::where('email', $request->email)->first();

        $dataUser = DB::table('users')
            ->where('users.email', $request->email)
            ->select(
                'users.*'
            )
            ->first();


        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'token' => $tokenUser->createToken('api-login')->plainTextToken,
            'data' => $dataUser,
            'code' => 200
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete(); 
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
            'code' => 200
        ]);
    }
    
}
