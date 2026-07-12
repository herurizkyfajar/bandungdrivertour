<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('mitras')->latest()->paginate(10);
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $mitras = Mitra::orderBy('full_name')->get();
        return view('vehicles.create', compact('mitras'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plate_number' => ['required', 'string', 'max:50', 'unique:vehicles,plate_number'],
            'make' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'mitra_ids' => ['required', 'array', 'min:1'],
            'mitra_ids.*' => ['exists:mitras,id'],
            'price_per_day' => ['nullable', 'string'],
        ]);
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('vehicles', 'public');
        }
        $ppd = preg_replace('/\D+/', '', (string)($data['price_per_day'] ?? ''));
        $ppdVal = $ppd !== '' ? (float) $ppd : null;
        $vehicle = Vehicle::create([
            'plate_number' => $data['plate_number'],
            'make' => $data['make'] ?? null,
            'model' => $data['model'] ?? null,
            'color' => $data['color'] ?? null,
            'photo_path' => $photoPath,
            'mitra_id' => $data['mitra_ids'][0],
            'price_per_day' => $ppdVal,
        ]);
        $vehicle->mitras()->sync($data['mitra_ids']);
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dibuat.');
    }

    public function edit(Vehicle $vehicle)
    {
        $mitras = Mitra::orderBy('full_name')->get();
        $vehicle->load('mitras');
        return view('vehicles.edit', compact('vehicle', 'mitras'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'plate_number' => ['required', 'string', 'max:50', 'unique:vehicles,plate_number,' . $vehicle->id],
            'make' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'mitra_ids' => ['required', 'array', 'min:1'],
            'mitra_ids.*' => ['exists:mitras,id'],
            'price_per_day' => ['nullable', 'string'],
        ]);
        if ($request->hasFile('photo')) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $vehicle->photo_path = $request->file('photo')->store('vehicles', 'public');
        }
        $vehicle->plate_number = $data['plate_number'];
        $vehicle->make = $data['make'] ?? null;
        $vehicle->model = $data['model'] ?? null;
        $vehicle->color = $data['color'] ?? null;
        $vehicle->mitra_id = $data['mitra_ids'][0];
        $ppd = preg_replace('/\D+/', '', (string)($data['price_per_day'] ?? ''));
        $vehicle->price_per_day = $ppd !== '' ? (float) $ppd : null;
        $vehicle->save();
        $vehicle->mitras()->sync($data['mitra_ids']);
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}
