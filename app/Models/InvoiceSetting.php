<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'nib',
        'signer_name',
        'signer_title',
        'signature_path',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'terms_html',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'company_name' => 'Bandung Driver Tour',
            'company_address' => 'Linggar Complex, Ciobeber Subdistrict, South Cimahi District, West Java Province, Indonesia 40531',
            'company_phone' => '+62 8121 4270 745',
            'company_email' => 'bandungdrivertour@gmail.com',
            'company_website' => 'bandungdrivertour.com',
            'signer_name' => 'Aldi Maulana',
            'signer_title' => 'CFO',
            'bank_name' => 'Bank Central Asia (Bank Transfer)',
            'bank_account_number' => '1394304240',
            'bank_account_name' => 'Aldi Maulana',
        ]);
    }
}
