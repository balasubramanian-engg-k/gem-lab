<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;  
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use League\Csv\Reader;
use App\Models\Gem; // replace with your model

class ImportController extends Controller
{
    public function importCsv(Request $request)
    {
        // Validate file
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        // Read CSV
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); // first row as header

        $failed = [];
        $rowNumber = 1;

        foreach ($csv as $record) {
            // if($rowNumber > 1) {
                try {
                    $imageUrl = $record['IMAGE']; // Google Drive link from CSV
                    $storedPath = $this->downloadFromGoogleDrive($imageUrl, 'gems');
                    // Example insert
                    Gem::create([
                        'description'  => $record['DESCRIPTION'],
                        'clarity'        => $record['COLOR / CLARITY'],
                        'gross_weight' => $record['GROSS WEIGHT'],
                        'diamond_weight' => $record['STONE WEIGHT'],
                        'image'        => $storedPath, // Google Drive link,
                        'comment' => $record['CONCLUSION'],
                        'created_by' => Auth::id(), // Set created_by to logged-in user
                    ]);
                } catch (\Exception $e) {
                    $failed[] = [
                        'row_no' => $rowNumber,
                        'error'  => $e->getMessage()
                    ];
                }
            // }
            $rowNumber++;
        }

        return response()->json([
            'failed' => $failed
        ]);
    }

    public function downloadFromGoogleDrive($driveUrl, $folder = 'uploads')
    {
        // Extract File ID from the URL
        preg_match('/\/d\/(.*?)\//', $driveUrl, $matches);
        if (!isset($matches[1])) {
            throw new \Exception("Invalid Google Drive link");
        }

        $fileId = $matches[1];
        $url = "https://drive.google.com/uc?export=download&id=" . $fileId;

        // Fetch the file
        $response = Http::get($url);

        if ($response->successful()) {
            // Generate unique filename
            $filename = uniqid() . ".jpg"; // you can detect extension dynamically

            // Save file in storage/app/public/uploads/
            Storage::disk('public')->put("$folder/$filename", $response->body());

            // Return the path to store in DB
            return "$folder/$filename";
        }

        throw new \Exception("Failed to download file from Google Drive");
    }
}
