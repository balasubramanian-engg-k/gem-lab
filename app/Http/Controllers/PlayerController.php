<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    // Store Player Details
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'father_mother_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'taluk' => 'required|string|max:500',
            'email' => 'required|email|unique:players,email',
            'mobile_number' => 'required|string|max:15',
            'mother_tongue' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'pincode' => 'required|string|max:10',
            'date_of_birth' => 'required|date',
            'date_of_birth_registration' => 'nullable|date',
            'fide_id' => 'nullable|string|max:255',
            'aicf_id' => 'nullable|string|max:255',
            'player_type' => 'required|in:Player,Arbiter',
            'passport_photo' => 'nullable|image|mimes:jpg,png|max:1000',
            'birth_certificate' => 'nullable|file|mimes:pdf,jpg,png|max:1000',
        ]);

        // Handle File Uploads
        $passportPhotoPath = $request->file('passport_photo') 
            ? $request->file('passport_photo')->store('players/photos', 'public')
            : null;

        $birthCertificatePath = $request->file('birth_certificate') 
            ? $request->file('birth_certificate')->store('players/certificates', 'public')
            : null;

        // Create Player
        $player = Player::create(array_merge(
            $request->except(['passport_photo', 'birth_certificate']),
            ['passport_photo' => $passportPhotoPath, 'birth_certificate' => $birthCertificatePath]
        ));

        return response()->json([
            'message' => 'Player registered successfully!',
            'player' => $player
        ], 201);
    }
}
