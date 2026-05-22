<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    public function settings()
    {
        $cryptomus = PaymentGateway::firstOrCreate(
            ['type' => 'Cryptomus'],
            ['api_config' => ['merchant_id' => '', 'payment_key' => '', 'fee_bearer' => 'user']]
        );

        $paypal = PaymentGateway::firstOrCreate(
            ['type' => 'Paypal'],
            ['api_config' => ['client_id' => '', 'client_secret' => '', 'mode' => 'sandbox', 'fee_bearer' => 'user']]
        );

        return view('admin.payment.settings.index', compact('cryptomus', 'paypal'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cryptomus_merchant_id' => 'nullable|string',
            'cryptomus_payment_key' => 'nullable|string',
            'cryptomus_fee_bearer' => 'required|in:admin,user',
            'paypal_client_id' => 'nullable|string',
            'paypal_client_secret' => 'nullable|string',
            'paypal_mode' => 'required|in:sandbox,live',
            'paypal_fee_bearer' => 'required|in:admin,user',
        ]);

        PaymentGateway::where('type', 'Cryptomus')->update([
            'api_config' => json_encode([
                'merchant_id' => $request->cryptomus_merchant_id,
                'payment_key' => $request->cryptomus_payment_key,
                'fee_bearer' => $request->cryptomus_fee_bearer,
            ])
        ]);

        PaymentGateway::where('type', 'Paypal')->update([
            'api_config' => json_encode([
                'client_id' => $request->paypal_client_id,
                'client_secret' => $request->paypal_client_secret,
                'mode' => $request->paypal_mode,
                'fee_bearer' => $request->paypal_fee_bearer,
            ])
        ]);

        return redirect()->back()->with('success', 'Pengaturan Payment Gateway berhasil disimpan!');
    }
}
