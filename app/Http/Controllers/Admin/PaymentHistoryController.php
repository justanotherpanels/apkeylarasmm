<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\HistoryDeposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class PaymentHistoryController extends Controller
{
    private function logError($message, $context = [])
    {
        try {
            $logPath = storage_path('logs/error.log');
            $timestamp = now()->toDateTimeString();
            $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
            $formattedMessage = "[{$timestamp}] ADMIN PAYMENT SYNC ERROR: {$message}{$contextStr}" . PHP_EOL;
            file_put_contents($logPath, $formattedMessage, FILE_APPEND);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to write to error.log: " . $e->getMessage() . " | Original Error: " . $message);
        }
    }

    public function index()
    {
        $deposits = HistoryDeposit::with('user')->orderBy('id', 'desc')->get();
        return view('admin.payment.history.index', compact('deposits'));
    }

    public function syncAll()
    {
        $pendingDeposits = HistoryDeposit::where('status_payment', 'Pending')->get();
        $syncedCount = 0;
        $successCount = 0;

        foreach ($pendingDeposits as $deposit) {
            $result = $this->syncSingleDeposit($deposit);
            if ($result['synced']) {
                $syncedCount++;
                if ($result['success']) {
                    $successCount++;
                }
            }
        }

        return redirect()->back()->with('success', "Sync completed! {$syncedCount} pending deposits checked, {$successCount} successful.");
    }

    private function syncSingleDeposit($deposit)
    {
        $method = strtolower($deposit->detail_transaction['payment_method'] ?? '');
        $synced = false;
        $success = false;

        if ($method === 'paypal') {
            $result = $this->syncPayPalDeposit($deposit);
            $synced = $result['synced'];
            $success = $result['success'];
        } elseif ($method === 'cryptomus') {
            $result = $this->syncCryptomusDeposit($deposit);
            $synced = $result['synced'];
            $success = $result['success'];
        }

        return ['synced' => $synced, 'success' => $success];
    }

    private function syncPayPalDeposit($deposit)
    {
        $synced = false;
        $success = false;

        $gateway = PaymentGateway::where('type', 'Paypal')->first();
        if (!$gateway || empty($gateway->api_config['client_id'])) {
            $this->logError("PayPal gateway not configured for deposit #{$deposit->invoice}");
            return ['synced' => $synced, 'success' => $success];
        }

        $config = $gateway->api_config;
        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];
        $mode = $config['mode'] ?? 'sandbox';
        $url = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        $paypalOrderId = $deposit->detail_transaction['paypal_order_id'] ?? null;

        if (!$paypalOrderId) {
            $this->logError("PayPal Order ID missing for deposit #{$deposit->invoice}");
            return ['synced' => $synced, 'success' => $success];
        }

        try {
            $tokenResponse = Http::external()
                ->asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post("{$url}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials'
                ]);

            if ($tokenResponse->failed()) {
                $this->logError("PayPal Token Request failed for deposit #{$deposit->invoice}");
                return ['synced' => $synced, 'success' => $success];
            }

            $accessToken = $tokenResponse->json()['access_token'];

            $orderResponse = Http::external()
                ->withToken($accessToken)
                ->get("{$url}/v2/checkout/orders/{$paypalOrderId}");

            if ($orderResponse->failed()) {
                $this->logError("PayPal Order Details failed for deposit #{$deposit->invoice}");
                return ['synced' => $synced, 'success' => $success];
            }

            $orderData = $orderResponse->json();
            $paypalStatus = $orderData['status'] ?? '';
            $synced = true;

            if ($paypalStatus === 'COMPLETED') {
                DB::transaction(function () use ($deposit, $orderData) {
                    $user = User::findOrFail($deposit->id_user);
                    $user->balance += $deposit->amount;
                    $user->save();

                    $deposit->update([
                        'status_payment' => 'Success',
                        'detail_transaction' => array_merge($deposit->detail_transaction, [
                            'capture_details' => $orderData
                        ])
                    ]);
                });
                $success = true;
            } elseif ($paypalStatus === 'APPROVED') {
                $captureResponse = Http::external()
                    ->withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$url}/v2/checkout/orders/{$paypalOrderId}/capture");

                if ($captureResponse->successful()) {
                    $captureData = $captureResponse->json();
                    if (($captureData['status'] ?? '') === 'COMPLETED') {
                        DB::transaction(function () use ($deposit, $captureData) {
                            $user = User::findOrFail($deposit->id_user);
                            $user->balance += $deposit->amount;
                            $user->save();

                            $deposit->update([
                                'status_payment' => 'Success',
                                'detail_transaction' => array_merge($deposit->detail_transaction, [
                                    'capture_details' => $captureData
                                ])
                            ]);
                        });
                        $success = true;
                    }
                }
            } elseif (in_array($paypalStatus, ['VOIDED', 'EXPIRED'])) {
                $deposit->update(['status_payment' => 'Failed']);
            }
        } catch (\Exception $e) {
            $this->logError("Exception in syncPayPalDeposit #{$deposit->invoice}: " . $e->getMessage());
        }

        return ['synced' => $synced, 'success' => $success];
    }

    private function syncCryptomusDeposit($deposit)
    {
        $synced = false;
        $success = false;

        $gateway = PaymentGateway::where('type', 'Cryptomus')->first();
        if (!$gateway || empty($gateway->api_config['merchant_id'])) {
            $this->logError("Cryptomus gateway not configured for deposit #{$deposit->invoice}");
            return ['synced' => $synced, 'success' => $success];
        }

        $config = $gateway->api_config;
        $merchantId = $config['merchant_id'];
        $paymentKey = $config['payment_key'];

        try {
            $postData = [
                'order_id' => $deposit->invoice
            ];

            $jsonData = json_encode($postData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $sign = md5(base64_encode($jsonData) . $paymentKey);

            $response = Http::external()
                ->withHeaders([
                    'merchant' => $merchantId,
                    'sign' => $sign
                ])
                ->withBody($jsonData, 'application/json')
                ->post('https://api.cryptomus.com/v1/payment/info');

            if ($response->failed()) {
                $this->logError("Cryptomus API failed for deposit #{$deposit->invoice}");
                return ['synced' => $synced, 'success' => $success];
            }

            $resData = $response->json();
            $synced = true;

            if (isset($resData['result']['status'])) {
                $cryptoStatus = strtolower($resData['result']['status']);

                if ($cryptoStatus === 'paid' || $cryptoStatus === 'paid_over') {
                    DB::transaction(function () use ($deposit, $resData) {
                        $user = User::findOrFail($deposit->id_user);
                        $user->balance += $deposit->amount;
                        $user->save();

                        $deposit->update([
                            'status_payment' => 'Success',
                            'detail_transaction' => array_merge($deposit->detail_transaction ?? [], [
                                'callback_details' => $resData['result']
                            ])
                        ]);
                    });
                    $success = true;
                } elseif (in_array($cryptoStatus, ['cancel', 'system_fail', 'fail'])) {
                    $deposit->update([
                        'status_payment' => 'Cancel',
                        'detail_transaction' => array_merge($deposit->detail_transaction ?? [], [
                            'callback_details' => $resData['result']
                        ])
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->logError("Exception in syncCryptomusDeposit #{$deposit->invoice}: " . $e->getMessage());
        }

        return ['synced' => $synced, 'success' => $success];
    }
}
