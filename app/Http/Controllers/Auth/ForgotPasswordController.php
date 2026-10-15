<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WebsiteMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Handles the "forgot password" request: creates a reset token, emails a
 * reset link, and (in non-production) prints the link to the server console
 * so the flow can be exercised without access to email.
 */
class ForgotPasswordController extends Controller
{
    /**
     * How long a reset link stays valid, in minutes.
     */
    public const TOKEN_TTL_MINUTES = 30;

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');

        // Always answer with exactly the same payload, whether or not the
        // account exists, so the endpoint cannot be used to enumerate users.
        $genericResponse = response()->json([
            'message' => 'If an account exists for that email, a reset link has been sent.',
        ]);

        $user = User::CheckEmailExist($email)->first();

        if (! $user) {
            return $genericResponse;
        }

        $token = Str::random(64);

        // Store only a deterministic hash of the token. A deterministic hash
        // (rather than a salted one) lets us look the token up on its own,
        // which is what allows the link to carry just the token.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        $link = $this->resetUrl($token);

        $this->logDevLink($link);

        // Still attempt the normal email send, but never let a delivery
        // failure change what the user sees.
        try {
            $subject = 'Reset Password';
            $message = 'Click on the following link to reset your password:<br> <a href="'.$link.'">'.$link.'</a>';

            Mail::to($user->email)->send(new WebsiteMail($subject, $message));
        } catch (\Throwable $e) {
            Log::warning('[password-reset] Failed to send reset email.', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return $genericResponse;
    }

    private function resetUrl(string $token): string
    {
        return rtrim(config('app.url'), '/').'/reset-password?token='.$token;
    }

    /**
     * In every environment except production, echo the full reset link to the
     * server console so developers can follow the flow without email.
     *
     * The application environment (APP_ENV) is the PHP equivalent of NODE_ENV;
     * it is what determines whether we are running in production.
     */
    private function logDevLink(string $link): void
    {
        if (app()->environment('production')) {
            return;
        }

        $line = '[password-reset] '.$link;

        // `php artisan serve` / php -S expose STDERR, which prints straight to
        // the terminal running the server.
        if (defined('STDERR')) {
            fwrite(STDERR, $line.PHP_EOL);
        }

        Log::info($line);
    }
}
