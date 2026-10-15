<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\WebsiteMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

//user will send their email to sent a reset pass link

class ForgotPasswordController extends Controller
{
    public function forgetPassword()
    {
        return view('Auth.EmailForgetPassword');
    }

    public function forget_Password_Submit(Request $request)
    {

        $request->validate([
            'email' => 'required|email'
        ]);

        $user =  User::CheckEmailExist($request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email does not exist.'
            ]);
        }
        $email_token = hash('sha256', time());
        $user->email_token = $email_token;
        $user->update();


        $link = URL::temporarySignedRoute(
            'customer_reset_password',
            now()->addMinutes(60),
            ['email_token' => $email_token, 'email' => $request->email]
        );

        $subject = 'Reset Password';
        $message = 'Click on the following link to reset your password:<br> <a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new WebsiteMail($subject, $message));

        return response()->json([
            'message' => 'Reset Password link sent to your email',
            'link' => $link
        ]);
    }
}
