<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::latest()->paginate(10);
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'website' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('groups', 'public');
        }
        Group::create([
            'name' => $data['name'],
            'logo_path' => $logoPath,
            'website' => $data['website'] ?? null,
            'contact' => $data['contact'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
        return redirect()->route('groups.index')->with('success', 'Group created successfully.');
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'website' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);
        if ($request->hasFile('logo')) {
            if ($group->logo_path) {
                Storage::disk('public')->delete($group->logo_path);
            }
            $group->logo_path = $request->file('logo')->store('groups', 'public');
        }
        $group->name = $data['name'];
        $group->website = $data['website'] ?? null;
        $group->contact = $data['contact'] ?? null;
        $group->address = $data['address'] ?? null;
        $group->save();
        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        if ($group->logo_path) {
            Storage::disk('public')->delete($group->logo_path);
        }
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}
