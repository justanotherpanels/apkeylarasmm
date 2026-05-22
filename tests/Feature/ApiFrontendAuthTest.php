<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\OrderSmm;
use App\Models\HistoryDeposit;
use App\Models\CategorySmm;
use App\Models\ServiceSmm;
use App\Models\ApiSmm;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiFrontendAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration.
     */
    public function test_api_registration_succeeds_and_sets_koneksi_to_api(): void
    {
        $response = $this->postJson(route('api-frontend.register'), [
            'full_name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'country_code' => '62',
            'phone' => '8123456789',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'user_id'
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'koneksi' => 'API',
            'status' => 'Not-Active',
        ]);
    }

    /**
     * Test OTP verification.
     */
    public function test_api_otp_verification_activates_user(): void
    {
        $user = User::factory()->create([
            'status' => 'Not-Active',
            'otp_code' => '123456',
            'koneksi' => 'API'
        ]);

        $response = $this->postJson(route('api-frontend.verify_otp'), [
            'user_id' => $user->id,
            'otp_code' => '123456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Account successfully verified and activated.'
        ]);

        $user->refresh();
        $this->assertEquals('Active', $user->status);
        $this->assertNull($user->otp_code);
        $this->assertNotEmpty($user->api_key);
    }

    /**
     * Test successful login.
     */
    public function test_api_login_returns_api_key(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'Active',
            'api_key' => 'usr_testapikey123456789'
        ]);

        $response = $this->postJson(route('api-frontend.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'user' => [
                'email' => 'test@example.com',
                'api_key' => 'usr_testapikey123456789'
            ]
        ]);
    }

    /**
     * Test API Frontend Dashboard metrics endpoint.
     */
    public function test_api_dashboard_endpoint_returns_metrics_and_charts(): void
    {
        // 1. Missing API key should return 401
        $responseMissing = $this->postJson(route('api-frontend.dashboard'));
        $responseMissing->assertStatus(401);
        $responseMissing->assertJson([
            'status' => 'error',
            'message' => 'API key is missing.'
        ]);

        // 2. Invalid API key should return 401
        $responseInvalid = $this->postJson(route('api-frontend.dashboard'), [
            'api_key' => 'invalid_key'
        ]);
        $responseInvalid->assertStatus(401);

        // 3. Setup user, SMM orders, and deposits
        $user = User::factory()->create([
            'status' => 'Active',
            'api_key' => 'usr_dashboardtest123456',
            'balance' => 15000.50
        ]);

        // Insert some orders
        OrderSmm::create([
            'id_user' => $user->id,
            'invoice' => 'INV-SMM-1',
            'target' => 'https://instagram.com/p/1',
            'amount' => 100,
            'price_sale' => 10.50,
            'status_order' => 'Success',
            'create_at' => now()->format('Y-m-d H:i:s'),
        ]);

        OrderSmm::create([
            'id_user' => $user->id,
            'invoice' => 'INV-SMM-2',
            'target' => 'https://instagram.com/p/2',
            'amount' => 200,
            'price_sale' => 20.00,
            'status_order' => 'Pending',
            'create_at' => now()->format('Y-m-d H:i:s'),
        ]);

        // Insert some deposits
        HistoryDeposit::create([
            'id_user' => $user->id,
            'invoice' => 'INV-DEP-1',
            'amount' => 500.00,
            'status_payment' => 'Success',
            'create_at' => now()->format('Y-m-d H:i:s'),
        ]);

        HistoryDeposit::create([
            'id_user' => $user->id,
            'invoice' => 'INV-DEP-2',
            'amount' => 200.00,
            'status_payment' => 'Pending',
            'create_at' => now()->format('Y-m-d H:i:s'),
        ]);

        // Test with parameter api_key
        $response = $this->postJson(route('api-frontend.dashboard'), [
            'api_key' => 'usr_dashboardtest123456'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'balance' => '15000.5000',
                ],
                'metrics' => [
                    'total_order' => 2,
                    'total_pending' => 1,
                    'total_success' => 1,
                    'total_deposit' => '500.00',
                ]
            ]
        ]);

        $response->assertJsonStructure([
            'data' => [
                'recent_transactions',
                'recent_deposits',
                'chart_data'
            ]
        ]);

        // Test with Authorization Bearer header
        $responseHeader = $this->withHeaders([
            'Authorization' => 'Bearer usr_dashboardtest123456'
        ])->postJson(route('api-frontend.dashboard'));

        $responseHeader->assertStatus(200);
        $responseHeader->assertJsonPath('data.metrics.total_order', 2);
    }

    /**
     * Test SMM Order API placement and validations.
     */
    public function test_api_order_placement_and_validation(): void
    {
        // 1. Setup user
        $user = User::factory()->create([
            'status' => 'Active',
            'api_key' => 'usr_ordertest123456',
            'balance' => 100.00
        ]);

        // 2. Setup Category, SMM Provider API, and Service SMM
        $category = CategorySmm::create([
            'name' => 'Instagram Services',
            'code' => 'IG',
            'status' => 'Active'
        ]);

        $apiProvider = ApiSmm::create([
            'name' => 'Provider X',
            'code' => 'PROV_X',
            'balance' => 500.00,
            'url' => 'https://api.provider.com/v2',
            'api_key' => 'provider_api_key_abc',
            'status' => 'Active'
        ]);

        $service = ServiceSmm::create([
            'id_category_smm' => $category->id,
            'id_api_smm' => $apiProvider->id,
            'name_service' => 'Instagram Likes',
            'pid' => '101',
            'min_order' => 10,
            'max_order' => 1000,
            'price_api' => 0.50,
            'price_sale' => 1.50, // USD 1.50 per 1000 likes
            'price_reseller' => 1.00,
            'type' => 'Default',
            'status' => 'Active',
            'desc' => 'Test description'
        ]);

        // Mock Http requests to SMM provider API
        Http::fake([
            'https://api.provider.com/v2' => Http::response([
                'status' => 'success',
                'order' => '998877'
            ], 200)
        ]);

        // 3. Test parameter and validation missing / invalid key
        $responseUnauth = $this->postJson(route('api-frontend.order.store'), [
            'api_key' => 'invalid_key',
            'id_category_smm' => $category->id,
            'id_service_smm' => $service->id,
            'target' => 'https://instagram.com/p/1',
            'amount' => 100
        ]);
        $responseUnauth->assertStatus(401);

        // 4. Test validation error (missing amount)
        $responseValErr = $this->postJson(route('api-frontend.order.store'), [
            'api_key' => 'usr_ordertest123456',
            'id_category_smm' => $category->id,
            'id_service_smm' => $service->id,
            'target' => 'https://instagram.com/p/1'
        ]);
        $responseValErr->assertStatus(422);
        $responseValErr->assertJsonStructure(['status', 'errors']);

        // 5. Test validation error (amount too low)
        $responseLowAmt = $this->postJson(route('api-frontend.order.store'), [
            'api_key' => 'usr_ordertest123456',
            'id_category_smm' => $category->id,
            'id_service_smm' => $service->id,
            'target' => 'https://instagram.com/p/1',
            'amount' => 5 // min is 10
        ]);
        $responseLowAmt->assertStatus(422);

        // 6. Test successful placement (Default type)
        // amount 500, price_sale is 1.50 per 1000, so price is 0.75
        $responseSuccess = $this->postJson(route('api-frontend.order.store'), [
            'api_key' => 'usr_ordertest123456',
            'id_category_smm' => $category->id,
            'id_service_smm' => $service->id,
            'target' => 'https://instagram.com/p/1',
            'amount' => 500
        ]);

        $responseSuccess->assertStatus(201);
        $responseSuccess->assertJson([
            'status' => 'success',
            'message' => 'Order placed successfully!',
            'data' => [
                'order_id' => '998877',
                'amount' => 500,
                'price' => '0.75',
                'balance_remaining' => '99.2500',
                'min_order' => 10,
                'max_order' => 1000,
                'description' => 'Test description'
            ]
        ]);

        // Assert database updates
        $user->refresh();
        $this->assertEquals(99.2500, (float)$user->balance);
        $this->assertDatabaseHas('order_smm', [
            'id_user' => $user->id,
            'id_service_smm' => $service->id,
            'sid' => '998877',
            'target' => 'https://instagram.com/p/1',
            'amount' => 500,
            'price_sale' => 0.75,
            'status_order' => 'Pending'
        ]);
    }

    /**
     * Test SMM Order API placement with reseller pricing and cURL compatibility parameter names.
     */
    public function test_api_order_placement_reseller_pricing_and_curl_aliases(): void
    {
        // 1. Setup user as reseller
        $user = User::factory()->create([
            'status' => 'Active',
            'api_key' => 'usr_resellertest123',
            'balance' => 100.00,
            'is_seller' => true // Reseller!
        ]);

        // 2. Setup Category, SMM Provider API, and Service SMM
        $category = CategorySmm::create([
            'name' => 'Instagram Services',
            'code' => 'IG',
            'status' => 'Active'
        ]);

        $apiProvider = ApiSmm::create([
            'name' => 'Provider Y',
            'code' => 'PROV_Y',
            'balance' => 500.00,
            'url' => 'https://api.provider2.com/v2',
            'api_key' => 'provider_api_key_xyz',
            'status' => 'Active'
        ]);

        $service = ServiceSmm::create([
            'id_category_smm' => $category->id,
            'id_api_smm' => $apiProvider->id,
            'name_service' => 'Instagram Likes Reseller',
            'pid' => '102',
            'min_order' => 10,
            'max_order' => 1000,
            'price_api' => 0.50,
            'price_sale' => 1.50, // Standard price USD 1.50 per 1000
            'price_reseller' => 1.00, // Reseller price USD 1.00 per 1000
            'type' => 'Default',
            'status' => 'Active',
            'desc' => 'Super cheap likes for resellers.'
        ]);

        // Mock Http requests to SMM provider API
        Http::fake([
            'https://api.provider2.com/v2' => Http::response([
                'status' => 'success',
                'order' => '998878'
            ], 200)
        ]);

        // 3. Place SMM Order using cURL aliases
        // quantity 500, price_reseller is 1.00 per 1000, so price is 0.50 (instead of 0.75)
        $responseSuccess = $this->postJson(route('api-frontend.order.store'), [
            'key' => 'usr_resellertest123', // cURL key alias
            'service' => '102', // cURL service alias (pid)
            'link' => 'https://instagram.com/p/2', // cURL link alias
            'quantity' => 500 // cURL quantity alias
        ]);

        $responseSuccess->assertStatus(201);
        $responseSuccess->assertJson([
            'status' => 'success',
            'message' => 'Order placed successfully!',
            'data' => [
                'order_id' => '998878',
                'amount' => 500,
                'price' => '0.50', // Reseller price used!
                'balance_remaining' => '99.5000',
                'min_order' => 10,
                'max_order' => 1000,
                'description' => 'Super cheap likes for resellers.'
            ]
        ]);

        // Assert database updates
        $user->refresh();
        $this->assertEquals(99.5000, (float)$user->balance);
        $this->assertDatabaseHas('order_smm', [
            'id_user' => $user->id,
            'id_service_smm' => $service->id,
            'sid' => '998878',
            'target' => 'https://instagram.com/p/2',
            'amount' => 500,
            'price_sale' => 0.50, // Reseller price charged!
            'status_order' => 'Pending'
        ]);
    }

    /**
     * Test API Deposit creation, validations, and callback redirection.
     */
    public function test_api_deposit_placement_validation_and_gateways(): void
    {
        // 1. Setup user
        $user = User::factory()->create([
            'status' => 'Active',
            'api_key' => 'usr_deposittest123',
            'username' => 'testdeposituser',
            'balance' => 0.00
        ]);

        // 2. Setup PaymentGateways
        PaymentGateway::create([
            'type' => 'Paypal',
            'api_config' => [
                'client_id' => 'paypal_client_id_123',
                'client_secret' => 'paypal_client_secret_abc',
                'mode' => 'sandbox'
            ]
        ]);

        PaymentGateway::create([
            'type' => 'Cryptomus',
            'api_config' => [
                'merchant_id' => 'cryptomus_merchant_id_456',
                'payment_key' => 'cryptomus_payment_key_def'
            ]
        ]);

        // 3. Test Unauthorized request
        $responseUnauth = $this->postJson(route('api-frontend.deposit.store'), [
            'api_key' => 'invalid_key',
            'amount' => 50,
            'payment_method' => 'paypal'
        ]);
        $responseUnauth->assertStatus(401);

        // 4. Test Validation failures
        $responseValFail = $this->postJson(route('api-frontend.deposit.store'), [
            'api_key' => 'usr_deposittest123',
            'amount' => 0.5, // minimum is 1
            'payment_method' => 'invalid_method'
        ]);
        $responseValFail->assertStatus(422);

        // 5. Mock HTTP APIs
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'mock_paypal_access_token_xyz'
            ], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id' => 'PAYPAL_ORDER_123',
                'links' => [
                    [
                        'rel' => 'approve',
                        'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL_ORDER_123'
                    ]
                ]
            ], 200),
            'https://api.cryptomus.com/v1/payment' => Http::response([
                'result' => [
                    'url' => 'https://pay.cryptomus.com/pay/cryptomus_uuid_abc',
                    'uuid' => 'cryptomus_uuid_abc'
                ]
            ], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/PAYPAL_ORDER_123/capture' => Http::response([
                'status' => 'COMPLETED',
                'id' => 'PAYPAL_ORDER_123'
            ], 200),
        ]);

        // 6. Test PayPal API Deposit Store
        $responsePaypal = $this->postJson(route('api-frontend.deposit.store'), [
            'api_key' => 'usr_deposittest123',
            'amount' => 100.50,
            'payment_method' => 'paypal',
            'url_success' => 'https://client-site.com/payment-success',
            'url_cancel' => 'https://client-site.com/payment-cancelled'
        ]);

        $responsePaypal->assertStatus(201);
        $responsePaypal->assertJson([
            'status' => 'success',
            'message' => 'PayPal transaction created successfully.',
            'data' => [
                'payment_method' => 'Paypal',
                'amount' => '100.50',
                'redirect_url' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL_ORDER_123'
            ]
        ]);

        $this->assertDatabaseHas('history_deposit', [
            'id_user' => $user->id,
            'amount' => 100.50,
            'status_payment' => 'Pending'
        ]);

        // Verify detail_transaction attributes
        $depositPaypal = HistoryDeposit::where('id_user', $user->id)
            ->where('amount', 100.50)
            ->first();
        $this->assertEquals('PAYPAL_ORDER_123', $depositPaypal->detail_transaction['paypal_order_id']);
        $this->assertEquals('https://client-site.com/payment-success', $depositPaypal->detail_transaction['url_success']);

        // 7. Test PayPal Callback Redirection
        $responseCallback = $this->actingAs($user)->get(route('member.payment.paypal.callback', [
            'token' => 'PAYPAL_ORDER_123'
        ]));

        $responseCallback->assertRedirect('https://client-site.com/payment-success');

        $user->refresh();
        $this->assertEquals(100.50, (float)$user->balance);

        $depositPaypal->refresh();
        $this->assertEquals('Success', $depositPaypal->status_payment);

        // 8. Test Cryptomus API Deposit Store
        $responseCryptomus = $this->postJson(route('api-frontend.deposit.store'), [
            'api_key' => 'usr_deposittest123',
            'amount' => 50.00,
            'payment_method' => 'cryptomus',
            'url_success' => 'https://client-site.com/payment-success',
            'url_return' => 'https://client-site.com/payment-return'
        ]);

        $responseCryptomus->assertStatus(201);
        $responseCryptomus->assertJson([
            'status' => 'success',
            'message' => 'Cryptomus transaction created successfully.',
            'data' => [
                'payment_method' => 'Cryptomus',
                'amount' => '50.00',
                'redirect_url' => 'https://pay.cryptomus.com/pay/cryptomus_uuid_abc'
            ]
        ]);

        $this->assertDatabaseHas('history_deposit', [
            'id_user' => $user->id,
            'amount' => 50.00,
            'status_payment' => 'Pending'
        ]);
    }

    /**
     * Test history endpoints for SMM orders and deposits.
     */
    public function test_api_history_endpoints_for_orders_and_deposits(): void
    {
        $user = User::factory()->create([
            'status' => 'Active',
            'api_key' => 'usr_historytestkey123',
            'balance' => 100.00
        ]);

        $category = CategorySmm::create([
            'name' => 'Instagram Services',
            'code' => 'IG',
            'status' => 'Active'
        ]);

        $apiProvider = ApiSmm::create([
            'name' => 'Provider Y',
            'code' => 'PROV_Y',
            'balance' => 500.00,
            'url' => 'https://api.provider2.com/v2',
            'api_key' => 'provider_api_key_xyz',
            'status' => 'Active'
        ]);

        $service = ServiceSmm::create([
            'id_category_smm' => $category->id,
            'id_api_smm' => $apiProvider->id,
            'name_service' => 'Instagram Likes',
            'pid' => '102',
            'min_order' => 10,
            'max_order' => 1000,
            'price_api' => 0.50,
            'price_sale' => 1.50,
            'price_reseller' => 1.00,
            'type' => 'Default',
            'status' => 'Active',
            'desc' => 'Standard likes'
        ]);

        // Insert Order records
        OrderSmm::create([
            'id_user' => $user->id,
            'id_service_smm' => $service->id,
            'id_api_smm' => $apiProvider->id,
            'sid' => '111',
            'invoice' => 'INV-ORDER-AAA',
            'target' => 'https://instagram.com/p/aaa',
            'amount' => 100,
            'price_api' => 0.05,
            'price_sale' => 0.15,
            'price_reseller' => 0.10,
            'status_order' => 'Success',
            'remains' => 0,
            'start_count' => 10,
            'refill' => false,
        ]);

        OrderSmm::create([
            'id_user' => $user->id,
            'id_service_smm' => $service->id,
            'id_api_smm' => $apiProvider->id,
            'sid' => '222',
            'invoice' => 'INV-ORDER-BBB',
            'target' => 'https://instagram.com/p/bbb',
            'amount' => 200,
            'price_api' => 0.10,
            'price_sale' => 0.30,
            'price_reseller' => 0.20,
            'status_order' => 'Pending',
            'remains' => 200,
            'start_count' => 0,
            'refill' => false,
        ]);

        // Insert Deposit records
        HistoryDeposit::create([
            'id_user' => $user->id,
            'invoice' => 'INV-DEP-AAA',
            'amount' => 50.00,
            'status_payment' => 'Success',
            'detail_transaction' => [
                'payment_method' => 'Paypal',
                'amount_raw' => 50.00
            ]
        ]);

        HistoryDeposit::create([
            'id_user' => $user->id,
            'invoice' => 'INV-DEP-BBB',
            'amount' => 20.00,
            'status_payment' => 'Pending',
            'detail_transaction' => [
                'payment_method' => 'Cryptomus',
                'amount_raw' => 20.00
            ]
        ]);

        // 1. Order History Unauthorized
        $responseOrderUnauth = $this->postJson(route('api-frontend.order.history'), [
            'api_key' => 'invalid_key'
        ]);
        $responseOrderUnauth->assertStatus(401);

        // 2. Order History Success & Formatting (Limit = 1)
        $responseOrderSuccess = $this->postJson(route('api-frontend.order.history'), [
            'api_key' => 'usr_historytestkey123',
            'limit' => 1
        ]);
        $responseOrderSuccess->assertStatus(200);
        $responseOrderSuccess->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'invoice',
                    'service_name',
                    'target',
                    'amount',
                    'price',
                    'status',
                    'remains',
                    'start_count',
                    'created_at'
                ]
            ],
            'pagination' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
                'from',
                'to'
            ]
        ]);
        $this->assertCount(1, $responseOrderSuccess->json('data'));
        $this->assertEquals(2, $responseOrderSuccess->json('pagination.total'));

        // 3. Order History status filter
        $responseOrderFilterStatus = $this->postJson(route('api-frontend.order.history'), [
            'api_key' => 'usr_historytestkey123',
            'status' => 'Pending'
        ]);
        $responseOrderFilterStatus->assertStatus(200);
        $this->assertCount(1, $responseOrderFilterStatus->json('data'));
        $this->assertEquals('INV-ORDER-BBB', $responseOrderFilterStatus->json('data.0.invoice'));

        // 4. Order History search filter
        $responseOrderFilterSearch = $this->postJson(route('api-frontend.order.history'), [
            'api_key' => 'usr_historytestkey123',
            'search' => 'aaa'
        ]);
        $responseOrderFilterSearch->assertStatus(200);
        $this->assertCount(1, $responseOrderFilterSearch->json('data'));
        $this->assertEquals('INV-ORDER-AAA', $responseOrderFilterSearch->json('data.0.invoice'));

        // 5. Deposit History Unauthorized
        $responseDepositUnauth = $this->postJson(route('api-frontend.deposit.history'), [
            'api_key' => 'invalid_key'
        ]);
        $responseDepositUnauth->assertStatus(401);

        // 6. Deposit History Success & Formatting (Limit = 1)
        $responseDepositSuccess = $this->postJson(route('api-frontend.deposit.history'), [
            'api_key' => 'usr_historytestkey123',
            'limit' => 1
        ]);
        $responseDepositSuccess->assertStatus(200);
        $responseDepositSuccess->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'invoice',
                    'amount',
                    'status',
                    'payment_method',
                    'created_at'
                ]
            ],
            'pagination' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
                'from',
                'to'
            ]
        ]);
        $this->assertCount(1, $responseDepositSuccess->json('data'));
        $this->assertEquals(2, $responseDepositSuccess->json('pagination.total'));

        // 7. Deposit History status filter
        $responseDepositFilterStatus = $this->postJson(route('api-frontend.deposit.history'), [
            'api_key' => 'usr_historytestkey123',
            'status' => 'Pending'
        ]);
        $responseDepositFilterStatus->assertStatus(200);
        $this->assertCount(1, $responseDepositFilterStatus->json('data'));
        $this->assertEquals('INV-DEP-BBB', $responseDepositFilterStatus->json('data.0.invoice'));

        // 8. Deposit History search filter
        $responseDepositFilterSearch = $this->postJson(route('api-frontend.deposit.history'), [
            'api_key' => 'usr_historytestkey123',
            'search' => 'DEP-AAA'
        ]);
        $responseDepositFilterSearch->assertStatus(200);
        $this->assertCount(1, $responseDepositFilterSearch->json('data'));
        $this->assertEquals('INV-DEP-AAA', $responseDepositFilterSearch->json('data.0.invoice'));
    }

    /**
     * Test Profile API endpoints.
     */
    public function test_api_profile_endpoints(): void
    {
        $user = User::factory()->create([
            'status' => 'Active',
            'api_key' => 'usr_profiletestkey123',
            'balance' => 350.25,
            'password' => Hash::make('password123'),
        ]);

        // 1. Show Profile Unauthorized
        $responseShowUnauth = $this->postJson(route('api-frontend.profile.show'), [
            'api_key' => 'invalid_key'
        ]);
        $responseShowUnauth->assertStatus(401);

        // 2. Show Profile Success
        $responseShow = $this->postJson(route('api-frontend.profile.show'), [
            'api_key' => 'usr_profiletestkey123'
        ]);
        $responseShow->assertStatus(200);
        $responseShow->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'balance' => '350.2500',
                'api_key' => 'usr_profiletestkey123',
            ]
        ]);

        // 3. Update Password Validation Error
        $responsePassValErr = $this->postJson(route('api-frontend.profile.update_password'), [
            'api_key' => 'usr_profiletestkey123',
            'current_password' => 'password123',
            'password' => 'newpass', // too short
            'password_confirmation' => 'different'
        ]);
        $responsePassValErr->assertStatus(422);

        // 4. Update Password Incorrect Current Password
        $responsePassIncorrect = $this->postJson(route('api-frontend.profile.update_password'), [
            'api_key' => 'usr_profiletestkey123',
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);
        $responsePassIncorrect->assertStatus(400);
        $responsePassIncorrect->assertJson([
            'status' => 'error',
            'message' => 'Current password is incorrect.'
        ]);

        // 5. Update Password Success
        $responsePassSuccess = $this->postJson(route('api-frontend.profile.update_password'), [
            'api_key' => 'usr_profiletestkey123',
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);
        $responsePassSuccess->assertStatus(200);
        $responsePassSuccess->assertJson([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));

        // 6. Regenerate API Key Success
        $responseRegenApiKey = $this->postJson(route('api-frontend.profile.regenerate_api_key'), [
            'api_key' => 'usr_profiletestkey123'
        ]);
        $responseRegenApiKey->assertStatus(200);
        $responseRegenApiKey->assertJson([
            'status' => 'success',
            'message' => 'API key regenerated successfully.'
        ]);
        $responseRegenApiKey->assertJsonStructure(['api_key']);
        
        $newKey = $responseRegenApiKey->json('api_key');
        $this->assertNotEmpty($newKey);
        $this->assertNotEquals('usr_profiletestkey123', $newKey);

        $user->refresh();
        $this->assertEquals($newKey, $user->api_key);

        // 7. Verify Old API key is now unauthorized
        $responseOldKeyUnauth = $this->postJson(route('api-frontend.profile.show'), [
            'api_key' => 'usr_profiletestkey123'
        ]);
        $responseOldKeyUnauth->assertStatus(401);

        // 8. Verify New API key is authorized
        $responseNewKeyAuth = $this->postJson(route('api-frontend.profile.show'), [
            'api_key' => $newKey
        ]);
        $responseNewKeyAuth->assertStatus(200);
        $responseNewKeyAuth->assertJsonPath('data.api_key', $newKey);
    }
}
