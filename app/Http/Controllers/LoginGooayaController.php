<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginGooayaController extends Controller
{
    private function authenticateGooaya($username, $password)
    {
        // API GOOAYA
        return true;
    }

    public function logincustom(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $authResult = $this->authenticateGooaya($username, $password);
        if ($authResult) {
            $authAttemptResult = Auth::attempt(['username' => $username, 'password' => 'IF1n1P@ssw0rdUB4Y4#']);
            if (!$authAttemptResult) {
                session()->flash('dnonactive', 'Username dan Password Anda Tidak Tercatat dalam Sistem!');
                return redirect('/login');
            } else {
                return redirect('/');
            }
        } else {
            session()->flash('dnonactive', 'Username dan Password Anda Tidak Tercatat dalam Sistem!');
            return redirect('/login');
        }
    }
}
