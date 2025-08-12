<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.login.index');
    }

    
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials)) {
            $user = Auth::user();
            if($user->status == 1 && $user->akses != 2) { // Memeriksa apakah status pengguna adalah 1 dan akses tidak sama dengan 2
                $request->session()->regenerate();
                return redirect()->intended('/');
            } else {
                Auth::logout(); // Logout pengguna jika status tidak aktif atau akses sama dengan 2
                return back()->with('pesan', 'Akun Anda tidak aktif atau tidak memiliki akses web. Silahkan hubungi admin');
            }
        }

        // Jika autentikasi gagal
        return back()->with('pesan', 'Login gagal! Silahkan coba lagi');
    }



    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
    
    // ini bagian khusus reset sandi atau password (ambali - 18-04-2024)
    public function forgotPassword() {
        return view('pages.auth.forgot-password');
    }

    public function showResetForm($token)
    {
        return view('pages.auth.reset-password', ['token' => $token]);
    }

    public function kirimLinkUntukReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
 
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function resetSandi(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);
     
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );
     
        return $status === Password::PASSWORD_RESET
                    ? redirect('/login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
    // ini batas akhir bagian khusus reset sandi atau password (ambali - 18-04-2024)
}
