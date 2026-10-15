<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

/**
 * Enforces the password policy used by the reset flow:
 *  - at least 8 characters
 *  - at most 128 characters
 *  - not a well known / common password
 *  - not found in the Have I Been Pwned breach corpus
 *
 * The breach lookup fails open: if the network is unavailable we do not block
 * the user, we simply fall back to the local checks.
 */
class StrongPassword implements ValidationRule
{
    public const MIN_LENGTH = 8;

    public const MAX_LENGTH = 128;

    /**
     * A small local list of very common passwords. This lets us reject the
     * worst offenders even when the breach API cannot be reached.
     *
     * @var array<int, string>
     */
    private const COMMON_PASSWORDS = [
        '123456',
        '12345678',
        '123456789',
        '1234567890',
        'password',
        'password1',
        'password123',
        'qwerty',
        'qwerty123',
        '111111',
        '123123',
        'abc123',
        'letmein',
        'welcome',
        'welcome1',
        'iloveyou',
        'admin',
        'admin123',
        'monkey',
        'dragon',
        'football',
        'baseball',
        'sunshine',
        'princess',
        'master',
        'shadow',
        'superman',
        'trustno1',
        'changeme',
        'secret',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid string.');

            return;
        }

        if (mb_strlen($value) < self::MIN_LENGTH) {
            $fail('The :attribute must be at least '.self::MIN_LENGTH.' characters.');

            return;
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            $fail('The :attribute must not be greater than '.self::MAX_LENGTH.' characters.');

            return;
        }

        if (in_array(mb_strtolower($value), self::COMMON_PASSWORDS, true)) {
            $fail('This password is too common. Please choose a different password.');

            return;
        }

        if ($this->isBreached($value)) {
            $fail('This password has appeared in a data breach. Please choose a different password.');
        }
    }

    private function isBreached(string $value): bool
    {
        $hash = strtoupper(sha1($value));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);

        try {
            $response = Http::timeout(3)
                ->withUserAgent('SignWise-Password-Reset')
                ->get("https://api.pwnedpasswords.com/range/{$prefix}");
        } catch (\Throwable $e) {
            // Network/API unavailable - fail open.
            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        foreach (preg_split('/\r\n|\r|\n/', $response->body()) as $line) {
            if (str_starts_with(trim($line), $suffix.':')) {
                return true;
            }
        }

        return false;
    }
}
