<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ConfirmPasswordController extends Controller implements HasMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini bertanggung jawab untuk menangani konfirmasi kata sandi
    | dan menggunakan trait sederhana untuk menyertakan perilaku tersebut. Anda
    | bebas untuk menjelajahi trait ini dan menimpa fungsi apa pun yang
    | memerlukan penyesuaian.
    |
    */

    use ConfirmsPasswords;

    /**
     * Tujuan pengalihan pengguna ketika URL yang dituju gagal.
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
        ];
    }
}
