<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function index(Request $request)
    {
        $query = Opd::query();

        if ($request->has('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('singkatan', 'like', '%' . $request->q . '%');
        }

        $opds = $query->orderBy('nama')->paginate(15);
        
        return view('opds.index', compact('opds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:opds,nama',
            'singkatan' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        Opd::create($request->all());

        return redirect()->route('opds.index')->with('success', 'Data OPD berhasil ditambahkan.');
    }

    public function update(Request $request, Opd $opd)
    {
        $request->validate([
            'nama' => 'required|unique:opds,nama,' . $opd->id,
            'singkatan' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $opd->update($request->all());

        return redirect()->route('opds.index')->with('success', 'Data OPD berhasil diperbarui.');
    }

    public function destroy(Opd $opd)
    {
        // Check if there are vehicles attached to this OPD (once refactored)
        // For now, just delete since it's master data
        $opd->delete();

        return redirect()->route('opds.index')->with('success', 'Data OPD berhasil dihapus.');
    }
}
