<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VerificationController extends Controller implements HasMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini bertanggung jawab untuk menangani verifikasi email untuk
    | setiap pengguna yang baru mendaftar pada aplikasi. Email juga dapat
    | dikirim ulang jika pengguna tidak menerima pesan email asli.
    |
    */

    use VerifiesEmails;

    /**
     * Tujuan pengalihan pengguna setelah berhasil verifikasi.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Mendapatkan middleware yang ditugaskan ke controller ini.
     * 
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('signed', only: ['verify']),
            new Middleware('throttle:6,1', only: ['verify', 'resend']),
        ];
    }
}
