<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $types = VehicleType::withCount('vehicles')->latest()->get();
        return view('vehicle-types.index', compact('types'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:vehicle_types,name',
            'description' => 'nullable'
        ]);

        VehicleType::create($validated);

        return redirect()->route('vehicle-types.index')
            ->with('success', 'Jenis kendaraan berhasil ditambahkan.');
    }

    public function update(Request $request, VehicleType $vehicleType): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:vehicle_types,name,' . $vehicleType->id,
            'description' => 'nullable'
        ]);

        $vehicleType->update($validated);

        return redirect()->route('vehicle-types.index')
            ->with('success', 'Jenis kendaraan berhasil diperbarui.');
    }

    public function destroy(VehicleType $vehicleType): \Illuminate\Http\RedirectResponse
    {
        // Check if there are vehicles using this type
        if ($vehicleType->vehicles()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Masih ada kendaraan yang menggunakan jenis ini.');
        }

        $vehicleType->delete();

        return redirect()->route('vehicle-types.index')
            ->with('success', 'Jenis kendaraan berhasil dihapus.');
    }

    public function cleanup(): \Illuminate\Http\RedirectResponse
    {
        $deletedCount = VehicleType::whereDoesntHave('vehicles')->delete();

        return redirect()->route('vehicle-types.index')
            ->with('success', "$deletedCount Jenis kendaraan yang kosong berhasil dibersihkan.");
    }
}
