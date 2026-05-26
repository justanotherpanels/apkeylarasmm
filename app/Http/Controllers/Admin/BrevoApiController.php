<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BrevoApi;

class BrevoApiController extends Controller
{
    public function index()
    {
        $brevo = BrevoApi::where('id', 1)->first();
        
        if (!$brevo) {
            $brevo = BrevoApi::create([
                'id' => 1,
                'status' => 'Not-Active',
                'api_config' => ['api_key' => '', 'sender_email' => '']
            ]);
        } else {
            $apiConfig = $brevo->api_config;
            if (!isset($apiConfig['sender_email'])) {
                $apiConfig['sender_email'] = '';
                $brevo->api_config = $apiConfig;
                $brevo->save();
            }
        }

        return view('admin.brevo-api.index', compact('brevo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Active,Not-Active',
            'api_key' => 'nullable|string',
            'sender_email' => 'nullable|email',
        ]);

        BrevoApi::where('id', 1)->update([
            'status' => $request->status,
            'api_config' => json_encode([
                'api_key' => $request->api_key,
                'sender_email' => $request->sender_email,
            ])
        ]);

        return redirect()->back()->with('success', 'Brevo API settings saved successfully!');
    }
}
