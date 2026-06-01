<?php

namespace App\Http\Controllers\ApiMobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BrevoApi;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const OTP_MAX_ATTEMPTS = 5;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;

    private function canResendOtp(User $user): bool
    {
        if (!$user->otp_last_sent_at) {
            return true;
        }

        return $user->otp_last_sent_at->diffInSeconds(now()) >= self::OTP_RESEND_COOLDOWN_SECONDS;
    }

    private function refreshOtp(User $user): void
    {
        $user->otp_code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp_expires_at = now()->addMinutes(self::OTP_TTL_MINUTES);
        $user->otp_attempts = 0;
        $user->otp_last_sent_at = now();
        $user->save();
    }

    /**
     * User Login API for Mobile
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Support login by email, username, or phone
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->orWhere('phone', ltrim($request->email, '0'))
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if ($user->status === 'Not-Active') {
            if ($this->canResendOtp($user)) {
                $this->sendOtpToUser($user, true);
            }
            return response()->json([
                'status' => 'inactive',
                'message' => 'Account is not verified. Please use your latest OTP code sent to WhatsApp.',
                'user_id' => $user->id
            ], 403);
        }

        // Generate api_key if empty
        if (empty($user->api_key)) {
            $user->api_key = 'usr_' . Str::random(40);
        }

        // Set connection type to Mobile
        $user->koneksi = 'Mobile';
        $user->last_login = now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'balance' => $user->balance,
                'level' => $user->level,
                'status' => $user->status,
                'koneksi' => $user->koneksi,
                'api_key' => $user->api_key
            ]
        ]);
    }

    /**
     * Mobile Registration Step 1: Input Email and Send OTP
     */
    public function registerStep1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Domain restriction validation (Gmail, Yahoo, Outlook)
        $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'outlook.co.id'];
        $emailDomain = substr(strrchr($request->email, "@"), 1);
        
        if (!in_array(strtolower($emailDomain), $allowedDomains)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email only allowed from Gmail, Yahoo, or Outlook.'
            ], 422);
        }

        $emailOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Check Brevo status
        $brevo = BrevoApi::where('status', 'Active')->first();
        $brevoReady = $brevo && !empty($brevo->api_config['api_key']) && !empty($brevo->api_config['sender_email']);

        if ($brevoReady) {
            $this->sendEmailOtp($request->email, $emailOtp);
        } else {
            Log::warning('Brevo API is not configured or not active during mobile registration step 1. OTP: ' . $emailOtp);
        }

        // Encrypt step 1 data statelessly
        $otpToken = Crypt::encrypt([
            'email' => $request->email,
            'email_otp' => $emailOtp,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
            'brevo_ready' => $brevoReady
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $brevoReady ? 'Verification code sent to your email.' : 'Registration initialized (Brevo API inactive, check system logs).',
            'otp_token' => $otpToken,
            'brevo_ready' => $brevoReady
        ]);
    }

    /**
     * Mobile Registration Resend Email OTP
     */
    public function registerResendEmailOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Domain restriction validation
        $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'outlook.co.id'];
        $emailDomain = substr(strrchr($request->email, "@"), 1);
        
        if (!in_array(strtolower($emailDomain), $allowedDomains)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email only allowed from Gmail, Yahoo, or Outlook.'
            ], 422);
        }

        $emailOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $brevo = BrevoApi::where('status', 'Active')->first();
        $brevoReady = $brevo && !empty($brevo->api_config['api_key']) && !empty($brevo->api_config['sender_email']);

        if ($brevoReady) {
            $this->sendEmailOtp($request->email, $emailOtp);
        } else {
            Log::warning('Brevo API is not active for mobile resend. OTP: ' . $emailOtp);
        }

        $otpToken = Crypt::encrypt([
            'email' => $request->email,
            'email_otp' => $emailOtp,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
            'brevo_ready' => $brevoReady
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'New email OTP has been sent.',
            'otp_token' => $otpToken
        ]);
    }

    /**
     * Mobile Registration Verify Email OTP
     */
    public function registerVerifyEmailOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_token' => 'required|string',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $payload = Crypt::decrypt($request->otp_token);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP token.'
            ], 400);
        }

        if (now()->timestamp > $payload['otp_expires_at']) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP code expired. Please request a new OTP.'
            ], 400);
        }

        if ($request->otp_code !== $payload['email_otp']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email OTP.'
            ], 400);
        }

        // Generate email token certifying verification success
        $emailToken = Crypt::encrypt([
            'email' => $payload['email'],
            'email_verified' => true,
            'expires_at' => now()->addMinutes(20)->timestamp
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully.',
            'email_token' => $emailToken
        ]);
    }

    /**
     * Mobile Registration Step 2: Complete Profile Data
     */
    public function registerStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_token' => 'required|string',
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $payload = Crypt::decrypt($request->email_token);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or tampered email token.'
            ], 400);
        }

        if (!isset($payload['email_verified']) || !$payload['email_verified']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email verification required.'
            ], 400);
        }

        if (now()->timestamp > $payload['expires_at']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Verification token expired. Please verify your email again.'
            ], 400);
        }

        // Format phone (remove leading 0)
        $phone = ltrim($request->phone, '0');

        // Check if email was somehow registered while user was filling step 2
        $emailExists = User::where('email', $payload['email'])->exists();
        if ($emailExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'This email address has already been registered.'
            ], 409);
        }

        // Generate immediate API key
        $apiKey = 'usr_' . Str::random(40);

        // Create active user with koneksi set to Mobile
        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $payload['email'],
            'country_code' => $request->country_code,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'level' => 'Member',
            'status' => 'Active',
            'koneksi' => 'Mobile',
            'api_key' => $apiKey
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful.',
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'balance' => $user->balance,
                'level' => $user->level,
                'status' => $user->status,
                'koneksi' => $user->koneksi,
                'api_key' => $user->api_key
            ]
        ], 201);
    }

    /**
     * Send Email OTP Helper using Brevo API
     */
    private function sendEmailOtp($email, $otp)
    {
        $brevo = BrevoApi::where('status', 'Active')->first();
        
        if (!$brevo) {
            Log::warning('Brevo API not active, skipping email OTP');
            return;
        }

        $apiKey = $brevo->api_config['api_key'] ?? null;
        $senderEmail = $brevo->api_config['sender_email'] ?? null;
        
        if (!$apiKey || !$senderEmail) {
            Log::warning('Brevo API configurations missing');
            return;
        }

        $siteName = config('app.name');
        try {
            $setting = \App\Models\Setting::first();
            if ($setting && $setting->site_name) {
                $siteName = $setting->site_name;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get site name from database');
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => ['name' => $siteName, 'email' => $senderEmail],
                'to' => [['email' => $email]],
                'subject' => 'Your Email Verification OTP',
                'htmlContent' => "<h1>Verification Code</h1><p>Your OTP code is: <strong>{$otp}</strong></p><p>This code is valid for 10 minutes.</p>",
            ]);

            Log::info('Email OTP sent to ' . $email . ': ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Failed to send email OTP: ' . $e->getMessage());
        }
    }

    /**
     * Request Forget Password OTP API for Mobile
     */
    public function forget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Support email, username, or phone
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->orWhere('phone', ltrim($request->email, '0'))
            ->first();

        if ($user) {
            $this->refreshOtp($user);

            $message = "🔐 *SMM Panel Password Reset OTP*\n\n";
            $message .= "Your password reset OTP code: *{$user->otp_code}*\n\n";
            $message .= "Please use this code to reset your password. Do not share it with anyone.";

            $fullPhone = $user->country_code . $user->phone;
            WhatsAppService::sendOtp($fullPhone, $message);

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset verification code has been sent to WhatsApp.',
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No account found with the provided information.'
        ], 404);
    }

    /**
     * Reset Password using OTP API for Mobile
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string', // Support email, username, phone or user_id (if numeric)
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Support search by user_id or email/username/phone
        if (is_numeric($request->email)) {
            $user = User::find($request->email);
        } else {
            $user = null;
        }

        if (!$user) {
            $user = User::where('email', $request->email)
                ->orWhere('username', $request->email)
                ->orWhere('phone', ltrim($request->email, '0'))
                ->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reset OTP code expired.'
            ], 400);
        }

        if (($user->otp_attempts ?? 0) >= self::OTP_MAX_ATTEMPTS) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many OTP attempts. Please request a new reset code.'
            ], 429);
        }

        if ($user->otp_code !== $request->otp_code) {
            $user->otp_attempts = ($user->otp_attempts ?? 0) + 1;
            $user->save();

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid reset OTP code.'
            ], 400);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->otp_last_sent_at = null;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Your password has been successfully reset.'
        ]);
    }

    /**
     * Verify Registration/Login OTP API for Mobile
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user || $user->status !== 'Not-Active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or already verified account.'
            ], 400);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP code expired. Please request a new OTP.'
            ], 400);
        }

        if (($user->otp_attempts ?? 0) >= self::OTP_MAX_ATTEMPTS) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many OTP attempts. Please request a new OTP.'
            ], 429);
        }

        if ($user->otp_code !== $request->otp_code) {
            $user->otp_attempts = ($user->otp_attempts ?? 0) + 1;
            $user->save();

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code.'
            ], 400);
        }

        // Activate user
        $user->status = 'Active';
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->otp_last_sent_at = null;
        $user->koneksi = 'Mobile'; // Set connection to Mobile
        
        if (empty($user->api_key)) {
            $user->api_key = 'usr_' . Str::random(40);
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Account successfully verified and activated.',
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'balance' => $user->balance,
                'level' => $user->level,
                'status' => $user->status,
                'koneksi' => $user->koneksi,
                'api_key' => $user->api_key
            ]
        ]);
    }

    /**
     * Resend OTP API for Mobile
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user || $user->status !== 'Not-Active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ], 400);
        }

        if (!$this->canResendOtp($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please wait before requesting another OTP.'
            ], 429);
        }

        $this->sendOtpToUser($user, true);

        return response()->json([
            'status' => 'success',
            'message' => 'A new OTP code has been sent to your WhatsApp.'
        ]);
    }

    /**
     * Send OTP Helper
     */
    protected function sendOtpToUser(User $user, bool $forceRegenerate = false)
    {
        if ($forceRegenerate || !$user->otp_code || !$user->otp_expires_at || $user->otp_expires_at->isPast()) {
            $this->refreshOtp($user);
        }

        $message = "🔐 *SMM Panel Verification Code*\n\n";
        $message .= "Your OTP Code: *{$user->otp_code}*\n\n";
        $message .= "This code is valid for 10 minutes.\n";
        $message .= "Do not share this code with anyone.";

        $fullPhone = $user->country_code . $user->phone;
        return WhatsAppService::sendOtp($fullPhone, $message);
    }
}
