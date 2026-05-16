<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;

/**
 * Controller untuk Manajemen Profil Pengguna
 */
class ProfileController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    /**
     * Menampilkan halaman profil.
     */
    public function index()
    {
        return view('profile.index', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Memperbarui profil pengguna.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        // Update data dasar
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = $validated['password']; // hashed via model cast
        }

        // Update avatar jika diunggah
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
