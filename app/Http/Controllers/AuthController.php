<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BrevoApi;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check if user exists first
        $user = User::where('email', $request->email)->first();
        
        if ($user && $user->status === 'Not-Active') {
            // Resend OTP and redirect to verification
            if ($this->canResendOtp($user)) {
                $this->sendOtpToUser($user, true);
            }
            return redirect()->route('otp.show', ['user_id' => $user->id])
                ->with('info', 'Akun belum diverifikasi. Gunakan OTP terbaru yang Anda terima di WhatsApp.');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $user->last_login = now();
            $user->save();

            // Redirect based on user level
            if ($user->level === 'Admin') {
                return redirect()->intended(route('admin.index'));
            }

            return redirect()->intended(route('member.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function registerStep1(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'outlook.co.id'];
        $emailDomain = substr(strrchr($request->email, "@"), 1);
        
        if (!in_array($emailDomain, $allowedDomains)) {
            return back()->withErrors(['email' => 'Email only allowed from Gmail, Yahoo, or Outlook.'])->withInput();
        }

        $emailOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $brevo = BrevoApi::where('status', 'Active')->first();
        $brevoReady = $brevo && !empty($brevo->api_config['api_key']) && !empty($brevo->api_config['sender_email']);
        
        $request->session()->put('register_step1', [
            'email' => $request->email,
            'email_otp' => $emailOtp,
            'email_verified' => false,
            'otp_expires_at' => now()->addMinutes(10),
            'brevo_ready' => $brevoReady,
        ]);

        if ($brevoReady) {
            $this->sendEmailOtp($request->email, $emailOtp);
        }

        return view('auth.register', ['step' => 1, 'showVerification' => true, 'brevoReady' => $brevoReady, 'emailVerified' => false]);
    }

    public function verifyEmailOtp(Request $request)
    {
        $step1 = $request->session()->get('register_step1');
        
        if (!$step1) {
            return redirect()->route('register')->withErrors(['error' => 'Session expired. Please start over.']);
        }

        $error = null;
        if (now()->gt($step1['otp_expires_at'])) {
            $error = 'OTP expired. Please request new OTP.';
        }

        if (!$error && $request->email_otp !== $step1['email_otp']) {
            $error = 'Invalid email OTP.';
        }

        if (!$error) {
            $step1['email_verified'] = true;
            $request->session()->put('register_step1', $step1);

            return view('auth.register', ['step' => 2]);
        }

        return view('auth.register', [
            'step' => 1, 
            'showVerification' => true, 
            'brevoReady' => $step1['brevo_ready'] ?? false, 
            'emailVerified' => false,
            'errors' => (object) ['email_otp' => $error]
        ]);
    }

    public function verifyWhatsAppOtp(Request $request)
    {
        return redirect()->route('register')->withErrors(['error' => 'WhatsApp verification is not required.']);
    }

    public function resendEmailOtp(Request $request)
    {
        $step1 = $request->session()->get('register_step1');
        
        if (!$step1) {
            return redirect()->route('register')->withErrors(['error' => 'Session expired. Please start over.']);
        }

        $emailOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $step1['email_otp'] = $emailOtp;
        $step1['otp_expires_at'] = now()->addMinutes(10);
        $request->session()->put('register_step1', $step1);

        $this->sendEmailOtp($step1['email'], $emailOtp);

        return view('auth.register', [
            'step' => 1, 
            'showVerification' => true, 
            'brevoReady' => $step1['brevo_ready'] ?? false, 
            'emailVerified' => false,
            'success' => 'New email OTP has been sent.'
        ]);
    }

    public function resendWhatsAppOtp(Request $request)
    {
        return redirect()->route('register')->withErrors(['error' => 'WhatsApp verification is not required.']);
    }

    public function registerStep2(Request $request)
    {
        $step1 = $request->session()->get('register_step1');
        
        if (!$step1 || !$step1['email_verified']) {
            return redirect()->route('register')->withErrors(['error' => 'Please verify email first.']);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
        ]);

        $phone = ltrim($request->phone, '0');

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $step1['email'],
            'country_code' => $request->country_code,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'level' => 'Member',
            'status' => 'Active',
        ]);

        $request->session()->forget('register_step1');

        Auth::login($user);

        return redirect()->intended(route('member.index'))->with('success', 'Registration successful!');
    }

    private function sendEmailOtp($email, $otp)
    {
        $brevo = BrevoApi::where('status', 'Active')->first();
        
        if (!$brevo) {
            Log::warning('Brevo API not active, skipping email OTP');
            return;
        }

        $apiKey = $brevo->api_config['api_key'] ?? null;
        $senderEmail = $brevo->api_config['sender_email'] ?? null;
        
        if (!$apiKey) {
            Log::warning('Brevo API key not configured');
            return;
        }

        if (!$senderEmail) {
            Log::warning('Brevo sender email not configured');
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

    private function sendWhatsAppOtp($fullPhone, $otp)
    {
        $message = "🔐 *SMM Panel Verification Code*\n\n";
        $message .= "Your WhatsApp OTP Code: *{$otp}*\n\n";
        $message .= "This code is valid for 10 minutes.\n";
        $message .= "Do not share this code with anyone.";

        $result = WhatsAppService::sendOtp($fullPhone, $message);
        
        Log::info("WhatsApp OTP sent to {$fullPhone}: " . json_encode($result));
        
        return $result;
    }

    /**
     * Send OTP to user via WhatsApp
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

        // Combine country_code + phone for WhatsApp
        $fullPhone = $user->country_code . $user->phone;
        
        // Use Sender OTP type WhatsApp account
        $result = WhatsAppService::sendOtp($fullPhone, $message);
        
        Log::info("OTP sent to {$fullPhone}: " . json_encode($result));
        
        return $result;
    }

    /**
     * Show OTP verification form
     */
    public function showOtp(Request $request)
    {
        $userId = $request->query('user_id');
        $user = User::find($userId);

        if (!$user || $user->status !== 'Not-Active') {
            return redirect()->route('login')->withErrors(['error' => 'Invalid verification request.']);
        }

        return view('auth.otp', compact('user'));
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = User::find($request->user_id);

        if (!$user || $user->status !== 'Not-Active') {
            return redirect()->route('login')->withErrors(['error' => 'Invalid verification request.']);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp_code' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.']);
        }

        if (($user->otp_attempts ?? 0) >= self::OTP_MAX_ATTEMPTS) {
            return back()->withErrors(['otp_code' => 'Terlalu banyak percobaan OTP. Silakan kirim ulang OTP.']);
        }

        if ($user->otp_code !== $request->otp_code) {
            $user->otp_attempts = ($user->otp_attempts ?? 0) + 1;
            $user->save();
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid. Silakan coba lagi.']);
        }

        // Activate user
        $user->status = 'Active';
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->otp_last_sent_at = null;
        $user->save();

        // Login user
        Auth::login($user);

        // Redirect based on user level
        $redirectUrl = $user->level === 'Admin' ? route('admin.index') : route('member.index');

        return redirect($redirectUrl)->with('success', 'Selamat! Akun Anda telah diverifikasi dan aktif.');
    }

    /**
     * Resend OTP code
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if (!$user || $user->status !== 'Not-Active') {
            return redirect()->route('login')->withErrors(['error' => 'Invalid request.']);
        }

        if (!$this->canResendOtp($user)) {
            return back()->withErrors(['error' => 'Tunggu sebentar sebelum meminta OTP baru lagi.']);
        }

        // Send OTP
        $this->sendOtpToUser($user, true);

        return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Show forget password form
     */
    public function showForgetPassword()
    {
        return view('auth.forget');
    }

    /**
     * Send forget password OTP
     */
    public function sendForgetPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
        ]);

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
            \App\Services\WhatsAppService::sendOtp($fullPhone, $message);
        }

        return redirect()->route('password.reset', ['email' => $request->email])
            ->with('status', 'If your account exists, a password reset verification code has been sent.');
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset', compact('email'));
    }

    /**
     * Reset password using OTP
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find user by email, username, or phone
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->orWhere('phone', ltrim($request->email, '0'))
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid account.']);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp_code' => 'Reset OTP code expired.']);
        }

        if (($user->otp_attempts ?? 0) >= self::OTP_MAX_ATTEMPTS) {
            return back()->withErrors(['otp_code' => 'Too many OTP attempts. Please request a new reset code.']);
        }

        if ($user->otp_code !== $request->otp_code) {
            $user->otp_attempts = ($user->otp_attempts ?? 0) + 1;
            $user->save();
            return back()->withErrors(['otp_code' => 'Invalid reset OTP code.']);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->otp_last_sent_at = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Your password has been successfully reset. Please login with your new password.');
    }
}
