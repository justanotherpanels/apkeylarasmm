<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderSmm;
use App\Models\ApiSmm;
use App\Models\CategorySmm;
use App\Models\ServiceSmm;
use Illuminate\Http\Request;

class SmmController extends Controller
{
    /**
     * Display a listing of SMM orders.
     */
    public function orders()
    {
        // Ambil data order beserta relasinya
        $orders = OrderSmm::with(['user', 'service', 'api'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.smm.order.index', compact('orders'));
    }

    /**
     * Display a listing of SMM API Providers.
     */
    public function apiIndex()
    {
        $apis = ApiSmm::orderBy('id', 'desc')->get();
        return view('admin.smm.api.index', compact('apis'));
    }

    /**
     * Show the form for creating a new SMM API Provider.
     */
    public function apiCreate()
    {
        return view('admin.smm.api.create');
    }

    /**
     * Store a newly created SMM API Provider in storage.
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:api_smm,code|max:255',
            'url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Not-Active',
        ]);

        ApiSmm::create([
            'name' => $request->name,
            'code' => $request->code,
            'url' => $request->url,
            'api_key' => $request->api_key,
            'balance' => $request->balance ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.smm.api')->with('success', 'API Provider berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified SMM API Provider.
     */
    public function apiEdit($id)
    {
        $api = ApiSmm::findOrFail($id);
        return view('admin.smm.api.edit', compact('api'));
    }

    /**
     * Update the specified SMM API Provider in storage.
     */
    public function apiUpdate(Request $request, $id)
    {
        $api = ApiSmm::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:api_smm,code,' . $id,
            'url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Not-Active',
        ]);

        $api->update([
            'name' => $request->name,
            'code' => $request->code,
            'url' => $request->url,
            'api_key' => $request->api_key,
            'balance' => $request->balance ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.smm.api')->with('success', 'API Provider berhasil diperbarui!');
    }

    /**
     * Remove the specified SMM API Provider from storage.
     */
    public function apiDestroy($id)
    {
        $api = ApiSmm::findOrFail($id);
        $api->delete();

        return redirect()->route('admin.smm.api')->with('success', 'API Provider berhasil dihapus!');
    }

    /**
     * Sync SMM API Provider Balance.
     */
    public function apiSyncBalance($id)
    {
        $api = ApiSmm::findOrFail($id);

        if (!$api->url || !$api->api_key) {
            return redirect()->route('admin.smm.api')->with('error', 'URL Endpoint atau API Key belum dikonfigurasi!');
        }

        try {
            // Mengirim request cURL POST untuk mendapatkan balance dengan menonaktifkan verifikasi SSL untuk kelancaran lokal
            $response = \Illuminate\Support\Facades\Http::external()->asForm()->post($api->url, [
                'action' => 'balance',
                'key' => $api->api_key
            ]);

            if ($response->failed()) {
                return redirect()->route('admin.smm.api')->with('error', 'Gagal terhubung ke API Provider! Status: ' . $response->status());
            }

            $data = $response->json();

            // Mendukung cURL response format Option A dan Option B
            if (isset($data['balance'])) {
                $newBalance = floatval($data['balance']);
                $api->update(['balance' => $newBalance]);

                $currency = $data['currency'] ?? 'USD';
                return redirect()->route('admin.smm.api')->with('success', "Balance berhasil disinkronisasi! Saldo saat ini: {$newBalance} {$currency}");
            } else {
                return redirect()->route('admin.smm.api')->with('error', "Gagal mensinkronkan balance. Respon API: " . json_encode($data));
            }

        } catch (\Exception $e) {
            return redirect()->route('admin.smm.api')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of SMM Categories.
     */
    public function categoryIndex()
    {
        $categories = CategorySmm::orderBy('id', 'desc')->get();
        return view('admin.smm.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new SMM Category.
     */
    public function categoryCreate()
    {
        return view('admin.smm.category.create');
    }

    /**
     * Store a newly created SMM Category in storage.
     */
    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_smm,name',
            'code' => 'nullable|string|max:255|unique:category_smm,code',
            'status' => 'required|in:Active,Not-Active',
        ]);

        CategorySmm::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.smm.category')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified SMM Category.
     */
    public function categoryEdit($id)
    {
        $category = CategorySmm::findOrFail($id);
        return view('admin.smm.category.edit', compact('category'));
    }

    /**
     * Update the specified SMM Category in storage.
     */
    public function categoryUpdate(Request $request, $id)
    {
        $category = CategorySmm::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:category_smm,name,' . $id,
            'code' => 'nullable|string|max:255|unique:category_smm,code,' . $id,
            'status' => 'required|in:Active,Not-Active',
        ]);

        $category->update([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.smm.category')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified SMM Category from storage.
     */
    public function categoryDestroy($id)
    {
        $category = CategorySmm::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.smm.category')->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Display a listing of SMM Services.
     */
    public function serviceIndex()
    {
        $services = ServiceSmm::with(['category', 'api'])->orderBy('id', 'desc')->get();
        $apis = ApiSmm::where('status', 'Active')->orderBy('name', 'asc')->get();
        return view('admin.smm.service.index', compact('services', 'apis'));
    }

    /**
     * Show the form for creating a new SMM Service.
     */
    public function serviceCreate()
    {
        $categories = CategorySmm::orderBy('name', 'asc')->get();
        $apis = ApiSmm::orderBy('name', 'asc')->get();
        return view('admin.smm.service.create', compact('categories', 'apis'));
    }

    /**
     * Store a newly created SMM Service in storage.
     */
    public function serviceStore(Request $request)
    {
        $request->validate([
            'id_category_smm' => 'required|exists:category_smm,id',
            'id_api_smm' => 'required|exists:api_smm,id',
            'name_service' => 'required|string|max:255',
            'pid' => 'nullable|string|max:255',
            'min_order' => 'required|integer|min:0',
            'max_order' => 'required|integer|min:0',
            'price_api' => 'required|numeric|min:0',
            'price_sale' => 'required|numeric|min:0',
            'price_reseller' => 'required|numeric|min:0',
            'type' => 'required|in:Default,Package,Custom Comments,Poll,Subscriptions',
            'desc' => 'nullable|string',
            'refill' => 'required|boolean',
            'status' => 'required|in:Active,Not-Active',
        ]);

        ServiceSmm::create($request->all());

        return redirect()->route('admin.smm.service')->with('success', 'Layanan berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified SMM Service.
     */
    public function serviceEdit($id)
    {
        $service = ServiceSmm::findOrFail($id);
        $categories = CategorySmm::orderBy('name', 'asc')->get();
        $apis = ApiSmm::orderBy('name', 'asc')->get();
        return view('admin.smm.service.edit', compact('service', 'categories', 'apis'));
    }

    /**
     * Update the specified SMM Service in storage.
     */
    public function serviceUpdate(Request $request, $id)
    {
        $service = ServiceSmm::findOrFail($id);

        $request->validate([
            'id_category_smm' => 'required|exists:category_smm,id',
            'id_api_smm' => 'required|exists:api_smm,id',
            'name_service' => 'required|string|max:255',
            'pid' => 'nullable|string|max:255',
            'min_order' => 'required|integer|min:0',
            'max_order' => 'required|integer|min:0',
            'price_api' => 'required|numeric|min:0',
            'price_sale' => 'required|numeric|min:0',
            'price_reseller' => 'required|numeric|min:0',
            'type' => 'required|in:Default,Package,Custom Comments,Poll,Subscriptions',
            'desc' => 'nullable|string',
            'refill' => 'required|boolean',
            'status' => 'required|in:Active,Not-Active',
        ]);

        $service->update($request->all());

        return redirect()->route('admin.smm.service')->with('success', 'Layanan SMM berhasil diperbarui!');
    }

    /**
     * Remove the specified SMM Service from storage.
     */
    public function serviceDestroy($id)
    {
        $service = ServiceSmm::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.smm.service')->with('success', 'Layanan SMM berhasil dihapus!');
    }

    /**
     * Show import form.
     */
    public function importIndex()
    {
        $apis = ApiSmm::where('status', 'Active')->orderBy('name', 'asc')->get();
        return view('admin.smm.import.index', compact('apis'));
    }

    /**
     * Fetch services from API.
     */
    public function importFetch(Request $request)
    {
        $request->validate([
            'id_api_smm' => 'required|exists:api_smm,id',
        ]);

        $api = ApiSmm::findOrFail($request->id_api_smm);

        if (!$api->url || !$api->api_key) {
            return back()->with('error', 'URL Endpoint atau API Key belum dikonfigurasi!');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::external()->asForm()->post($api->url, [
                'action' => 'services',
                'key' => $api->api_key
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal mengambil data dari API Provider! Status: ' . $response->status());
            }

            $fetchedServices = $response->json();

            if (!is_array($fetchedServices)) {
                return back()->with('error', 'Respon dari API Provider tidak valid!');
            }

            $apis = ApiSmm::where('status', 'Active')->orderBy('name', 'asc')->get();
            $categories = CategorySmm::where('status', 'Active')->orderBy('name', 'asc')->get();
            $selectedApiId = $api->id;

            return view('admin.smm.import.index', compact('apis', 'categories', 'fetchedServices', 'selectedApiId'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store imported services in database.
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'id_api_smm' => 'required|exists:api_smm,id',
            'services' => 'required|array',
            'markup' => 'required|numeric|min:0',
            'reseller_markup' => 'required|numeric|min:0',
            'id_category_smm' => 'nullable|exists:category_smm,id',
        ]);

        $api = ApiSmm::findOrFail($request->id_api_smm);
        $markup = floatval($request->markup);
        $resellerMarkup = floatval($request->reseller_markup);
        $selectedServices = $request->services;
        $targetCategoryId = $request->id_category_smm;

        $count = 0;

        foreach ($selectedServices as $serviceDataStr) {
            $serviceData = json_decode($serviceDataStr, true);
            if (!$serviceData) continue;

            // Determine local Category ID
            if ($targetCategoryId) {
                $categoryId = $targetCategoryId;
            } else {
                $categoryName = trim($serviceData['category'] ?? 'Uncategorized');

                // Find or create Category dynamically
                $category = CategorySmm::firstOrCreate(
                    ['name' => $categoryName],
                    [
                        'code' => \Illuminate\Support\Str::slug($categoryName),
                        'status' => 'Active'
                    ]
                );
                $categoryId = $category->id;
            }

            // Determine Service Type
            $dbType = 'Default';
            $apiType = strtolower($serviceData['type'] ?? 'default');
            if (strpos($apiType, 'comment') !== false) {
                $dbType = 'Custom Comments';
            } elseif (strpos($apiType, 'poll') !== false) {
                $dbType = 'Poll';
            } elseif (strpos($apiType, 'subscription') !== false) {
                $dbType = 'Subscriptions';
            } elseif (strpos($apiType, 'package') !== false) {
                $dbType = 'Package';
            }

            // Calculate Sale and Reseller Prices
            $rate = floatval($serviceData['rate'] ?? 0);
            $priceSale = $rate * (1 + $markup / 100);
            $priceReseller = $rate * (1 + $resellerMarkup / 100);

            // Parse refill
            $refill = false;
            if (isset($serviceData['refill'])) {
                $refill = filter_var($serviceData['refill'], FILTER_VALIDATE_BOOLEAN);
            }

            // Update or Create Service SMM
            ServiceSmm::updateOrCreate(
                [
                    'id_api_smm' => $api->id,
                    'pid' => strval($serviceData['service']),
                ],
                [
                    'id_category_smm' => $categoryId,
                    'name_service' => $serviceData['name'] ?? 'SMM Service',
                    'min_order' => intval($serviceData['min'] ?? 0),
                    'max_order' => intval($serviceData['max'] ?? 0),
                    'price_api' => $rate,
                    'price_sale' => $priceSale,
                    'price_reseller' => $priceReseller,
                    'type' => $dbType,
                    'desc' => $serviceData['desc'] ?? null,
                    'refill' => $refill,
                    'status' => 'Active'
                ]
            );

            $count++;
        }

        return redirect()->route('admin.smm.service')->with('success', "Berhasil mengimpor {$count} layanan SMM!");
    }

    public function serviceUpdatePid(Request $request)
    {
        $request->validate([
            'id_api_smm' => 'required|exists:api_smm,id',
        ]);

        $api = ApiSmm::findOrFail($request->id_api_smm);

        if (!$api->url || !$api->api_key) {
            return back()->with('error', 'URL Endpoint atau API Key belum dikonfigurasi!');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::external()->asForm()->post($api->url, [
                'action' => 'services',
                'key' => $api->api_key
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal mengambil data dari API Provider! Status: ' . $response->status());
            }

            $fetchedServices = $response->json();
            if (!is_array($fetchedServices)) {
                return back()->with('error', 'Respon dari API Provider tidak valid!');
            }

            $map = [];
            foreach ($fetchedServices as $svc) {
                if (!isset($svc['name']) || !isset($svc['service'])) continue;
                $key = strtolower(trim(preg_replace('/\s+/', ' ', $svc['name'])));
                $map[$key] = strval($svc['service']);
            }

            $dbServices = ServiceSmm::where('id_api_smm', $api->id)->get();
            $updated = 0;
            $matched = 0;

            foreach ($dbServices as $svc) {
                $nameKey = strtolower(trim(preg_replace('/\s+/', ' ', $svc->name_service)));
                if (isset($map[$nameKey])) {
                    $matched++;
                    $newPid = $map[$nameKey];
                    if ($svc->pid !== $newPid) {
                        $svc->update(['pid' => $newPid]);
                        $updated++;
                    }
                }
            }

            return back()->with('success', "Update PID selesai. Cocok: {$matched}, Diperbarui: {$updated}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function serviceApplyMarkup(Request $request)
    {
        $request->validate([
            'markup_sale' => 'required|numeric|min:0',
            'markup_reseller' => 'required|numeric|min:0',
        ]);

        $markupSale = floatval($request->markup_sale);
        $markupReseller = floatval($request->markup_reseller);

        $services = ServiceSmm::all();
        $updated = 0;

        foreach ($services as $svc) {
            $priceSale = floatval($svc->price_api) * (1 + $markupSale / 100);
            $priceReseller = floatval($svc->price_api) * (1 + $markupReseller / 100);
            $svc->update([
                'price_sale' => $priceSale,
                'price_reseller' => $priceReseller,
            ]);
            $updated++;
        }

        return back()->with('success', "Markup harga diterapkan ke {$updated} layanan.");
    }

    /**
     * Sync order status with API provider.
     */
    public function orderSync($id)
    {
        $order = OrderSmm::with('api')->findOrFail($id);

        if (!$order->api || !$order->api->url || !$order->api->api_key) {
            return back()->with('error', 'API Provider untuk order ini belum dikonfigurasi!');
        }

        if (!$order->sid) {
            return back()->with('error', 'Order ini tidak memiliki SID (API Order ID)!');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::external()->asForm()->post($order->api->url, [
                'action' => 'status',
                'key' => $order->api->api_key,
                'order' => $order->sid
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal terhubung ke API Provider! Status: ' . $response->status());
            }

            $data = $response->json();

            if (isset($data['status'])) {
                // Map API status to database status_order
                $apiStatus = strtolower($data['status']);
                $dbStatus = 'Pending';

                if ($apiStatus === 'pending') {
                    $dbStatus = 'Pending';
                } elseif ($apiStatus === 'processing' || $apiStatus === 'in progress' || $apiStatus === 'in progress') {
                    $dbStatus = 'In Progres';
                } elseif ($apiStatus === 'partial') {
                    $dbStatus = 'Partial';
                } elseif ($apiStatus === 'canceled' || $apiStatus === 'cancel') {
                    $dbStatus = 'Cancel';
                } elseif ($apiStatus === 'error' || $apiStatus === 'fail') {
                    $dbStatus = 'Error';
                } elseif ($apiStatus === 'completed' || $apiStatus === 'success' || $apiStatus === 'finish') {
                    $dbStatus = 'Success';
                }

                $order->update([
                    'status_order' => $dbStatus,
                    'start_count' => intval($data['start_count'] ?? $order->start_count),
                    'remains' => intval($data['remains'] ?? $order->remains),
                ]);

                return back()->with('success', 'Status order #' . $order->invoice . ' berhasil disinkronisasikan!');
            } else {
                $errorMsg = $data['error'] ?? 'API response format invalid.';
                return back()->with('error', 'Gagal mensinkronisasikan status: ' . $errorMsg);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
