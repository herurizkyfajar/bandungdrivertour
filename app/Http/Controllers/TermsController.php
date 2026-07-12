<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceSetting;

class TermsController extends Controller
{
    public function edit()
    {
        $settings = InvoiceSetting::instance();
        return view('settings.terms', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = InvoiceSetting::instance();

        $data = $request->validate([
            'terms_html' => ['nullable', 'string'],
        ]);

        $settings->terms_html = $data['terms_html'] ?? null;
        $settings->save();

        return redirect()->route('settings.terms.edit')->with('success', 'Rental Duration & Service Terms updated successfully.');
    }
}
