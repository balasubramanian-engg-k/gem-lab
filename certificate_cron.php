<?php
// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use App\Models\Gem;   // include your model

// Boot Laravel Kernel
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// 1. Fetch gems (customize your query as needed)
$gems = Gem::where('certificate_generated', 0)->get(); // example: only pending

foreach ($gems as $gem) {
    try {
        // 2. Render Blade view into PDF
        $barcode = \DNS1D::getBarcodePNG($gem->summary_no, 'C128', 6, 200);
        $html = View::make('gems.cardExport', ['gem' => $gem,'barcode' => $barcode])->render();
        $pdf = Pdf::loadHTML($html);

        // Save temporary PDF
        $pdfPath = storage_path("app/public/certificates/{$gem->id}.pdf");
        $pdf->save($pdfPath);

        // 3. Convert PDF → PNG
        $imagick = new \Imagick();
        $imagick->setResolution(300, 300);
        $imagick->readImage($pdfPath . '[0]'); // only first page
        $imagick->setImageFormat('png');

        $pngPath = storage_path("app/public/certificates/{$gem->id}.png");
        $imagick->writeImage($pngPath);
        $imagick->clear();
        $imagick->destroy();

        // 4. Mark as generated in DB
        $gem->certificate_generated = 1;
        $gem->certificate_path = "app/public/certificates/{$gem->id}.png";
        $gem->save();

        echo "✅ Certificate generated for Gem ID: {$gem->id} → $pngPath\n";
    } catch (Exception $e) {
        echo "❌ Error for Gem ID {$gem->id}: " . $e->getMessage() . "\n";
    }
}
