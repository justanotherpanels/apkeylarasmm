<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use App\Models\WhatsappAccount;

class WhatsAppController extends Controller
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

    public function index()
    {
        $accounts = WhatsappAccount::orderBy('created_at', 'desc')->get();
        
        // Sync ALL accounts status with Node.js in real-time
        foreach ($accounts as $account) {
            $nodeStatus = WhatsAppService::getStatus($account->phone_number);
            $nodeStatusVal = $nodeStatus['status'] ?? null;

            if (!$nodeStatusVal) continue;

            // If Node says disconnected but DB says connected → update to disconnected
            if (in_array($nodeStatusVal, ['disconnected', 'offline']) && $account->status === 'connected') {
                $account->update(['status' => 'disconnected', 'pairing_code' => null]);
            }
            // If Node says connected but DB doesn't → update to connected
            elseif ($nodeStatusVal === 'connected' && $account->status !== 'connected') {
                $account->update(['status' => 'connected', 'pairing_code' => null]);
            }
        }

        // Re-fetch after updates
        $accounts = WhatsappAccount::orderBy('created_at', 'desc')->get();

        return view('admin.whatsapp.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:Bot,Sender OTP',
            'country_code' => 'required|numeric',
            'phone_number' => 'required|numeric'
        ]);

        $fullNumber = $request->country_code . ltrim($request->phone_number, '0');
        
        // Check if exists
        $account = WhatsappAccount::where('phone_number', $fullNumber)->first();
        if ($account) {
            return redirect()->back()->with('error', 'Nomor WhatsApp sudah ada di daftar.');
        }

        WhatsappAccount::create([
            'phone_number' => $fullNumber,
            'type' => $request->type,
            'status' => 'disconnected'
        ]);

        return redirect()->back()->with('success', 'Berhasil menambahkan nomor WhatsApp ke daftar.');
    }

    public function scan($id)
    {
        $account = WhatsappAccount::findOrFail($id);

        if ($account->status == 'connected') {
            // Double-check with Node.js before redirecting
            $nodeStatus = WhatsAppService::getStatus($account->phone_number);
            if (($nodeStatus['status'] ?? '') !== 'connected') {
                $account->update(['status' => 'disconnected']);
            } else {
                return redirect()->route('admin.whatsapp.index')->with('success', 'Akun WhatsApp sudah terhubung.');
            }
        }

        // If Node.js session not running, start it
        $nodeStatus = WhatsAppService::getStatus($account->phone_number);
        $nodeStatusVal = $nodeStatus['status'] ?? 'offline';

        if (in_array($nodeStatusVal, ['disconnected', 'offline'])) {
            WhatsAppService::startSession($account->phone_number);
            $account->update(['status' => 'pairing_ready']);
        } elseif ($nodeStatusVal === 'connected') {
            $account->update(['status' => 'connected', 'pairing_code' => null]);
            return redirect()->route('admin.whatsapp.index')->with('success', 'WhatsApp berhasil terhubung!');
        } else {
            // qr_ready or pairing — update status in DB
            $account->update(['status' => 'pairing_ready']);
        }

        return view('admin.whatsapp.scan', compact('account'));
    }

    public function status($id)
    {
        $account = WhatsappAccount::findOrFail($id);
        $nodeStatus = WhatsAppService::getStatus($account->phone_number);
        
        if (($nodeStatus['status'] ?? '') == 'connected' && $account->status != 'connected') {
            $account->update(['status' => 'connected', 'pairing_code' => null]);
        }
        
        return response()->json([
            'status' => $nodeStatus['status'] ?? 'offline',
            'qr' => $nodeStatus['qr'] ?? null
        ]);
    }

    public function logout(Request $request)
    {
        $request->validate(['id' => 'required|exists:whatsapp_accounts,id']);
        $account = WhatsappAccount::find($request->id);

        $result = WhatsAppService::logout($account->phone_number);
        $account->delete();

        return redirect()->back()->with('success', 'Akun WhatsApp berhasil dihapus dan dilogout.');
    }

    public function testSend(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'message' => 'required',
            'sender' => 'nullable' // Session ID
        ]);

        $result = WhatsAppService::sendMessage($request->number, $request->message, $request->sender);

        if (($result['status'] ?? '') == 'success') {
            $usedSender = $result['sender'] ?? 'Unknown';
            return redirect()->back()->with('success', "Pesan test berhasil dikirim! (Menggunakan Sender: $usedSender)");
        }
        
        return redirect()->back()->with('error', 'Gagal mengirim pesan: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Webhook called by Node.js when a session is disconnected (logged out)
     */
    public function webhookDisconnect(Request $request)
    {
        if ($response = $this->verifyWebhookSecret($request)) {
            return $response;
        }

        $sessionId = $request->input('sessionId');
        if (!$sessionId) {
            return response()->json(['status' => 'error', 'message' => 'sessionId required'], 400);
        }

        $account = WhatsappAccount::where('phone_number', $sessionId)->first();
        if ($account) {
            $account->update(['status' => 'disconnected', 'pairing_code' => null]);
            \Log::info("WhatsApp account {$sessionId} disconnected via webhook.");
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Webhook called by Node.js when a session connects successfully
     */
    public function webhookConnected(Request $request)
    {
        if ($response = $this->verifyWebhookSecret($request)) {
            return $response;
        }

        $sessionId = $request->input('sessionId');
        if (!$sessionId) {
            return response()->json(['status' => 'error', 'message' => 'sessionId required'], 400);
        }

        $account = WhatsappAccount::where('phone_number', $sessionId)->first();
        if ($account) {
            $account->update(['status' => 'connected', 'pairing_code' => null]);
            \Log::info("WhatsApp account {$sessionId} connected via webhook.");
        }

        return response()->json(['status' => 'ok']);
    }
}
