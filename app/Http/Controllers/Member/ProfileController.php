<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('member.account.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->full_name = $request->full_name;
        $user->country_code = $request->country_code;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('member.account.index')->with('success', 'Akun berhasil diperbarui.');
    }
}
