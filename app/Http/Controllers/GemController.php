<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use App\Models\Gem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\PngEncoder;
use Spatie\Browsershot\Browsershot;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Illuminate\Support\Facades\Auth;
class GemController extends Controller
{
    /**
     * Summary numbers that use `gems.card_new` and `public/certificates_new/{summary_no}.png`.
     * Comparison is case-insensitive. Add more IDs here as you scale.
     *
     * @var list<string>
     */
    private const CERTIFICATE_CARD_NEW_SUMMARY_NOS = [
        'GHCSNRYVZW',
        'GHCJYEA751',
        'GHC1NR5ZY2',
        'GHC0B9JKX2',
        'GHC02KLFW2',
        'GHC22VACRP4',
        'GHC750HDEJ',
        'GHCFMIGSER',
        'GHCFSFPFLB',
        'GHCJ8RIJY5',
        'GHCK6W2KCM',
        'GHCWBOOQPP',
        'GHCXQA7135',
        'GHCZTXA9CR'
    ];

   public function index(Request $request)
    {
        $user = Auth::user();
        $query = Gem::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('summary_no', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('gross_weight', 'LIKE', "%{$search}%")
                ->orWhere('diamond_weight', 'LIKE', "%{$search}%")
                ->orWhere('comment', 'LIKE', "%{$search}%")
                ->orWhere('clarity', 'LIKE', "%{$search}%");
            });
        }

        // Sort by latest (assuming created_at column exists)
        $gems = $query->with('creator')->orderBy('created_at', 'desc')->paginate(100);

        return view('gems.index', compact('gems','user'));
    }

    public function create()
    {
        return view('gems.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description'    => 'required|max:115',
            'weight_type'   => 'required|in:gross_weight,hardness',
            'gross_weight'  => 'required_if:weight_type,gross_weight|nullable',
            'hardness'      => 'required_if:weight_type,hardness|nullable',
            'diamond_weight' => 'required',
            'clarity_type'   => 'required|in:AA,Others',
            'clarity'        => 'required_if:clarity_type,Others|nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Set clarity based on the radio button selection
        $data['clarity'] = ($request->clarity_type === 'Others') ? $request->clarity : 'AA';

        // Handle weight_type: if hardness is selected, store hardness value in gross_weight
        if ($request->weight_type === 'hardness' && $request->has('hardness')) {
            $data['gross_weight'] = $request->hardness;
        }

        // Set weight_type (default to gross_weight if not provided)
        $data['weight_type'] = $request->weight_type ?? 'gross_weight';

        // Remove hardness from data as it's stored in gross_weight
        unset($data['hardness']);

        // Set created_by to the authenticated user's ID
        $data['created_by'] = Auth::id();

       if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gems', 'public');
        }

        Gem::create($data);

        return redirect()->route('gems.index')->with('success', 'Gem certificate added successfully.');
    }

    public function show($id)
    {
        $gem = Gem::findOrFail($id);
        $imageUrl = asset('storage/' . $gem->image);
        $path = $gem->image; // Your image path in storage
        $photopath = '';
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path); // Get the image file
            $type = Storage::disk('public')->mimeType($path); // Get the file type
            $photopath = $base64 = 'data:' . $type . ';base64,' . base64_encode($file);
        }
        $gem->image = $photopath;
        return response()->json($gem);
    }

    public function edit(Gem $gem)
    {
        $photopath = '';
        $imageUrl = asset('storage/' . $gem->image);
        $path = $gem->image; // Your image path in storage
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path); // Get the image file
            $type = Storage::disk('public')->mimeType($path); // Get the file type
            $photopath = $base64 = 'data:' . $type . ';base64,' . base64_encode($file);
        }
        $gem->image = $photopath;
        return view('gems.edit', compact('gem'));
    }
    
    public function card($certificateNo)
    {
        $gem = Gem::where('summary_no', $certificateNo)->firstOrFail();
        $photopath = '';
        $imageUrl = asset('storage/' . $gem->image);
        $path = $gem->image; // Your image path in storage
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path); // Get the image file
            $type = Storage::disk('public')->mimeType($path); // Get the file type
            $photopath = $base64 = 'data:' . $type . ';base64,' . base64_encode($file);
        }
        $gem->image = $photopath;
        $barcode = \DNS1D::getBarcodePNG($gem->summary_no, 'C128', 6, 200);
        $path = 'qrcodes/test.svg';
Storage::disk('public')->put($path, QrCode::format('svg')->size(200)->generate('https://example.com'));
        $qrCode = QrCode::format('svg')
                ->margin(0) // Remove extra white space
                ->generate(url('/search_certificate/' . $gem->summary_no));

        if (in_array(strtoupper((string) $gem->summary_no), array_map('strtoupper', self::CERTIFICATE_CARD_NEW_SUMMARY_NOS), true)) {
            $certNewPublicPath = public_path('certificates_new/'.$gem->summary_no.'.png');
            $certificateNewImageUrl = null;
            if (is_file($certNewPublicPath)) {
                $certificateNewImageUrl = asset('certificates_new/'.$gem->summary_no.'.png').'?v='.filemtime($certNewPublicPath);
            }

            return view('gems.card_new', compact('gem', 'barcode', 'qrCode', 'certificateNewImageUrl'));
        }

        return view('gems.card', compact('gem', 'barcode', 'qrCode'));
    }

    public function cardExport($certificateNo)
    {
        $gem = Gem::where('summary_no', $certificateNo)->firstOrFail();
        $photopath = '';
        $imageUrl = asset('storage/' . $gem->image);
        $path = $gem->image; // Your image path in storage
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path); // Get the image file
            $type = Storage::disk('public')->mimeType($path); // Get the file type
            $photopath = $base64 = 'data:' . $type . ';base64,' . base64_encode($file);
        }
        $gem->image = $photopath;
        $barcode = \DNS1D::getBarcodePNG($gem->summary_no, 'C128', 6, 200);
        $path = 'qrcodes/test.svg';
Storage::disk('public')->put($path, QrCode::format('svg')->size(200)->generate('https://example.com'));
        $qrCode = QrCode::format('svg')
                ->margin(0) // Remove extra white space
                ->generate(url('/search_certificate/' . $gem->summary_no));
        return view('gems.cardExport', compact('gem','barcode', 'qrCode'));
    }

    public function update(Request $request, Gem $gem)
    {
        $request->validate([
            'description'    => 'required|max:115',
            'weight_type'   => 'required|in:gross_weight,hardness',
            'gross_weight'  => 'required_if:weight_type,gross_weight|nullable',
            'hardness'      => 'required_if:weight_type,hardness|nullable',
            'diamond_weight' => 'required',
            'clarity_type'   => 'required|in:AA,Others',
            'clarity'        => 'required_if:clarity_type,Others|nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Set clarity based on the radio button selection
        $data['clarity'] = ($request->clarity_type === 'Others') ? $request->clarity : 'AA';

        // Handle weight_type: if hardness is selected, store hardness value in gross_weight
        if ($request->weight_type === 'hardness' && $request->has('hardness')) {
            $data['gross_weight'] = $request->hardness;
        }

        // Set weight_type (default to gross_weight if not provided)
        $data['weight_type'] = $request->weight_type ?? 'gross_weight';

        // Remove hardness from data as it's stored in gross_weight
        unset($data['hardness']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gems', 'public');
        }

        $gem->update($data);

        return redirect()->route('gems.index')->with('success', 'Gem certificate updated successfully.');
    }

    public function destroy(Gem $gem)
    {
        $gem->delete();
        return redirect()->route('gems.index')->with('success', 'Gem certificate deleted successfully.');
    }

    public function certificate($certificateNo)
    {
        $gem = Gem::where('summary_no', $certificateNo)->firstOrFail();
        if (!$gem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate not found'
            ], 404);
        }
        $gem['status'] = 'success';
        return response()->json($gem);
    }

    public function dashboard()
    {
        $user = auth()->user();
        return view('dashboard.index');
    }

   public function downloadCertificate(Request $request)
    {
        $data = $request->input('image');
        $filename = $request->input('filename');
        // Strip "data:image/png;base64,"
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $data);
        $imageData = base64_decode($imageData);
        // Read + set DPI
        $image = Image::read($imageData)->setResolution(300, 300);
        // Encode using PngEncoder (v3 way)
        $pngData = $image->encode(new PngEncoder());
        return response($pngData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

   public function printCertificate(Request $request)
    {
        $data = $request->input('image');
        $filename = $request->input('filename', uniqid().'.png');

        // Strip "data:image/png;base64,"
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $data);
        $imageData = base64_decode($imageData);

        // Read and set resolution
        $image = Image::read($imageData)->setResolution(300, 300);

        // Encode to PNG (quality = 90)
        $encoded = $image->encode(new PngEncoder(90));

        // Save to storage
        Storage::disk('public')->put("certificates/".$filename, $encoded);

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'url' => Storage::url("certificates/".$filename),
        ]);
    }

    public function saveCertificate(Request $request)
    {
        $data = $request->input('image');
        $cert_no = $request->input('cert_no');
        $filename = $request->input('filename');
        // Strip "data:image/png;base64,"
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $data);
        $imageData = base64_decode($imageData);
        // Read + set DPI
        $image = Image::read($imageData)->setResolution(300, 300);
        // Encode using PngEncoder (v3 way)
        $pngData = $image->encode(new PngEncoder());

        Storage::disk('public')->put("export/".$filename, $pngData);
        \App\Models\Gem::where('summary_no', $cert_no)->update(['certificate_generated' => 1]);
        return response()->json([
            'success' => true,
            'path' => 'storage/certificates/'.$filename
        ]);
    }

    public function downloadZip()
    {
        $filePath = storage_path('app/public/latest_backup.zip');

        if (!file_exists($filePath)) {
            abort(404, 'File not found!');
        }

        return response()->download($filePath, 'latest_backup.zip', [
            'Content-Type' => 'application/zip'
        ]);
    }

    public function exportGems(Request $request)
    {
        $summaryNos = $request->input('gems', []); // frontend sends summary_no
        if (empty($summaryNos)) {
            return response()->json(['error' => 'No gems selected'], 400);
        }

        // Fetch gem details for Excel with creator relationship
        $gems = Gem::with('creator')->whereIn('summary_no', $summaryNos)->get();

        // Create Excel spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gems Details');

        // Header row
        $sheet->setCellValue('A1', 'Summary No');
        $sheet->setCellValue('B1', 'Created At');
        $sheet->setCellValue('C1', 'Created By');

        // Data rows
        $row = 2;
        foreach ($gems as $gem) {
            $sheet->setCellValue("A{$row}", $gem->summary_no);
            $sheet->setCellValue("B{$row}", $gem->created_at ? \Carbon\Carbon::parse($gem->created_at)->format('Y-m-d H:i:s') : '-');
            $sheet->setCellValue("C{$row}", $gem->creator ? $gem->creator->name : 'Admin');
            $row++;
        }

        // Save Excel to storage as .xls
        $excelFileName = 'gem_details.xls';  
        $excelPath = storage_path("app/public/{$excelFileName}");
        $writer = new Xls($spreadsheet); // <-- use Xls writer
        $writer->save($excelPath);

        // Create ZIP including images + Excel
        $zip = new \ZipArchive;
        $zipFileName = 'export.zip';
        $zipPath = storage_path("app/public/{$zipFileName}");

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            // Add images
            foreach ($summaryNos as $summaryNo) {
                $filePath = storage_path("app/public/export/{$summaryNo}.png");
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, "{$summaryNo}.png");
                }
            }

            // Add Excel
            if (file_exists($excelPath)) {
                $zip->addFile($excelPath, $excelFileName);
            }

            $zip->close();
        } else {
            return response()->json(['error' => 'Could not create ZIP file'], 500);
        }

        // Optional: delete Excel file after adding to ZIP
        if (file_exists($excelPath)) {
            unlink($excelPath);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
