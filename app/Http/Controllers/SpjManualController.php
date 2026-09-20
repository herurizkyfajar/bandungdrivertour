<?php

namespace App\Http\Controllers;

use App\Models\SpjManual;
use App\Models\Mitra;
use App\Models\Service;
use Illuminate\Http\Request;

class SpjManualController extends Controller
{
    public function index()
    {
        $spjManuals = SpjManual::latest()->paginate(10);
        return view('spj-manuals.index', compact('spjManuals'));
    }

    public function show(SpjManual $spjManual)
    {
        return view('spj-manuals.show', ['spjManual' => $spjManual]);
    }

    public function create()
    {
        $mitras = Mitra::orderBy('full_name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('spj-manuals.create', compact('mitras', 'services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'driver_name'       => ['required', 'string', 'max:255'],
            'customer_name'     => ['required', 'string', 'max:255'],
            'customer_contact'  => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'passenger_count'   => ['required', 'integer', 'min:1'],
            'start_date'        => ['required', 'date'],
            'pickup_time'       => ['required'],
            'pickup_address'    => ['required', 'string'],
            'service_type'      => ['nullable', 'string', 'max:255'],
            'service_duration'  => ['nullable', 'string', 'max:255'],
            'payment_plan'      => ['nullable', 'string', 'max:255'],
            'trip_details'      => ['nullable', 'string'],
        ]);

        SpjManual::create($data);

        return redirect()->route('spj-manuals.index')->with('success', 'SPJ Manual berhasil ditambahkan.');
    }

    public function edit(SpjManual $spjManual)
    {
        $mitras = Mitra::orderBy('full_name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('spj-manuals.edit', ['spjManual' => $spjManual, 'mitras' => $mitras, 'services' => $services]);
    }

    public function update(Request $request, SpjManual $spjManual)
    {
        $data = $request->validate([
            'driver_name'       => ['required', 'string', 'max:255'],
            'customer_name'     => ['required', 'string', 'max:255'],
            'customer_contact'  => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'passenger_count'   => ['required', 'integer', 'min:1'],
            'start_date'        => ['required', 'date'],
            'pickup_time'       => ['required'],
            'pickup_address'    => ['required', 'string'],
            'service_type'      => ['nullable', 'string', 'max:255'],
            'service_duration'  => ['nullable', 'string', 'max:255'],
            'payment_plan'      => ['nullable', 'string', 'max:255'],
            'trip_details'      => ['nullable', 'string'],
        ]);

        $spjManual->update($data);

        return redirect()->route('spj-manuals.index')->with('success', 'SPJ Manual berhasil diperbarui.');
    }

    public function destroy(SpjManual $spjManual)
    {
        $spjManual->delete();
        return redirect()->route('spj-manuals.index')->with('success', 'SPJ Manual berhasil dihapus.');
    }
}
