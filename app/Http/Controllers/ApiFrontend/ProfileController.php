<?php

namespace App\Http\Controllers\ApiFrontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Helper to authenticate user via API Key or Bearer Token.
     */
    private function authenticateUser(Request $request)
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
     * Fetch user profile details and remaining balance.
     */
    public function show(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key.'
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
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key.'
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
            'message' => 'Password updated successfully.'
        ]);
    }

    /**
     * Regenerate user API Key.
     */
    public function regenerateApiKey(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key.'
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
