<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    public function index()
    {
        $types = VehicleType::withCount('vehicles')->latest()->get();
        return view('vehicle-types.index', compact('types'));
    }

    public function create()
    {
        return view('vehicle-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:vehicle_types,name',
            'description' => 'nullable'
        ]);

        VehicleType::create($request->all());

        return redirect()->route('vehicle-types.index')
            ->with('success', 'Jenis kendaraan berhasil ditambahkan.');
    }

    public function edit(VehicleType $vehicleType)
    {
        return view('vehicle-types.edit', compact('vehicleType'));
    }

    public function update(Request $request, VehicleType $vehicleType)
    {
        $request->validate([
            'name' => 'required|unique:vehicle_types,name,' . $vehicleType->id,
            'description' => 'nullable'
        ]);

        $vehicleType->update($request->all());

        return redirect()->route('vehicle-types.index')
            ->with('success', 'Jenis kendaraan berhasil diperbarui.');
    }

    public function destroy(VehicleType $vehicleType)
    {
        // Check if there are vehicles using this type
        if ($vehicleType->vehicles()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Masih ada kendaraan yang menggunakan jenis ini.');
        }

        $vehicleType->delete();

        return redirect()->route('vehicle-types.index')
            ->with('success', 'Jenis kendaraan berhasil dihapus.');
    }

    public function cleanup()
    {
        $deletedCount = 0;
        $types = VehicleType::withCount('vehicles')->get();

        foreach ($types as $type) {
            if ($type->vehicles_count == 0) {
                $type->delete();
                $deletedCount++;
            }
        }

        return redirect()->route('vehicle-types.index')
            ->with('success', "$deletedCount Jenis kendaraan yang kosong berhasil dibersihkan.");
    }
}
