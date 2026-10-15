<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WebsiteMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register_create()
    {
        return view('Auth.register');
    }

    public function register_store(Request $request)
    {
        $request->validate([
            'name' => ['required',  'regex:/^[A-Za-z\s]+$/', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->max(16)->mixedCase()->numbers()->symbols(),
            ],
        ]);
        $user = new User;
        $email_token = Str::random(64);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->email_token = $email_token;
        $user->save();

        // A secure signed URL that expires in 60 minutes
        $link = URL::temporarySignedRoute(
            'registration_verify',
            now()->addMinutes(60),
            ['email_token' => $email_token, 'email' => $request->email]
        );

        $subject = 'Registration Verification';
        $message = 'Click on the following link to verify your email: <br> <a href="'.$link.'">'.$link.'</a>';

        Mail::to($request->email)->send(new WebsiteMail($subject, $message));

        return response()->json([
            'status' => true,
            'message' => 'Register Successfuly, check your email to verify it. but note this link will expires after 1 hour.',
        ]);
    }

    public function registration_verify(Request $request, $email_token, $email)
    {
        // Validates that the signature in the URL matches and hasn't been tampered with or expired
        if (! $request->hasValidSignature()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired verification link.',
            ]);
        }

        $user = User::CheckEmailExist($email)->CheckTokenExist($email_token)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid token or email',
            ]);
        }
        $user->forceFill([
            'email_token' => null,
            'email_verified_at' => now(),
        ])->save();

        return redirect()->route('login');
    }

    public function login_create()
    {

        return view('Auth.login');
    }

    public function login_store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $remember = $request->has('remember') ? true : false;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {

            if (is_null(Auth::user()->email_verified_at)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'status' => false,
                    'message' => 'Please verify your email address before logging in',
                ], 403);
            } else {

                $request->session()->regenerate();

                return response()->json([
                    'status' => true,
                    'message' => 'Logged in Successfuly.',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect Email or Password.',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/welcome');
    }
}
