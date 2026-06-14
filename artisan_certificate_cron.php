<?php

use App\Models\Gem;
use Spatie\Browsershot\Browsershot;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;
use App\Exports\GemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Bootstrap Laravel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ==============================
// Your gem certificate logic
// ==============================

// Find gems without certificate
$gems = Gem::where('certificate_generated', 0)->get();
Excel::store(
    new GemsExport,
    'export/gems.xls',
    'public',
    ExcelFormat::XLS
);
foreach ($gems as $gem) {
    $url = route('gems.cardExport', ['summary_no' => $gem->summary_no]);
    $path = storage_path("app/public/export/{$gem->summary_no}.png");
    Browsershot::url($url)
        ->waitUntilNetworkIdle()  // waits for all JS fetch() calls to finish
        ->bodyHtml();             // just load page, no saving

    // Mark as generated
    $gem->certificate_generated = 1;
    $gem->save();

    echo "✅ Certificate generated for Gem ID: {$gem->id}\n";
}

 $zip = new \ZipArchive;
// Path where zip will be stored
$zipFileName = 'latest_backup.zip';
$zipFilePath = storage_path('app/public/'.$zipFileName);

if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
    // Example: add multiple files from storage
$files = Storage::disk('public')->files('export'); // all files in "storage/app/public/certificates"
foreach ($files as $file) {
    $absolutePath = storage_path('app/public/'.$file);
    if (file_exists($absolutePath)) {
        // Add file to zip (basename keeps only the file name inside the zip)
        $zip->addFile($absolutePath, basename($file));
    }
}
$zip->close();
    
}