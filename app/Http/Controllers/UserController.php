<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Opd;
use App\Enums\UserRole;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

/**
 * Controller untuk Manajemen Pengguna & Role (Khusus Superadmin)
 */
class UserController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            'role:superadmin',
        ];
    }

    /**
     * Menampilkan daftar pengguna dengan filter role.
     */
    public function index(Request $request)
    {
        $query = User::with('opd');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        $users = $query->latest()->paginate(15);
        $opds = Opd::orderBy('nama')->get();
        $roles = UserRole::cases();

        return view('users.index', compact('users', 'opds', 'roles'));
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user)
    {
        // Mencegah hapus diri sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Generate akun otomatis untuk semua OPD yang belum memiliki akun.
     */
    public function generateAllOpdAccounts(\App\Services\AccountService $accountService)
    {
        $opdsWithoutAccount = Opd::whereDoesntHave('user')->get();
        $count = 0;

        if ($opdsWithoutAccount->isEmpty()) {
            return redirect()->route('users.index')->with('info', 'Semua OPD sudah memiliki akun admin.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($opdsWithoutAccount, $accountService, &$count) {
            foreach ($opdsWithoutAccount as $opd) {
                $accountService->createOpdAccount($opd);
                $count++;
            }
        });

        return redirect()->route('users.index')->with('success', "Berhasil men-generate {$count} akun admin OPD baru.");
    }

    /**
     * Reset password user ke password acak baru.
     */
    public function resetPassword(User $user)
    {
        $newPassword = 'DGL-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(4));
        
        $user->update([
            'password' => $newPassword, // hashed via model cast
        ]);

        return redirect()->route('users.index')->with([
            'success' => "Password untuk {$user->name} berhasil di-reset.",
            'reset_password' => [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $newPassword
            ]
        ]);
    }
}
