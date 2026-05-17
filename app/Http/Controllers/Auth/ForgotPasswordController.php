<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini bertanggung jawab untuk menangani email penyetelan ulang
    | kata sandi dan menyertakan trait yang membantu dalam mengirimkan
    | notifikasi ini dari aplikasi Anda kepada pengguna Anda.
    |
    */

    use SendsPasswordResetEmails;
}
