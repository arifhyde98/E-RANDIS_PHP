<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controller untuk Manajemen Pengaturan Aplikasi (CMS)
 * 
 * Digunakan untuk memperbarui variabel web seperti logo, nama instansi, dll.
 */
class SettingController extends Controller
{
    /**
     * Menampilkan halaman daftar pengaturan yang dikelompokkan berdasarkan grup.
     * 
     * @return View
     */
    public function index(): View
    {
        $settings = Setting::all()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    /**
     * Memperbarui beberapa pengaturan sekaligus.
     * 
     * Menangani pembaruan teks maupun unggahan file gambar, serta membersihkan cache.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                if ($setting->type === 'image' && $request->hasFile("settings.$key")) {
                    // Hapus file lama jika ada
                    if ($setting->value && Str::startsWith($setting->value, 'uploads/')) {
                        File::delete(public_path($setting->value));
                    }

                    $directory = public_path('uploads/settings');
                    File::ensureDirectoryExists($directory);

                    $file = $request->file("settings.$key");
                    $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
                    $file->move($directory, $filename);

                    $path = 'uploads/settings/'.$filename;
                    $setting->update(['value' => $path]);
                    cache()->forget("setting.{$key}");
                } else {
                    $setting->update(['value' => $value]);
                    cache()->forget("setting.{$key}");
                }
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}

