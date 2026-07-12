<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\InvoiceSetting;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = InvoiceSetting::instance();
        return view('settings.invoice', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = InvoiceSetting::instance();

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'company_phone' => ['nullable', 'string', 'max:255'],
            'company_email' => ['nullable', 'string', 'max:255'],
            'company_website' => ['nullable', 'string', 'max:255'],
            'nib' => ['nullable', 'string', 'max:255'],
            'signer_name' => ['required', 'string', 'max:255'],
            'signer_title' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('signature')) {
            if ($settings->signature_path) {
                Storage::disk('public')->delete($settings->signature_path);
            }
            $data['signature_path'] = $request->file('signature')->store('signatures', 'public');
        }

        unset($data['signature']);
        $settings->fill($data)->save();

        return redirect()->route('settings.invoice.edit')->with('success', 'Invoice settings updated successfully.');
    }
}
