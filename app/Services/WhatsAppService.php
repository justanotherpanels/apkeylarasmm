<?php

namespace App\Services;

use App\Models\WhatsappAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private static function apiUrl(): string
    {
        return rtrim((string) config('services.whatsapp.server_url', 'http://localhost:3000'), '/');
    }

    private static function authHeaders(): array
    {
        $token = (string) config('services.whatsapp.server_token');

        if ($token === '') {
            return [];
        }

        return [
            'X-Server-Token' => $token,
        ];
    }

    /**
     * Get connected WhatsApp account by type
     * 
     * @param string $type 'Bot' or 'Sender OTP'
     * @return WhatsappAccount|null
     */
    public static function getAccountByType($type)
    {
        $account = WhatsappAccount::where('type', $type)
            ->where('status', 'connected')
            ->inRandomOrder()
            ->first();

        if (!$account && $type === 'Sender OTP') {
            $account = WhatsappAccount::where('status', 'connected')
                ->inRandomOrder()
                ->first();
        }

        return $account;
    }

    /**
     * Send OTP message using Sender OTP type WhatsApp account
     * 
     * @param string $number Target phone number
     * @param string $message OTP message
     * @return array
     */
    public static function sendOtp($number, $message)
    {
        $otpSender = self::getAccountByType('Sender OTP');
        
        if (!$otpSender) {
            Log::error('WhatsApp OTP Sender not found or not connected');
            return ['status' => 'error', 'message' => 'No OTP sender available'];
        }

        return self::sendMessage($number, $message, $otpSender->phone_number);
    }

    /**
     * Check if a specific WhatsApp session is connected
     */
    public static function getStatus($sessionId)
    {
        $t0 = microtime(true);
        Log::debug("[WA DEBUG] getStatus START sessionId={$sessionId}");
        try {
            $response = Http::timeout(5)
                ->withHeaders(self::authHeaders())
                ->get(self::apiUrl() . '/session/status', [
                'sessionId' => $sessionId
                ]);
            $json = $response->json();
            $elapsed = round((microtime(true) - $t0) * 1000);
            $qrLength = isset($json['qr']) ? strlen($json['qr']) : 0;
            Log::debug("[WA DEBUG] getStatus DONE sessionId={$sessionId} elapsed={$elapsed}ms status={$json['status']} qrLength={$qrLength}");
            return $json;
        } catch (\Exception $e) {
            Log::error("[WA DEBUG] getStatus ERROR sessionId={$sessionId} message=" . $e->getMessage());
            return ['status' => 'offline', 'message' => 'Node.js Server is down'];
        }
    }

    /**
     * Start session and get QR Code
     * @param string $number
     */
    public static function startSession($number)
    {
        $t0 = microtime(true);
        Log::debug("[WA DEBUG] startSession START number={$number}");
        try {
            $response = Http::timeout(10)
                ->withHeaders(self::authHeaders())
                ->post(self::apiUrl() . '/session/start', [
                'number' => $number
                ]);
            $json = $response->json();
            $elapsed = round((microtime(true) - $t0) * 1000);
            Log::debug("[WA DEBUG] startSession DONE number={$number} elapsed={$elapsed}ms response=" . json_encode($json));
            return $json;
        } catch (\Exception $e) {
            Log::error("[WA DEBUG] startSession ERROR number={$number} message=" . $e->getMessage());
            return ['status' => 'offline', 'message' => 'Node.js Server is down'];
        }
    }

    /**
     * Send a WhatsApp message
     * 
     * @param string $number Target phone number
     * @param string $message Text message
     * @param string|null $sessionId Specific sender session ID. If null, node will pick available sender.
     * @return array
     */
    public static function sendMessage($number, $message, $sessionId = null)
    {
        try {
            $payload = [
                'number' => $number,
                'message' => $message
            ];
            
            if ($sessionId) {
                $payload['sessionId'] = $sessionId;
            }

            $response = Http::timeout(10)
                ->withHeaders(self::authHeaders())
                ->post(self::apiUrl() . '/session/send', $payload);
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to connect to WhatsApp server'];
        }
    }

    /**
     * Logout specific WhatsApp session
     */
    public static function logout($sessionId)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(self::authHeaders())
                ->post(self::apiUrl() . '/session/logout', [
                'sessionId' => $sessionId
                ]);
            return $response->json();
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to connect to WhatsApp server'];
        }
    }
}
