<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WhatsAppBotService;
use Illuminate\Support\Facades\Log;

class WhatsAppBotController extends Controller
{
    private function verifyWebhookSecret(Request $request)
    {
        $expectedSecret = (string) config('services.whatsapp.webhook_secret');

        if ($expectedSecret === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Webhook secret is not configured',
            ], 503);
        }

        $receivedSecret = (string) $request->header('X-Webhook-Secret', '');
        if (!hash_equals($expectedSecret, $receivedSecret)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return null;
    }

    /**
     * Webhook to receive incoming WhatsApp messages
     */
    public function webhook(Request $request)
    {
        if ($response = $this->verifyWebhookSecret($request)) {
            return $response;
        }

        try {
            Log::info('WhatsApp Bot Webhook: ' . json_encode($request->all()));
            
            // Validate required fields
            if (!$request->has(['sender', 'message'])) {
                return response()->json(['status' => 'error', 'message' => 'Missing required fields'], 400);
            }

            $sender = $request->input('sender');
            $message = $request->input('message');

            // Process the message
            WhatsAppBotService::handleMessage($sender, $message);

            return response()->json(['status' => 'success', 'message' => 'Message processed']);

        } catch (\Exception $e) {
            Log::error('WhatsApp Bot Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }
}
