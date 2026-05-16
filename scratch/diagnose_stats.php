<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DIAGNOSA DATA KENDARAAN ---\n";
echo "Total Kendaraan: " . \App\Models\Vehicle::count() . "\n\n";

echo "--- DISTRIBUSI KONDISI ---\n";
$stats = \App\Models\Vehicle::selectRaw('kondisi, count(*) as qty')
    ->groupBy('kondisi')
    ->get();

foreach ($stats as $s) {
    echo "[" . $s->kondisi . "]: " . $s->qty . " kendaraan\n";
}

echo "\n--- SAMPLE DATA TERBARU ---\n";
$latest = \App\Models\Vehicle::latest()->first();
if ($latest) {
    echo "ID: " . $latest->id . "\n";
    echo "Plat: " . $latest->no_polisi . "\n";
    echo "Kondisi: '" . $latest->kondisi . "'\n";
    echo "Status: '" . $latest->status . "'\n";
    echo "OPD ID: " . $latest->opd_id . "\n";
}
