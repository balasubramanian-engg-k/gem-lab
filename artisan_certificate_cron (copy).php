<?php

use App\Models\Gem;
use Spatie\Browsershot\Browsershot;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;
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

foreach ($gems as $gem) {
    $url = route('gems.cardExport', ['summary_no' => $gem->summary_no]);
    $path = storage_path("app/public/certificates/{$gem->summary_no}.png");

    Browsershot::url($url)
        ->waitUntilNetworkIdle()  // waits for all JS fetch() calls to finish
        ->bodyHtml();             // just load page, no saving

    //     response()->download($path);
    //     exec("wkhtmltoimage --quality 300 {$url} {$path}");

    //     $manager = new ImageManager(\Intervention\Image\Drivers\Gd\Driver::class);
    // // or use Imagick driver if installed
    // // $manager = new ImageManager(\Intervention\Image\Drivers\Imagick\Driver::class);

    // $image = $manager->read(storage_path("app/public/certificates/{$gem->summary_no}.png"))
    //     ->resize(1004, 650)
    //     ->setResolution(300, 300)
    //     ->save(storage_path("app/public/certificates/certificate_resized_{$gem->summary_no}.png"));
        // 👉 Put your actual certificate generation logic here
        // e.g., create PDF, image, etc.

        // Mark as generated
        // $gem->certificate_generated = 1;
        // $gem->save();

    echo "✅ Certificate generated for Gem ID: {$gem->id}\n";
}
