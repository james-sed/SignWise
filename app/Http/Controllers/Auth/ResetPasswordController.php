<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WebsiteMail;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Validates a password reset token and, once valid, lets the user set a new
 * password. Tokens are single use and expire after TOKEN_TTL_MINUTES.
 */
class ResetPasswordController extends Controller
{
    public const TOKEN_TTL_MINUTES = 30;

    /**
     * Check that a token exists and has not expired. The token itself is never
     * echoed back in the response.
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $record = $this->findValidToken($request->input('token'));

        return response()->json([
            'valid' => $record !== null,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', new StrongPassword],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        $record = $this->findValidToken($request->input('token'));

        if (! $record) {
            return response()->json([
                'message' => 'This link has expired.',
                'reason' => 'token',
            ], 422);
        }

        $user = User::CheckEmailExist($record->email)->first();

        if (! $user) {
            DB::table('password_reset_tokens')->where('email', $record->email)->delete();

            return response()->json([
                'message' => 'This link has expired.',
                'reason' => 'token',
            ], 422);
        }

        // The User model's `hashed` cast takes care of hashing this value.
        $user->password = $request->input('password');
        // Rotate the remember token so "remember me" cookies are invalidated.
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Tokens are single use: remove it so the link cannot be reused.
        DB::table('password_reset_tokens')->where('email', $record->email)->delete();

        // Invalidate every existing session for this user.
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Tell the account owner their password changed. A delivery failure
        // must not change the response the user sees.
        try {
            Mail::to($user->email)->send(new WebsiteMail(
                'Your password was changed',
                'Your SignWise password was just changed. If this was not you, reset it again immediately.'
            ));
        } catch (\Throwable $e) {
            Log::warning('[password-reset] Failed to send password-changed email.', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        $this->logDevConfirmation($user->email);

        return response()->json([
            'message' => 'Password updated',
        ]);
    }

    /**
     * Return the reset record for a valid, unexpired token, or null.
     */
    private function findValidToken(string $token): ?object
    {
        $record = DB::table('password_reset_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        if (! $record || ! $record->created_at) {
            return null;
        }

        if (Carbon::parse($record->created_at)->lt(now()->subMinutes(self::TOKEN_TTL_MINUTES))) {
            // Clean up the expired token so it can never be used.
            DB::table('password_reset_tokens')->where('email', $record->email)->delete();

            return null;
        }

        return $record;
    }

    private function logDevConfirmation(string $email): void
    {
        if (app()->environment('production')) {
            return;
        }

        $line = '[password-reset] Password changed for '.$email;

        if (defined('STDERR')) {
            fwrite(STDERR, $line.PHP_EOL);
        }

        Log::info($line);
    }
}
