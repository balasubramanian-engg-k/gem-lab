<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Registration;
use App\Models\RegistrationType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class RegistrationController extends Controller
{
    public function store(Request $request)
    {   
        $user = Registration::where('email', $request->email)->where('status', 0)->first();
        if ($user) {
            return response()->json(['message' => 'User is not active', 'data' => $user], 201);
        }
        // Validate input fields
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => 'required|email|unique:registrations,email',
            'mobilenumber' => 'required|digits_between:10,15',
            'parent_name' => 'required|string',
            'relationship' => 'required|string',
            'mother_tounge' => 'required|string',
            'gender' => 'required|string',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'state' => 'required|string',
            'district' => 'required|string',
            'taluk' => 'required|string',
            'pincode' => 'required|string',
            'photo' => 'nullable|file|mimes:jpg,png|max:1024',
            'birth_certificate' => 'nullable|file|mimes:jpg,png|max:1024',
            'title' => 'nullable|string',
            'fide_id' => 'nullable|numeric',
            'aicp_id' => 'nullable|alpha_num',
            'tnsca_id' => 'nullable|alpha_num',
            'player_type' => 'required|string',
            'dob_registration' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $registrationType = RegistrationType::find($request->player_type);
        if (!$registrationType) {
            return response()->json(['success' => false, 'message' => 'Registration type not found'], 404);
        }
        // Handle file uploads
        $photoPath = $request->file('photo') ? $request->file('photo')->store('uploads/photos', 'public') : null;
        $certificatePath = $request->file('birth_certificate') ? $request->file('birth_certificate')->store('uploads/certificates', 'public') : null;

        // Store in database
        $registration = Registration::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'mobilenumber' => $request->mobilenumber,
            'parent_name' => $request->parent_name,
            'relationship' => $request->relationship,
            'mother_tounge' => $request->mother_tounge,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'state' => 'Tamil Nadu',
            'district' => 'Madurai',
            'taluk' => $request->taluk,
            'pincode' => $request->pincode,
            'photo' => $photoPath,
            'birth_certificate' => $certificatePath,
            'title' => $request->title,
            'fide_id' => $request->fide_id,
            'aicp_id' => $request->aicp_id,
            'player_type' => $registrationType->title,
            'dob_registration' => $request->dob_registration,
            'registration_type' => $request->player_type,
            'status' => 0,
            'user_status' => 0,
            'tnsca_id' => $request->tnsca_id
        ]);

        return response()->json(['message' => 'Registration successful', 'data' => $registration], 201);
    }

    public function getRegistrations(Request $request)
    {
        $query = Registration::query();

        $query->where('status', 1);

        if ($request->filled('taluk')) {
            $query->where('taluk', $request->taluk);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('aicp_id', 'like', "%{$search}%")
                ->orWhere('fide_id', 'like', "%{$search}%")
                ->orWhere('mdcc_id', 'like', "%{$search}%");
            });
        }

        // Explicitly select all fields except 'email'
        $columns = Schema::getColumnListing('registrations');
        $columns = array_filter($columns, fn($col) => $col !== 'email');
        $query->select($columns);

        $perPage = $request->input('per_page', 10);
        $registrations = $query->paginate($perPage);

        return response()->json([
            "data" => $registrations->items(),
            "recordsTotal" => $registrations->total(),
            "recordsFiltered" => $registrations->total(),
            "perPage" => $registrations->perPage(),
            "currentPage" => $registrations->currentPage()
        ]);
    }

    public function getRegistration($id)
    {
        $registration = Registration::find($id);

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found'], 404);
        }

        // Concatenating first name and last name
        $fullName = trim($registration->first_name . ' ' . $registration->last_name);

        // Masking the mobile number (e.g., 9********0)
        $maskedMobile = substr($registration->mobilenumber, 0, 1) . str_repeat('*', strlen($registration->mobilenumber) - 2) . substr($registration->mobilenumber, -1);

        // Masking the email (e.g., a***@gmail.com)
        $emailParts = explode('@', $registration->email);
        $maskedEmail = substr($emailParts[0], 0, 1) . str_repeat('*', max(strlen($emailParts[0]) - 1, 1)) . '@' . $emailParts[1];

        // Masking the address (Replace everything with asterisks)
        $maskedAddress = str_repeat('*', strlen($registration->address));
        $path = $registration->photo; // Your image path in storage
        $photopath = '';
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path); // Get the image file
            $type = Storage::disk('public')->mimeType($path); // Get the file type
            $photopath = $base64 = 'data:' . $type . ';base64,' . base64_encode($file);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'mdcc_id' => $registration->mdcc_id, // Combined first name and last name
                'full_name' => $fullName, // Combined first name and last name
                'email' => '**********', // Masked email
                'mobilenumber' => $maskedMobile, // Masked mobile number
                'gender' => $registration->gender,
                'address' => '**********', // Masked address
                'state' => $registration->state,
                'district' => $registration->district,
                'taluk' => $registration->taluk,
                'pincode' => $registration->pincode,
                'player_type' => $registration->player_type,
                'relationship' => $registration->relationship,
                'parent_name' => $registration->parent_name,
                'mother_tounge' => $registration->mother_tounge,
                'year_of_birth' => $registration->date_of_birth ? Carbon::parse($registration->date_of_birth)->format('Y') : null,
                'fide_id' => $registration->fide_id,
                'aicp_id' => $registration->aicp_id,
                'district' => $registration->district,
                'photo' => $photopath,
                'tnsca_id' => $registration->tnsca_id,
                'user_status' => $registration->user_status,
            ]
        ]);
    }

}
