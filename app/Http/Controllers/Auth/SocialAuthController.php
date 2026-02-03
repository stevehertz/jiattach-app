<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    //

    /**
     * Redirect to the provider (Google/LinkedIn)
     */
    public function redirect($provider)
    {
        // Handle LinkedIn specific driver name if needed, or use default
        $driver = $provider === 'linkedin' ? 'linkedin-openid' : $provider;
        
        return Socialite::driver($driver)->redirect();
    }

    /**
     * Handle the callback from the provider
     */
    public function callback($provider)
    {
        try {
            $driver = $provider === 'linkedin' ? 'linkedin-openid' : $provider;
            $socialUser = Socialite::driver($driver)->user();

            // Check if user exists by Social ID or Email
            $user = User::where($provider . '_id', $socialUser->id)
                ->orWhere('email', $socialUser->email)
                ->first();

            if ($user) {
                // Update Social ID and Avatar if missing
                if (!$user->{$provider . '_id'}) {
                    $user->update([
                        $provider . '_id' => $socialUser->id,
                        'avatar' => $socialUser->avatar
                    ]);
                }
                
                Auth::login($user, true); // true = Remember Me

                return redirect()->intended('student/dashboard'); // Redirect based on role logic
            } else {
                // Create New User
                $user = $this->createUser($socialUser, $provider);
                
                Auth::login($user, true);

                // Redirect to profile edit to fill in missing details (National ID, etc)
                return redirect()->route('student.profile.edit') 
                    ->with('success', 'Account created! Please complete your profile details.');
            }

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('status', 'Login failed: ' . $e->getMessage());
        }
    }

     /**
     * Create a new user from Social Data
     */
    protected function createUser($socialUser, $provider)
    {
        // Split Name
        $nameParts = explode(' ', $socialUser->name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        // For Google, we can sometimes get specific parts
        if ($provider === 'google' && isset($socialUser->user['given_name'])) {
            $firstName = $socialUser->user['given_name'];
            $lastName = $socialUser->user['family_name'] ?? '';
        }

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $socialUser->email,
            'password' => Hash::make(Str::random(24)), // Random password
            'email_verified_at' => now(), // Trusted provider = Auto verified
            $provider . '_id' => $socialUser->id,
            'avatar' => $socialUser->avatar,
            'is_active' => true,
            'is_verified' => false, // Still needs document verification
            // Default placeholder values to satisfy DB constraints if not nullable
            // You might need to make these columns nullable in migration or handle here
            'disability_status' => 'none', 
            'gender' => 'prefer_not_to_say',
        ]);

        // Assign default Student Role
        $user->assignRole('student');

        return $user;
    }
}
