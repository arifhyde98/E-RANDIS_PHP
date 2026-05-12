<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                if ($setting->type === 'image' && $request->hasFile("settings.$key")) {
                    // Delete old image if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    $path = $request->file("settings.$key")->store('settings', 'public');
                    $setting->update(['value' => $path]);
                } else {
                    $setting->update(['value' => $value]);
                }
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
