<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        // Format phone number (remove leading 0 if exists)
        $phone = ltrim($request->phone, '0');

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'country_code' => $request->country_code,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'level' => 'Member',
            'status' => 'Not-Active',
        ]);

        // Send OTP via WhatsApp
        $this->sendOtpToUser($user, true);

        return redirect()->route('otp.show', ['user_id' => $user->id])
            ->with('success', 'Registrasi berhasil! Silakan masukkan kode OTP yang dikirim ke WhatsApp Anda.');
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
