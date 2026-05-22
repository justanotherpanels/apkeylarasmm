<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'balance' => 'numeric|min:0',
            'level' => 'required|in:Member,Admin',
            'status' => 'required|in:Active,Not-Active',
            'is_seller' => 'boolean'
        ]);

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $data['is_seller'] = $request->has('is_seller');

        User::create($data);

        return redirect()->route('admin.user.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
            'balance' => 'numeric|min:0',
            'level' => 'required|in:Member,Admin',
            'status' => 'required|in:Active,Not-Active',
            'is_seller' => 'boolean'
        ]);

        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $data['is_seller'] = $request->has('is_seller');

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'Pengguna berhasil dihapus');
    }
}
