<?php

namespace App\Http\Controllers\ApiMobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    /**
     * Helper to authenticate mobile client
     */
    private function authUser(Request $request)
    {
        $apiKey = $request->input('api_key') ?: $request->input('key');
        
        if (!$apiKey) {
            $authHeader = $request->header('Authorization');
            if ($authHeader) {
                if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                    $apiKey = $matches[1];
                } else {
                    $apiKey = $authHeader;
                }
            }
        }

        if (!$apiKey) {
            return null;
        }

        return User::where('api_key', $apiKey)->where('status', 'Active')->first();
    }

    /**
     * Retrieve user profile details.
     */
    public function show(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'country_code' => $user->country_code,
                'phone' => $user->phone,
                'balance' => number_format($user->balance, 4, '.', ''),
                'level' => $user->level,
                'status' => $user->status,
                'koneksi' => $user->koneksi,
                'api_key' => $user->api_key,
                'created_at' => $user->create_at ? $user->create_at->toDateTimeString() : null
            ]
        ]);
    }

    /**
     * Update user profile details (country code and phone).
     */
    public function updateProfile(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Format phone (remove leading 0)
        $phone = ltrim($request->phone, '0');

        $user->country_code = $request->country_code;
        $user->phone = $phone;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Account details updated successfully.',
            'data' => [
                'country_code' => $user->country_code,
                'phone' => $user->phone
            ]
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect.'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully.'
        ]);
    }

    /**
     * Regenerate user API Key.
     */
    public function regenerateApiKey(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $newApiKey = 'usr_' . Str::random(40);
        $user->api_key = $newApiKey;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'API key regenerated successfully.',
            'api_key' => $newApiKey
        ]);
    }
}
