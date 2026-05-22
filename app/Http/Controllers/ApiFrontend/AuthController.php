<?php

namespace App\Http\Controllers\ApiFrontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
     * User Login API
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

        // Support login by email or username
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if ($user->status === 'Not-Active') {
            // Send new OTP
            if ($this->canResendOtp($user)) {
                $this->sendOtpToUser($user, true);
            }
            return response()->json([
                'status' => 'inactive',
                'message' => 'Account is not verified. Please use your latest OTP code.',
                'user_id' => $user->id
            ], 403);
        }

        // Ensure api_key is generated
        if (empty($user->api_key)) {
            $user->api_key = 'usr_' . Str::random(40);
        }

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
     * User Register API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Format phone (remove leading 0)
        $phone = ltrim($request->phone, '0');

        // Generate a new API key immediately
        $apiKey = 'usr_' . Str::random(40);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'country_code' => $request->country_code,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'level' => 'Member',
            'status' => 'Not-Active',
            'koneksi' => 'API', // Set to API as requested
            'api_key' => $apiKey,
        ]);

        // Send OTP via WhatsApp
        $this->sendOtpToUser($user, true);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful! Verification code sent to WhatsApp.',
            'user_id' => $user->id
        ], 201);
    }

    /**
     * Verify Registration/Login OTP API
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
     * Resend OTP API
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
     * Forget Password API
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
        }

        return response()->json([
            'status' => 'success',
            'message' => 'If your account exists, a password reset verification code has been sent.'
        ]);
    }

    /**
     * Reset Password using OTP API
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid reset OTP code.'
            ], 400);
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
