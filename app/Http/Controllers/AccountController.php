<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'super_admin') {
            $accounts = User::latest()->paginate(10);
        } else {
            $accounts = User::where('id', Auth::id())->paginate(10);
        }
        return view('accounts.index', compact('accounts'));
    }

    public function show(User $account)
    {
        $account->load('itineraries.days.activities');
        return view('accounts.show', compact('account'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:super_admin,mitra,user'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(User $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, User $account)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $account->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:super_admin,mitra,user'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $account->update($updateData);

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(User $account)
    {
        if ($account->id === auth()->id()) {
            return redirect()->route('accounts.index')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
