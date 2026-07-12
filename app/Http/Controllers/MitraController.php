<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = Mitra::withCount('bookings')->latest()->paginate(10);
        return view('mitras.index', compact('mitras'));
    }

    public function show(Mitra $mitra)
    {
        $mitra->load(['bookings.vehicle', 'bookings.service', 'vehicles']);
        $bookings = $mitra->bookings()->latest()->paginate(10);
        return view('mitras.show', compact('mitra', 'bookings'));
    }

    public function create()
    {
        $appOptions = ['gojek','grab','maxim','indrive','others'];
        return view('mitras.create', compact('appOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:mitras,email'],
            'whatsapp_contact' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'apps' => ['nullable', 'array'],
            'apps.*' => ['in:gojek,grab,maxim,indrive,others'],
            'other_app' => ['nullable', 'string', 'max:255'],
        ]);
        $apps = $data['apps'] ?? [];
        if (($data['other_app'] ?? null) && !in_array('others', $apps, true)) {
            $apps[] = 'others';
        }
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('mitras', 'public');
        }
        $mitra = Mitra::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'whatsapp_contact' => $data['whatsapp_contact'],
            'photo_path' => $photoPath,
            'apps' => $apps,
            'other_app' => $data['other_app'] ?? null,
        ]);
        $existingUser = User::where('email', $data['email'])->first();
        $extraNote = '';
        if (!$existingUser) {
            $tempPassword = Str::random(10);
            User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => $tempPassword,
                'role' => 'mitra',
            ]);
            $extraNote = " Akun mitra dibuat. Password sementara: {$tempPassword}";
        } else {
            $existingUser->name = $data['full_name'];
            $existingUser->role = 'mitra';
            $existingUser->save();
            $extraNote = " Akun pengguna ditemukan dan diperbarui sebagai mitra.";
        }
        return redirect()->route('mitras.index')->with('success', 'Mitra berhasil dibuat.' . $extraNote);
    }

    public function edit(Mitra $mitra)
    {
        $appOptions = ['gojek','grab','maxim','indrive','others'];
        return view('mitras.edit', compact('mitra','appOptions'));
    }

    public function update(Request $request, Mitra $mitra)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:mitras,email,' . $mitra->id],
            'whatsapp_contact' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'apps' => ['nullable', 'array'],
            'apps.*' => ['in:gojek,grab,maxim,indrive,others'],
            'other_app' => ['nullable', 'string', 'max:255'],
        ]);
        $apps = $data['apps'] ?? [];
        if (($data['other_app'] ?? null) && !in_array('others', $apps, true)) {
            $apps[] = 'others';
        }
        if ($request->hasFile('photo')) {
            if ($mitra->photo_path) {
                Storage::disk('public')->delete($mitra->photo_path);
            }
            $mitra->photo_path = $request->file('photo')->store('mitras', 'public');
        }
        $mitra->full_name = $data['full_name'];
        $mitra->email = $data['email'];
        $mitra->whatsapp_contact = $data['whatsapp_contact'];
        $mitra->apps = $apps;
        $mitra->other_app = $data['other_app'] ?? null;
        $mitra->save();
        $user = User::where('email', $mitra->email)->first();
        if ($user) {
            $user->name = $mitra->full_name;
            if ($user->email !== $mitra->email) {
                $user->email = $mitra->email;
            }
            if ($user->role !== 'mitra') {
                $user->role = 'mitra';
            }
            $user->save();
        }
        return redirect()->route('mitras.index')->with('success', 'Mitra berhasil diperbarui dan akun pengguna disinkronkan.');
    }

    public function destroy(Mitra $mitra)
    {
        if ($mitra->photo_path) {
            Storage::disk('public')->delete($mitra->photo_path);
        }
        $mitra->delete();
        return redirect()->route('mitras.index')->with('success', 'Mitra berhasil dihapus.');
    }
}
