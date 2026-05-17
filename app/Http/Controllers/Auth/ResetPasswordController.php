<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini bertanggung jawab untuk menangani permintaan penyetelan ulang
    | kata sandi dan menggunakan trait sederhana untuk menyertakan perilaku ini.
    | Anda bebas menjelajahi trait ini dan menimpa metode apa pun yang ingin
    | Anda sesuaikan.
    |
    */

    use ResetsPasswords;

    /**
     * Tujuan pengalihan pengguna setelah berhasil menyetel ulang kata sandi mereka.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
