<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'      => 'nullable|string|max:255',
            'favicon'        => 'nullable|image|mimes:png,jpg,jpeg,gif,ico,svg,webp|max:2048',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,gif,ico,svg,webp|max:2048',
            'email'          => 'nullable|email|max:255',
            'instagram_url'  => 'nullable|string|max:500',
            'facebook_url'   => 'nullable|string|max:500',
            'whatsapp_url'   => 'nullable|string|max:500',
            'head_code'      => 'nullable|string',
            'footer_code'    => 'nullable|string',
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $oldHeadCode = $setting->head_code;
        $oldFooterCode = $setting->footer_code;

        $setting->fill($request->only([
            'site_name',
            'email',
            'instagram_url',
            'facebook_url',
            'whatsapp_url',
            'head_code',
            'footer_code',
        ]));

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            
            // Delete old file if exists
            if (!empty($setting->logo_path)) {
                $parsedUrl = parse_url($setting->logo_path);
                if (isset($parsedUrl['path'])) {
                    $relativePublicPath = ltrim($parsedUrl['path'], '/');
                    $oldPath = public_path($relativePublicPath);
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }

            // Ensure public/uploads directory exists
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0755, true);
            }

            $logoFile->move(public_path('uploads'), $logoName);
            $setting->logo_path = asset('uploads/' . $logoName);
        }

        if ($request->hasFile('favicon')) {
            $faviconFile = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '.' . $faviconFile->getClientOriginalExtension();
            
            // Delete old file if exists
            if (!empty($setting->favicon_path)) {
                $parsedUrl = parse_url($setting->favicon_path);
                if (isset($parsedUrl['path'])) {
                    $relativePublicPath = ltrim($parsedUrl['path'], '/');
                    $oldPath = public_path($relativePublicPath);
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }

            // Ensure public/uploads directory exists
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0755, true);
            }

            $faviconFile->move(public_path('uploads'), $faviconName);
            $setting->favicon_path = asset('uploads/' . $faviconName);
        }

        $setting->save();

        if ($oldHeadCode !== $setting->head_code || $oldFooterCode !== $setting->footer_code) {
            \Illuminate\Support\Facades\Log::info('Setting head/footer code changed by admin', [
                'admin_id' => auth()->id(),
                'old_head_code' => $oldHeadCode,
                'new_head_code' => $setting->head_code,
                'old_footer_code' => $oldFooterCode,
                'new_footer_code' => $setting->footer_code,
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan!');
    }
}
