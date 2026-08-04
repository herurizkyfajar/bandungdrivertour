@extends('layouts.app', ['title' => 'Invoice Settings'])

@section('content')
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <h2 style="margin:0 0 1rem;">Invoice Settings</h2>

    @if(session('success'))
      <div style="background:#dcfce7; color:#166534; padding:.75rem 1rem; border-radius:12px; margin-bottom:1rem; font-weight:600;">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('settings.invoice.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:1.25rem; margin-bottom:1rem;">
        <h3 style="margin:0 0 1rem; font-size:1rem; font-weight:700; color:#0f172a;">Company Information</h3>
        <div class="form-grid">
          <div class="col-6">
            <div class="field">
              <label for="company_name">Company Name</label>
              <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $settings->company_name) }}" required>
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="company_website">Website</label>
              <input id="company_website" type="text" name="company_website" value="{{ old('company_website', $settings->company_website) }}" placeholder="bandungdrivertour.com">
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="company_phone">Phone</label>
              <input id="company_phone" type="text" name="company_phone" value="{{ old('company_phone', $settings->company_phone) }}">
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="company_email">Email</label>
              <input id="company_email" type="email" name="company_email" value="{{ old('company_email', $settings->company_email) }}">
            </div>
          </div>
          <div class="col-12">
            <div class="field">
              <label for="company_address">Full Address</label>
              <textarea id="company_address" name="company_address" rows="3">{{ old('company_address', $settings->company_address) }}</textarea>
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="nib">Business Registration Number (NIB)</label>
              <input id="nib" type="text" name="nib" value="{{ old('nib', $settings->nib) }}">
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="ahu_certificate_number">AHU Certificate Number</label>
              <input id="ahu_certificate_number" type="text" name="ahu_certificate_number" value="{{ old('ahu_certificate_number', $settings->ahu_certificate_number) }}" placeholder="e.g., AHU-0001234-00">
            </div>
          </div>
        </div>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:1.25rem; margin-bottom:1rem;">
        <h3 style="margin:0 0 1rem; font-size:1rem; font-weight:700; color:#0f172a;">Signer</h3>
        <div class="form-grid">
          <div class="col-6">
            <div class="field">
              <label for="signer_name">Signer Name</label>
              <input id="signer_name" type="text" name="signer_name" value="{{ old('signer_name', $settings->signer_name) }}" required>
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="signer_title">Title</label>
              <input id="signer_title" type="text" name="signer_title" value="{{ old('signer_title', $settings->signer_title) }}">
            </div>
          </div>
          <div class="col-12">
            <div class="field">
              <label>Signature Image</label>
              @if($settings->signature_path)
                <div style="margin-bottom:.5rem;">
                  <img src="{{ asset('storage/' . $settings->signature_path) }}" alt="Signature" style="max-height:80px; border:1px solid #e2e8f0; border-radius:8px; padding:4px; background:#fff;">
                </div>
              @endif
              <input type="file" name="signature" accept="image/png,image/jpeg">
              <div style="font-size:.78rem; color:#6b7280; margin-top:.25rem;">PNG or JPG, max 2MB</div>
            </div>
          </div>
        </div>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:1.25rem; margin-bottom:1rem;">
        <h3 style="margin:0 0 1rem; font-size:1rem; font-weight:700; color:#0f172a;">Payment Details</h3>
        <div class="form-grid">
          <div class="col-6">
            <div class="field">
              <label for="bank_name">Bank Name</label>
              <input id="bank_name" type="text" name="bank_name" value="{{ old('bank_name', $settings->bank_name) }}">
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="bank_account_number">Account Number</label>
              <input id="bank_account_number" type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings->bank_account_number) }}">
            </div>
          </div>
          <div class="col-6">
            <div class="field">
              <label for="bank_account_name">Account Holder Name</label>
              <input id="bank_account_name" type="text" name="bank_account_name" value="{{ old('bank_account_name', $settings->bank_account_name) }}">
            </div>
          </div>
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn btn-primary">Save Settings</button>
      </div>
    </form>
  </main>
</div>
@endsection
