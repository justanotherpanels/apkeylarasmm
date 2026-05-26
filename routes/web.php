<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\SmmController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\WhatsAppController;
use App\Http\Controllers\Admin\SystemUpdateController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/auth/forgot-password', [AuthController::class, 'showForgetPassword'])->name('password.request');
Route::post('/auth/forgot-password', [AuthController::class, 'sendForgetPasswordOtp'])->middleware('throttle:5,1')->name('password.email');
Route::get('/auth/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:10,1')->name('password.update');

Route::get('/auth/otp', [AuthController::class, 'showOtp'])->name('otp.show');
Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('otp.verify');
Route::post('/auth/otp/resend', [AuthController::class, 'resendOtp'])->middleware('throttle:5,1')->name('otp.resend');

Route::middleware(['auth'])->prefix('member')->name('member.')->group(function () {
    Route::get('/', [App\Http\Controllers\Member\SmmController::class, 'dashboard'])->name('index');

    Route::get('/smm/order', [App\Http\Controllers\Member\SmmController::class, 'orderForm'])->name('smm.order');
    Route::post('/smm/order', [App\Http\Controllers\Member\SmmController::class, 'storeOrder'])->name('smm.order.store');
    Route::get('/smm/get-services/{categoryId}', [App\Http\Controllers\Member\SmmController::class, 'getServices'])->name('smm.get_services');
    Route::post('/smm/order/{id}/sync', [App\Http\Controllers\Member\SmmController::class, 'orderSync'])->name('smm.order.sync');

    // History Order
    Route::get('/smm/history', [App\Http\Controllers\Member\SmmController::class, 'historyIndex'])->name('smm.history');
    Route::get('/smm/history/{invoice}', [App\Http\Controllers\Member\SmmController::class, 'historyShow'])->name('smm.history.show');

    // History Deposit / Payment Add
    Route::get('/payment/add', [App\Http\Controllers\Member\PaymentController::class, 'add'])->name('payment.add');
    Route::post('/payment/add', [App\Http\Controllers\Member\PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payment/history', [App\Http\Controllers\Member\PaymentController::class, 'history'])->name('payment.history');
    Route::get('/payment/history/{invoice}', [App\Http\Controllers\Member\PaymentController::class, 'showDeposit'])->name('payment.history.show');
    Route::post('/payment/history/{invoice}/sync', [App\Http\Controllers\Member\PaymentController::class, 'syncDeposit'])->name('payment.history.sync');
    Route::get('/payment/paypal/callback', [App\Http\Controllers\Member\PaymentController::class, 'paypalCallback'])->name('payment.paypal.callback');

    // Refill Order
    Route::get('/smm/refill', function () {
        return view('member.smm.refill.index');
    })->name('smm.refill');

    // Pages SMM/Static
    Route::get('/pages/api', function () {
        return view('member.pages.api.index');
    })->name('pages.api');

    Route::post('/pages/api/generate', [App\Http\Controllers\Member\SmmController::class, 'generateApiKey'])->name('pages.api.generate');

    Route::get('/pages/faq', function () {
        return view('member.pages.faq.index');
    })->name('pages.faq');

    Route::get('/pages/privacy', function () {
        return view('member.pages.privacy.index');
    })->name('pages.privacy');

    Route::get('/smm/service', function () {
        $services = App\Models\ServiceSmm::with('category')
            ->where('status', 'Active')
            ->orderBy('id_category_smm', 'asc')
            ->get();
        return view('member.smm.service.index', compact('services'));
    })->name('smm.service');

    Route::get('/smm/service/{id}', function ($id) {
        $service = App\Models\ServiceSmm::with('category')
            ->where('status', 'Active')
            ->findOrFail($id);
        return view('member.smm.service.show', compact('service'));
    })->name('smm.service.show');

    // Tickets
    Route::get('/tickets', [App\Http\Controllers\Member\TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [App\Http\Controllers\Member\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [App\Http\Controllers\Member\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [App\Http\Controllers\Member\TicketController::class, 'reply'])->name('tickets.reply');
});


Route::middleware(['admin'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.index');

    Route::prefix('admin/user')->name('admin.user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/smm')->name('admin.smm.')->group(function () {
        Route::get('/order', [SmmController::class, 'orders'])->name('order');
        Route::post('/order/{id}/sync', [SmmController::class, 'orderSync'])->name('order.sync');
        // Category CRUD Routes
        Route::get('/category', [SmmController::class, 'categoryIndex'])->name('category');
        Route::get('/category/create', [SmmController::class, 'categoryCreate'])->name('category.create');
        Route::post('/category', [SmmController::class, 'categoryStore'])->name('category.store');
        Route::get('/category/{id}/edit', [SmmController::class, 'categoryEdit'])->name('category.edit');
        Route::put('/category/{id}', [SmmController::class, 'categoryUpdate'])->name('category.update');
        Route::delete('/category/{id}', [SmmController::class, 'categoryDestroy'])->name('category.destroy');

        // Service CRUD Routes
        Route::get('/service', [SmmController::class, 'serviceIndex'])->name('service');
        Route::get('/service/create', [SmmController::class, 'serviceCreate'])->name('service.create');
        Route::post('/service', [SmmController::class, 'serviceStore'])->name('service.store');
        Route::get('/service/{id}/edit', [SmmController::class, 'serviceEdit'])->name('service.edit');
        Route::put('/service/{id}', [SmmController::class, 'serviceUpdate'])->name('service.update');
        Route::delete('/service/{id}', [SmmController::class, 'serviceDestroy'])->name('service.destroy');
        Route::post('/service/update-pid', [SmmController::class, 'serviceUpdatePid'])->name('service.update_pid');
        Route::post('/service/apply-markup', [SmmController::class, 'serviceApplyMarkup'])->name('service.apply_markup');
        
        // API Provider CRUD Routes
        Route::get('/api', [SmmController::class, 'apiIndex'])->name('api');
        Route::get('/api/create', [SmmController::class, 'apiCreate'])->name('api.create');
        Route::post('/api', [SmmController::class, 'apiStore'])->name('api.store');
        Route::get('/api/{id}/edit', [SmmController::class, 'apiEdit'])->name('api.edit');
        Route::post('/api/{id}/sync', [SmmController::class, 'apiSyncBalance'])->name('api.sync');
        Route::put('/api/{id}', [SmmController::class, 'apiUpdate'])->name('api.update');
        Route::delete('/api/{id}', [SmmController::class, 'apiDestroy'])->name('api.destroy');

        Route::get('/import', [SmmController::class, 'importIndex'])->name('import');
        Route::post('/import/fetch', [SmmController::class, 'importFetch'])->name('import.fetch');
        Route::post('/import/store', [SmmController::class, 'importStore'])->name('import.store');
    });

    Route::prefix('admin/payment')->name('admin.payment.')->group(function () {
        Route::get('/history', function () { 
            $deposits = \App\Models\HistoryDeposit::with('user')->orderBy('id', 'desc')->get();
            return view('admin.payment.history.index', compact('deposits')); 
        })->name('history');
        Route::get('/settings', [PaymentGatewayController::class, 'settings'])->name('settings');
        Route::post('/settings', [PaymentGatewayController::class, 'update'])->name('settings.update');
    });

    Route::prefix('admin/ticket')->name('admin.ticket.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('reply');
        Route::post('/{id}/close', [App\Http\Controllers\Admin\TicketController::class, 'close'])->name('close');
    });

    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/admin/system-update', [SystemUpdateController::class, 'index'])->name('admin.system-update.index');
    Route::post('/admin/system-update/check', [SystemUpdateController::class, 'check'])->name('admin.system-update.check');
    Route::post('/admin/system-update/run', [SystemUpdateController::class, 'update'])->name('admin.system-update.run');

    Route::get('/admin/whatsapp', [WhatsAppController::class, 'index'])->name('admin.whatsapp.index');
    Route::post('/admin/whatsapp/store', [WhatsAppController::class, 'store'])->name('admin.whatsapp.store');
    Route::get('/admin/whatsapp/scan/{id}', [WhatsAppController::class, 'scan'])->name('admin.whatsapp.scan');
    Route::get('/admin/whatsapp/status/{id}', [WhatsAppController::class, 'status'])->name('admin.whatsapp.status');
    Route::post('/admin/whatsapp/test', [WhatsAppController::class, 'testSend'])->name('admin.whatsapp.test');
    Route::post('/admin/whatsapp/logout', [WhatsAppController::class, 'logout'])->name('admin.whatsapp.logout');
});

// WhatsApp Webhook from Node.js server (no auth, no CSRF)
Route::post('/webhook/whatsapp/disconnect', [WhatsAppController::class, 'webhookDisconnect'])->name('whatsapp.webhook.disconnect');
Route::post('/webhook/whatsapp/connected', [WhatsAppController::class, 'webhookConnected'])->name('whatsapp.webhook.connected');

// WhatsApp Bot Webhook (Public, No CSRF, No Auth)
Route::post('/webhook/whatsapp/bot', [App\Http\Controllers\Admin\WhatsAppBotController::class, 'webhook'])->name('whatsapp.bot.webhook');

// Cryptomus Webhook (Public, No CSRF, No Auth)
Route::post('/member/payment/cryptomus/callback', [App\Http\Controllers\Member\PaymentController::class, 'cryptomusCallback'])->name('member.payment.cryptomus.callback');

// SMM API v2 Endpoint (Client-facing)
Route::post('/api/v2', [App\Http\Controllers\ApiController::class, 'handle'])->name('api.v2');

// API Frontend authentication endpoints
Route::prefix('api-frontend/auth')->group(function () {
    Route::post('/login', [App\Http\Controllers\ApiFrontend\AuthController::class, 'login'])->middleware('throttle:20,1')->name('api-frontend.login');
    Route::post('/register', [App\Http\Controllers\ApiFrontend\AuthController::class, 'register'])->name('api-frontend.register');
    Route::post('/verify-otp', [App\Http\Controllers\ApiFrontend\AuthController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('api-frontend.verify_otp');
    Route::post('/resend-otp', [App\Http\Controllers\ApiFrontend\AuthController::class, 'resendOtp'])->middleware('throttle:5,1')->name('api-frontend.resend_otp');
    Route::post('/forget', [App\Http\Controllers\ApiFrontend\AuthController::class, 'forget'])->middleware('throttle:5,1')->name('api-frontend.forget');
    Route::post('/reset-password', [App\Http\Controllers\ApiFrontend\AuthController::class, 'resetPassword'])->middleware('throttle:10,1')->name('api-frontend.reset_password');
});

// API Frontend dashboard endpoints
Route::post('/api-frontend/dashboard', [App\Http\Controllers\ApiFrontend\DashboardController::class, 'index'])->name('api-frontend.dashboard');

// API Frontend SMM order endpoints
Route::post('/api-frontend/order', [App\Http\Controllers\ApiFrontend\OrderController::class, 'store'])->name('api-frontend.order.store');
Route::post('/api-frontend/order/history', [App\Http\Controllers\ApiFrontend\OrderController::class, 'history'])->name('api-frontend.order.history');

// API Frontend deposit endpoints
Route::post('/api-frontend/deposit', [App\Http\Controllers\ApiFrontend\DepositController::class, 'store'])->name('api-frontend.deposit.store');
Route::post('/api-frontend/deposit/history', [App\Http\Controllers\ApiFrontend\DepositController::class, 'history'])->name('api-frontend.deposit.history');

// API Frontend profile endpoints
Route::post('/api-frontend/profile', [App\Http\Controllers\ApiFrontend\ProfileController::class, 'show'])->name('api-frontend.profile.show');
Route::post('/api-frontend/profile/update-password', [App\Http\Controllers\ApiFrontend\ProfileController::class, 'updatePassword'])->name('api-frontend.profile.update_password');
Route::post('/api-frontend/profile/regenerate-api-key', [App\Http\Controllers\ApiFrontend\ProfileController::class, 'regenerateApiKey'])->name('api-frontend.profile.regenerate_api_key');



