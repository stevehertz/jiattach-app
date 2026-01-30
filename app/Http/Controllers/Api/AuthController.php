<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    // Use trait to access the static logActivity method
    use LogsActivity;

    /**
     * Handle Student Registration
     */
    public function register(RegisterRequest $request) 
    {
        // Start Database Transaction
        return DB::transaction(function () use ($request) {
            // 1. Create User
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'national_id' => $request->national_id,
                'disability_status' => $request->disability_status,
                'disability_details' => in_array($request->disability_status, ['none', 'prefer_not_to_say']) 
                                        ? null 
                                        : $request->disability_details,
                'county' => $request->county,
                'password' => Hash::make($request->password),
                'is_active' => true,
                'verification_token' => Str::random(60),
                'bio' => $request->bio,
            ]);

             // 2. Assign Role
            $user->assignRole('student');

            // 3. Create Student Profile
            // Note: 'skills' and 'interests' are automatically cast to JSON by the model
            StudentProfile::create([
                'user_id' => $user->id,
                'student_reg_number' => $request->student_reg_number,
                'institution_name' => $request->institution_name,
                'institution_type' => $request->institution_type,
                'course_name' => $request->course_name,
                'course_level' => $request->course_level,
                'year_of_study' => $request->year_of_study,
                'expected_graduation_year' => $request->expected_graduation_year,
                'cgpa' => $request->cgpa ?: null,
                'skills' => $request->skills ?? [],
                'interests' => $request->interests ?? [],
                'preferred_location' => $request->preferred_location,
                'attachment_status' => 'seeking',
            ]);

            // 4. Create Token (Auto-login)
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // 5. Log Custom Activity (Registration is already logged by LogsModelActivity trait on User model)
            // But we can log the login event
            Auth::login($user);

            LogsActivity::logActivity('User registered and logged in via API', 'logged_in');

            return response()->json([
                'message' => 'Registration successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load('studentProfile'),
            ], 201);
        });

    }

     /**
     * Handle Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Check if active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Account is deactivated. Please contact support.'
            ], 403);
        }

        // Update Last Login
        $user->update(['last_login_at' => now()]);

        // Create Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Custom Activity Log for Login
        // Note: We temporarily act as the user to ensure the log has the correct causer_id
        Auth::setUser($user); 
        LogsActivity::logActivity('User logged in via API', 'logged_in');

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('studentProfile'),
        ]);
    }

     /**
     * Handle Logout
     */
    public function logout(Request $request)
    {
        // Log the logout event before destroying token
        LogsActivity::logActivity('User logged out via API', 'logged_out');

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get Authenticated User
     */
    public function user(Request $request)
    {
        return response()->json(
            $request->user()->load('studentProfile')
        );
    }
}
