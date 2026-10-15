<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function user_reset_password(Request $request, $token, $email)
    {
        // Validates that the signature in the URL matches and hasn't been tampered with or expired
        if (! $request->hasValidSignature()) {
            return response()->json([
                'status' => false,
                'message' => "Invalid or expired reset password link."
            ]);
        }

        $user = User::CheckEmailExist($email)->CheckTokenExist($token)->first();

        if (!$user) {

            return response()->json([
                'message' => 'Invalid token or email'
            ]);
        }
        return view('Auth.resetPassword', compact('token', 'email'));
    }

    public function reset_password_submit(Request $request, $email_token, $email)
    {
        // Validates that the signature in the URL matches and hasn't been tampered with or expired
        if (! $request->hasValidSignature()) {
            return response()->json([
                'status' => false,
                'message' => "Invalid or expired reset password link."
            ]);
        }
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        $user = User::CheckEmailExist($email)->CheckTokenExist($email_token)->first();

        $user->password = Hash::make($request->password);
        $user->email_token = '';
        $user->update();

        return response()->json([
            'message' => 'Password reseted successfuly',
            'new_password' => $request->password
        ]);
    }
}
