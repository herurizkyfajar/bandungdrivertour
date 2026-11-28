@extends('layouts.app', ['title' => 'Booking Form'])

@section('content')
@php($bdtWaDigits = '6282129292884')
<style>
    body.homepage-with-header .homepage-header { display: none; }
    body.homepage-with-header main.container { margin-top: 0; max-width: 100%; padding-left: 0; padding-right: 0; }

    .booking-page {
        width: 100%;
        background: linear-gradient(180deg, #eef5fd 0%, #f4faf7 55%, #ffffff 100%);
        padding-bottom: 1rem;
    }

    /* ---------- Hero ---------- */
    .bhero {
        position: relative;
        overflow: hidden;
        color: #fff;
        background:
            linear-gradient(100deg, rgba(6, 40, 110, .97) 0%, rgba(9, 62, 160, .93) 34%, rgba(13, 82, 196, .62) 58%, rgba(7, 44, 118, .38) 100%),
            url('{{ asset('assets/img/gedung-sate-drive.png') }}') right 72% / cover no-repeat;
    }
    .bhero-inner {
        max-width: 1180px;
        margin: 0 auto;
        padding: 1.6rem 1.25rem 3.4rem;
        position: relative;
        z-index: 2;
    }
    .bhero-brand { display: inline-flex; align-items: center; margin-bottom: 1.25rem; }
    .bhero-brand img { width: 168px; height: auto; display: block; }
    .bhero h1 {
        margin: 0 0 .5rem;
        font-size: clamp(2rem, 4vw, 2.9rem);
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.1;
    }
    .bhero h1 span { color: #7ee04a; }
    .bhero p {
        margin: 0;
        max-width: 30rem;
        font-size: 1rem;
        line-height: 1.55;
        color: rgba(255, 255, 255, .92);
    }
    .bhero-chips { display: flex; flex-wrap: wrap; gap: .55rem; margin-top: 1.4rem; }
    .bhero-chip {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .85rem .5rem .5rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .22);
        font-size: .85rem;
        font-weight: 700;
        backdrop-filter: blur(6px);
    }
    .bhero-chip i {
        display: grid;
        place-items: center;
        width: 1.55rem;
        height: 1.55rem;
        border-radius: 50%;
        background: #fff;
        color: #0b4bb5;
        font-size: .8rem;
    }
    .bhero-wave {
        position: absolute;
        left: -6%;
        right: -6%;
        bottom: -34px;
        height: 68px;
        border-radius: 100% 100% 0 0 / 100% 100% 0 0;
        background: linear-gradient(90deg, #2fbf5b 0%, #35c07a 32%, #2f9dff 100%);
        z-index: 1;
    }

    /* ---------- Layout ---------- */
    .bwrap {
        max-width: 1180px;
        margin: -1.6rem auto 0;
        padding: 0 1.25rem;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 348px;
        gap: 1.35rem;
        align-items: start;
        position: relative;
        z-index: 3;
    }
    .bcard {
        background: #fff;
        border: 1px solid #e3ecf7;
        border-radius: 22px;
        box-shadow: 0 22px 48px rgba(15, 55, 115, .12);
        overflow: hidden;
    }
    .bcard-head {
        display: flex;
        gap: .85rem;
        align-items: flex-start;
        padding: 1.35rem 1.4rem .35rem;
    }
    .bcard-head .bicon-lg {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 2.9rem;
        height: 2.9rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #1d6fe0, #0e4fbf);
        color: #fff;
        font-size: 1.3rem;
        box-shadow: 0 10px 20px rgba(20, 90, 200, .28);
    }
    .bcard-head h2 { margin: 0; font-size: 1.35rem; font-weight: 800; letter-spacing: -.01em; color: #10233f; }
    .bcard-head .subtitle { margin-top: .15rem; font-size: .9rem; }
    .bform { padding: .85rem 1.4rem 1.4rem; }

    /* ---------- Sections ---------- */
    .bsec { margin-top: 1.15rem; }
    .bsec-head {
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .6rem .85rem;
        border-radius: 12px;
        background: linear-gradient(90deg, #dbe9fe 0%, #eaf3ff 65%, #f4f9ff 100%);
        color: #1a3f80;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: -.01em;
    }
    .bnum {
        display: grid;
        place-items: center;
        width: 1.6rem;
        height: 1.6rem;
        border-radius: 50%;
        background: #2563eb;
        color: #fff;
        font-size: .85rem;
        font-weight: 700;
        flex: 0 0 auto;
    }
    .bsec-head .bi-chevron-right { margin-left: auto; color: #93b4e6; font-size: .8rem; }
    .bsec-body { padding-top: .85rem; }

    /* ---------- Fields ---------- */
    .booking-page .form-grid { gap: .85rem 1rem; }
    .booking-page label { font-size: .85rem; font-weight: 700; color: #1e2c44; margin-bottom: .35rem; }
    .bcontrol { position: relative; }
    .bcontrol > i {
        position: absolute;
        left: .8rem;
        top: 50%;
        transform: translateY(-50%);
        color: #8aa2bd;
        font-size: .95rem;
        pointer-events: none;
        z-index: 2;
    }
    .booking-page .bcontrol > input,
    .booking-page .bcontrol > select {
        width: 100%;
        height: 46px;
        padding: .6rem .8rem .6rem 2.4rem;
        border-radius: 10px;
        border: 1px solid #d8e4f2;
        background: #fdfefe;
        color: #1f2937;
        font-size: .92rem;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .booking-page .bcontrol > select { padding-right: 2rem; appearance: auto; }
    .booking-page .bcontrol > input:focus,
    .booking-page .bcontrol > select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
        background: #fff;
    }
    .booking-page .bcontrol input::placeholder { color: #a8b8cc; }
    .booking-page .bcontrol.date > i { z-index: 3; }

    /* Country searchable select */
    .booking-page .search-select { position: relative; }
    .booking-page .search-select .search-input {
        width: 100%;
        height: 46px;
        padding: .6rem .8rem .6rem 2.4rem;
        border-radius: 10px;
        border: 1px solid #d8e4f2;
        background: #fdfefe;
        font-size: .92rem;
        color: #1f2937;
    }
    .booking-page .search-select .search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }
    .booking-page .search-select > i {
        position: absolute;
        left: .8rem;
        top: 50%;
        transform: translateY(-50%);
        color: #8aa2bd;
        font-size: .95rem;
        pointer-events: none;
        z-index: 2;
    }
    .booking-page .search-select .search-dropdown { border-color: #cbdff5; border-radius: 10px; }

    /* Pickup time */
    .btime { display: flex; gap: .35rem; align-items: center; flex-wrap: nowrap; }
    .booking-page .btime select,
    .booking-page .btime input {
        width: auto;
        height: 46px;
        padding: .4rem .45rem;
        border: 1px solid #d8e4f2;
        border-radius: 10px;
        font-size: .9rem;
        color: #1f2937;
        background: #fdfefe;
    }
    .booking-page .btime select { width: 66px; flex-shrink: 0; }
    .booking-page .btime input { width: 58px; flex-shrink: 0; text-align: center; }
    .btime .bcolon { font-weight: 700; font-size: 1.05rem; color: #7d92ab; flex-shrink: 0; }
    .bampm { display: flex; border: 1px solid #d8e4f2; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
    .bampm label { margin: 0; cursor: pointer; font-weight: 600; }
    .pt-ampm-btn {
        display: block;
        padding: .8rem .85rem;
        font-size: .82rem;
        font-weight: 700;
        color: #64748b;
        background: #f8fafc;
        transition: all .15s;
        white-space: nowrap;
    }
    .pt-ampm-btn:hover { background: #e8f0fb; }
    input[name="pt_ampm"]:checked + .pt-ampm-btn { background: #2563eb; color: #fff; }

    /* Service picker */
    .booking-page .service-select {
        width: 100%;
        height: 46px;
        padding: .5rem .8rem;
        border: 1px solid #d8e4f2;
        border-radius: 10px;
        font-size: .92rem;
        background: #fdfefe;
        color: #1f2937;
    }
    .booking-page .service-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }
    .booking-page .service-tag { background: #e2ecfd !important; color: #1d5fce !important; }
    .booking-page .service-tag-remove { color: #1d5fce !important; }

    /* WYSIWYG */
    .wysiwyg { border: 1px solid #d8e4f2; border-radius: 12px; overflow: hidden; background: #fdfefe; }
    .w-toolbar { display: flex; gap: .25rem; flex-wrap: wrap; padding: .45rem; background: #f3f8ff; border-bottom: 1px solid #e3ecf7; }
    .w-btn { display: inline-flex; align-items: center; gap: .35rem; padding: .4rem .55rem; border: 1px solid #e1eaf5; border-radius: 7px; background: #fff; color: #26364d; font-size: .85rem; }
    .w-btn:hover { background: #eaf2fd; }
    .w-content { min-height: 150px; padding: .8rem .9rem; line-height: 1.6; font-size: .93rem; outline: none; background: #fff; }
    .w-content:empty:before { content: attr(data-placeholder); color: #a8b8cc; }
    .w-sep { width: 1px; height: 26px; background: #e1eaf5; margin: 0 .25rem; }
    .w-content ul { padding-left: 1.25rem; list-style: disc; margin: .25rem 0; }
    .w-content ol { padding-left: 1.25rem; list-style: decimal; margin: .25rem 0; }
    .w-content li { margin: .15rem 0; }

    /* Submit */
    .bsubmit-wrap { margin-top: 1.4rem; }
    .bsubmit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        width: 100%;
        padding: .95rem 1.2rem;
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
        letter-spacing: .01em;
        cursor: pointer;
        background: linear-gradient(90deg, #26b56b 0%, #1d7be0 58%, #1d4ed8 100%);
        box-shadow: 0 12px 24px rgba(24, 96, 190, .25);
        transition: filter .15s ease, transform .15s ease;
    }
    .bsubmit:hover { filter: brightness(1.05); transform: translateY(-1px); }
    .bsubmit:disabled { opacity: .75; cursor: wait; transform: none; }

    /* ---------- Sidebar ---------- */
    .bside { display: grid; gap: 1rem; position: sticky; top: 1rem; }
    .bcar {
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(160deg, #3ea3f0 0%, #1a6fd6 55%, #0d4ba8 100%);
        box-shadow: 0 18px 36px rgba(15, 55, 115, .18);
        line-height: 0;
    }
    .bcar img { width: 100%; height: auto; display: block; mix-blend-mode: normal; }
    .bpanel {
        background: #fff;
        border: 1px solid #e3ecf7;
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(15, 55, 115, .07);
        padding: 1.15rem 1.2rem;
    }
    .bpanel-title { display: flex; align-items: center; gap: .6rem; margin: 0 0 .85rem; font-size: 1.02rem; font-weight: 800; color: #10233f; line-height: 1.3; }
    .bpanel-title .bbadge {
        display: grid;
        place-items: center;
        width: 2.1rem;
        height: 2.1rem;
        flex: 0 0 auto;
        border-radius: 50%;
        background: #e7f8ec;
        color: #22a552;
        font-size: 1.1rem;
    }
    .bbenefits { list-style: none; margin: 0; padding: 0; display: grid; gap: .7rem; }
    .bbenefits li { display: flex; align-items: flex-start; gap: .6rem; font-size: .88rem; color: #34455e; line-height: 1.45; }
    .bbenefits i { color: #2b74d8; font-size: 1rem; margin-top: .1rem; }
    .bpanel.help .bbadge { background: #e8f1fe; color: #2b74d8; }
    .bpanel.help p { margin: 0 0 .9rem; font-size: .86rem; color: #5a6c84; line-height: 1.55; }
    .bwa {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .8rem 1rem;
        border-radius: 10px;
        background: #16a34a;
        color: #fff;
        font-weight: 700;
        font-size: .92rem;
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(22, 163, 74, .22);
        transition: filter .15s ease;
    }
    .bwa:hover { filter: brightness(1.06); color: #fff; }

    /* ---------- Thank you ---------- */
    .bthanks { grid-column: 1 / -1; text-align: center; padding: 2.6rem 1.5rem; }
    .bthanks .bbadge-lg {
        display: grid;
        place-items: center;
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: #e7f8ec;
        color: #22a552;
        font-size: 2rem;
    }
    .bthanks h1 { margin: 0 0 .5rem; font-size: 1.7rem; font-weight: 800; color: #10233f; }
    .bthanks .subtitle { max-width: 30rem; margin: 0 auto; }

    /* ---------- Groups ---------- */
    .bgroups { max-width: 1180px; margin: 1.8rem auto 0; padding: 0 1.25rem 1.5rem; text-align: center; }
    .bgroups-title { display: flex; align-items: center; justify-content: center; gap: .9rem; margin: 0 0 1.15rem; }
    .bgroups-title span { flex: 0 0 82px; height: 3px; border-radius: 99px; background: linear-gradient(90deg, rgba(47,191,91,0), #2fbf5b); }
    .bgroups-title span:last-child { background: linear-gradient(90deg, #2fbf5b, rgba(47,191,91,0)); }
    .bgroups-title h2 { margin: 0; font-size: 1.2rem; font-weight: 800; color: #14304f; }
    .bgroups-logos { display: flex; align-items: center; justify-content: center; gap: 2.5rem; flex-wrap: wrap; }
    .bgroups-logos img { height: 96px; width: auto; object-fit: contain; }

    @media (max-width: 1024px) {
        .bwrap { grid-template-columns: minmax(0, 1fr); }
        .bside { position: static; grid-template-columns: repeat(2, minmax(0, 1fr)); align-items: start; }
        .bcar { grid-column: 1 / -1; }
    }
    @media (max-width: 768px) {
        .bhero-inner { padding: 1.25rem 1rem 2.8rem; }
        .bhero-brand img { width: 140px; }
        .bwrap { padding: 0 .85rem; gap: 1rem; }
        .bform { padding: .75rem 1rem 1.15rem; }
        .bcard-head { padding: 1.1rem 1rem .25rem; }
        .bside { grid-template-columns: minmax(0, 1fr); }
        .bgroups-logos { gap: 1.35rem; }
        .bgroups-logos img { height: 74px; }
    }
    @media (max-width: 480px) {
        .booking-page .btime select { width: 58px; }
        .booking-page .btime input { width: 48px; }
        .pt-ampm-btn { padding: .8rem .6rem; font-size: .76rem; }
    }
</style>

<div class="booking-page">
    <section class="bhero">
        <div class="bhero-inner">
            <a href="{{ url('/') }}" class="bhero-brand" aria-label="Bandung Driver Tour home">
                <img src="{{ asset('Logo-Bandung-Driver-Tour.webp') }}" alt="Bandung Driver Tour">
            </a>
            <h1>Booking <span>Form</span></h1>
            <p>Fill in your trip details so we can arrange your car and driver in Bandung quickly, safely, and comfortably.</p>
            <div class="bhero-chips">
                <span class="bhero-chip"><i class="bi bi-clock-history"></i>Fast Process</span>
                <span class="bhero-chip"><i class="bi bi-shield-check"></i>Secure Data</span>
                <span class="bhero-chip"><i class="bi bi-headset"></i>Quick Response</span>
            </div>
        </div>
        <div class="bhero-wave"></div>
    </section>

    @if(session('success'))
    <div class="bwrap">
        <div class="bcard bthanks">
            <div class="bbadge-lg"><i class="bi bi-check2-circle"></i></div>
            <h1>Thank You</h1>
            <div class="subtitle">{{ session('success') }}</div>
        </div>
    </div>
    @else
    <div class="bwrap">
        <div class="bcard">
            <div class="bcard-head">
                <div class="bicon-lg"><i class="bi bi-calendar2-check"></i></div>
                <div>
                    <h2>Booking Form</h2>
                    <div class="subtitle">Fill in your trip details for faster processing.</div>
                    <div class="subtitle">Our admin will contact you via WhatsApp using the phone number you provide.</div>
                </div>
            </div>

            <form class="bform" method="POST" action="{{ route('booking.store') }}">
                @csrf

                <section class="bsec">
                    <div class="bsec-head"><span class="bnum">1</span> Booking Data <i class="bi bi-chevron-right"></i></div>
                    <div class="bsec-body form-grid">
                        <div class="col-6">
                            <div class="field">
                                <label for="customer_name">Customer Name <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-person"></i>
                                    <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Full name" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="field">
                                <label for="contact_number">Contact Number <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-whatsapp"></i>
                                    <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="Phone number" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bsec">
                    <div class="bsec-head"><span class="bnum">2</span> Trip Details <i class="bi bi-chevron-right"></i></div>
                    <div class="bsec-body form-grid">
                        <div class="col-4">
                            <div class="field">
                                <label for="number_of_passengers">Number of Passengers <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-people"></i>
                                    <input id="number_of_passengers" type="number" name="number_of_passengers" value="{{ old('number_of_passengers') }}" placeholder="Number of passengers" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field">
                                <label for="country_search">Country of Origin <span style="color:red;">*</span></label>
                                <div class="search-select" id="country_select">
                                    <i class="bi bi-globe2"></i>
                                    <input id="country_search" class="search-input" type="text" placeholder="Type to search country" autocomplete="off" value="{{ old('country_of_origin') }}" required>
                                    <input type="hidden" name="country_of_origin" id="country_of_origin_hidden" value="{{ old('country_of_origin') }}">
                                    <div class="search-dropdown" id="country_dropdown"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field">
                                <label for="pickup_location">Pickup Location <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-geo-alt"></i>
                                    <input id="pickup_location" type="text" name="pickup_location" value="{{ old('pickup_location') }}" placeholder="Pickup location" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="field">
                                <label for="start_date">Start Date <span style="color:red;">*</span></label>
                                <div class="bcontrol date">
                                    <i class="bi bi-calendar3"></i>
                                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="field">
                                <label for="end_date">End Date <span style="color:red;">*</span></label>
                                <div class="bcontrol date">
                                    <i class="bi bi-calendar3"></i>
                                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="field">
                                <label for="pickup_time">Pickup Time (12H) <span style="color:red;">*</span></label>
                                <div class="btime">
                                    <select id="pt_hour" name="pt_hour" required>
                                        <option value="">HH</option>
                                        @foreach(range(1,12) as $h)
                                            <option value="{{ $h }}">{{ $h }}</option>
                                        @endforeach
                                    </select>
                                    <span class="bcolon">:</span>
                                    <input id="pt_min" name="pt_min" type="text" inputmode="numeric" maxlength="2" placeholder="MM" required>
                                    <div class="bampm">
                                        <label for="pt_ampm_am">
                                            <input type="radio" id="pt_ampm_am" name="pt_ampm" value="AM" required style="display:none;">
                                            <span class="pt-ampm-btn">AM</span>
                                        </label>
                                        <label for="pt_ampm_pm">
                                            <input type="radio" id="pt_ampm_pm" name="pt_ampm" value="PM" style="display:none;">
                                            <span class="pt-ampm-btn">PM</span>
                                        </label>
                                    </div>
                                </div>
                                <input type="hidden" id="pickup_time" name="pickup_time" value="{{ old('pickup_time') }}">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bsec">
                    <div class="bsec-head"><span class="bnum">3</span> Vehicle &amp; Services <i class="bi bi-chevron-right"></i></div>
                    <div class="bsec-body form-grid">
                        <div class="col-6">
                            <div class="field">
                                <label for="vehicle_id">Car of Choice <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-car-front"></i>
                                    <select id="vehicle_id" name="vehicle_id" required>
                                        <option value="">Select car</option>
                                        @foreach($vehicles ?? [] as $v)
                                            <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                                {{ $v->make }} {{ $v->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="field">
                                <label>Services <span style="color:red;">*</span> <small style="color:var(--muted);font-weight:400;">(can select multiple)</small></label>
                                <div class="service-picker" data-name="service_ids[]">
                                    <select class="service-select">
                                        <option value="">Select service...</option>
                                        @foreach($services ?? [] as $service)
                                            <option value="{{ $service->id }}" data-name="{{ $service->name }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="service-tags" style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.5rem;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bsec">
                    <div class="bsec-head"><span class="bnum">4</span> Trip Plan <i class="bi bi-chevron-right"></i></div>
                    <div class="bsec-body form-grid">
                        <div class="col-12">
                            <div class="field">
                                <label for="travel_plans_editor">Travel Plans</label>
                                <div class="wysiwyg" id="travel_plans_wysiwyg">
                                    <div class="w-toolbar">
                                        <button type="button" class="w-btn" data-cmd="bold">B</button>
                                        <button type="button" class="w-btn" data-cmd="italic"><span style="font-style:italic;">I</span></button>
                                        <button type="button" class="w-btn" data-cmd="underline"><span style="text-decoration:underline;">U</span></button>
                                        <div class="w-sep"></div>
                                        <button type="button" class="w-btn" data-cmd="formatBlock" data-value="h2">H2</button>
                                        <button type="button" class="w-btn" data-cmd="formatBlock" data-value="blockquote">Quote</button>
                                        <div class="w-sep"></div>
                                        <button type="button" class="w-btn" data-cmd="insertUnorderedList">• List</button>
                                        <button type="button" class="w-btn" data-cmd="insertOrderedList">1. List</button>
                                        <div class="w-sep"></div>
                                        <button type="button" class="w-btn" id="add_link_btn">Link</button>
                                        <button type="button" class="w-btn" id="clear_format_btn">Clear</button>
                                        <div style="margin-left:auto; display:flex; gap:.25rem;">
                                            <button type="button" class="w-btn" data-cmd="undo">Undo</button>
                                            <button type="button" class="w-btn" data-cmd="redo">Redo</button>
                                        </div>
                                    </div>
                                    <div id="travel_plans_editor" class="w-content" contenteditable="true" data-placeholder="Describe your trip plan, destinations, and duration.">{!! old('travel_plans') !!}</div>
                                </div>
                                <input type="hidden" name="travel_plans" id="travel_plans_input" value="{{ old('travel_plans') }}">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bsec">
                    <div class="bsec-head"><span class="bnum">5</span> Other Information <i class="bi bi-chevron-right"></i></div>
                    <div class="bsec-body form-grid">
                        <div class="col-6">
                            <div class="field">
                                <label for="info_source">Where do you get the information from? <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-megaphone"></i>
                                    <select name="info_source" id="info_source" required>
                                        <option value="">Select source</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="ai">AI</option>
                                        <option value="website">Website</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6" id="info_source_other_wrap" style="display:none;">
                            <div class="field">
                                <label for="info_source_other">Specify Source <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-pencil"></i>
                                    <input id="info_source_other" type="text" name="info_source_other" value="{{ old('info_source_other') }}" placeholder="e.g., friend recommendation">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="field">
                                <label for="group_id">Company <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-building"></i>
                                    <select id="group_id" name="group_id" required>
                                        <option value="">Select company</option>
                                        @foreach($groups as $g)
                                            <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>
                                                {{ $g->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="field">
                                <label for="payment_plan">Payment Plan <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-credit-card"></i>
                                    <select id="payment_plan" name="payment_plan" required>
                                        <option value="down_payment" {{ old('payment_plan', 'down_payment') == 'down_payment' ? 'selected' : '' }}>Down Payment</option>
                                        <option value="payment_full_transfer" {{ old('payment_plan') == 'payment_full_transfer' ? 'selected' : '' }}>Payment Full Transfer</option>
                                        <option value="payment_full_on_driver" {{ old('payment_plan') == 'payment_full_on_driver' ? 'selected' : '' }}>Payment Full On Driver</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6" id="down_payment_amount_wrap" style="display: {{ old('payment_plan', 'down_payment') === 'down_payment' ? 'block' : 'none' }};">
                            <div class="field">
                                <label for="down_payment_amount">Down Payment Amount (IDR) <span style="color:red;">*</span></label>
                                <div class="bcontrol">
                                    <i class="bi bi-cash-coin"></i>
                                    <input id="down_payment_amount" type="text" inputmode="numeric" name="down_payment_amount" value="{{ old('down_payment_amount') }}" placeholder="Enter down payment amount">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="bsubmit-wrap">
                    <button type="submit" id="submit_btn" class="bsubmit">
                        <i class="bi bi-send"></i> Submit Booking
                    </button>
                </div>
            </form>
        </div>

        <aside class="bside">
            <div class="bcar">
                <img src="{{ asset('assets/img/booking-avanza.jpg') }}" alt="Bandung Driver Tour car">
            </div>

            <div class="bpanel">
                <h3 class="bpanel-title"><span class="bbadge"><i class="bi bi-check-lg"></i></span> Why Book with Bandung Driver Tour</h3>
                <ul class="bbenefits">
                    <li><i class="bi bi-car-front"></i><span>Professional &amp; experienced drivers</span></li>
                    <li><i class="bi bi-shield-check"></i><span>Comfortable, well-maintained vehicles</span></li>
                    <li><i class="bi bi-geo-alt"></i><span>Explore Bandung &amp; nearby destinations</span></li>
                    <li><i class="bi bi-lightning-charge"></i><span>Easy &amp; fast booking process</span></li>
                    <li><i class="bi bi-headset"></i><span>Responsive customer support</span></li>
                </ul>
            </div>

            <div class="bpanel help">
                <h3 class="bpanel-title"><span class="bbadge"><i class="bi bi-headset"></i></span> Need Help?</h3>
                <p>Contact us via WhatsApp for quick consultation and booking.</p>
                <a class="bwa" href="https://wa.me/{{ $bdtWaDigits }}" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp"></i> Chat via WhatsApp <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </aside>
    </div>
    @endif

    @php($bdtGroups = \App\Models\Group::whereNotNull('logo_path')->orderBy('name')->get())
    @if($bdtGroups->count())
    <div class="bgroups">
        <div class="bgroups-title">
            <span></span>
            <h2>Bandung Driver Tour Groups</h2>
            <span></span>
        </div>
        <div class="bgroups-logos">
            @foreach($bdtGroups as $g)
                @if($g->website)
                    <a href="{{ $g->website }}" target="_blank" rel="noopener" title="{{ $g->name }}">
                        <img src="{{ asset('storage/' . $g->logo_path) }}" alt="{{ $g->name }}">
                    </a>
                @else
                    <img src="{{ asset('storage/' . $g->logo_path) }}" alt="{{ $g->name }}" title="{{ $g->name }}">
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>

@unless(session('success'))
<script>
  document.getElementById('submit_btn').closest('form').addEventListener('submit', function() {
    var btn = document.getElementById('submit_btn');
    btn.disabled = true;
    btn.innerHTML = 'Submitting...';
  });
</script>
<script>
  (function(){
    var infoSource = document.getElementById('info_source');
    if (!infoSource) return;
    infoSource.addEventListener('change', function() {
      var otherWrap = document.getElementById('info_source_other_wrap');
      var otherInput = document.getElementById('info_source_other');
      if (this.value === 'other') {
        otherWrap.style.display = 'block';
        otherInput.required = true;
      } else {
        otherWrap.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
      }
    });
  })();
const COUNTRIES = [
  "Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria",
  "Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan",
  "Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia",
  "Cameroon","Canada","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Congo-Brazzaville)","Costa Rica",
  "Croatia","Cuba","Cyprus","Czechia","Democratic Republic of the Congo","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador",
  "Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Fiji","Finland","France",
  "Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau",
  "Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland",
  "Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan",
  "Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Madagascar",
  "Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia",
  "Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal",
  "Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Korea","North Macedonia","Norway","Oman","Pakistan",
  "Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania",
  "Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal",
  "Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Korea",
  "South Sudan","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria","Taiwan","Tajikistan",
  "Tanzania","Thailand","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu",
  "Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela",
  "Vietnam","Yemen","Zambia","Zimbabwe"
];
const countrySelect = document.getElementById('country_select');
const countrySearch = document.getElementById('country_search');
const countryHidden = document.getElementById('country_of_origin_hidden');
const countryDropdown = document.getElementById('country_dropdown');
function renderCountryList(items) {
  countryDropdown.innerHTML = '';
  if (!items.length) {
    const empty = document.createElement('div');
    empty.className = 'search-empty';
    empty.textContent = 'No results';
    countryDropdown.appendChild(empty);
    return;
  }
  items.forEach(function(c) {
    const el = document.createElement('div');
    el.className = 'search-item';
    el.textContent = c;
    el.addEventListener('click', function() {
      countrySearch.value = c;
      countryHidden.value = c;
      countrySelect.classList.remove('open');
    });
    countryDropdown.appendChild(el);
  });
}
function filterCountries(q) {
  const query = (q || '').toLowerCase().trim();
  if (!query) return COUNTRIES.slice();
  return COUNTRIES.filter(c => c.toLowerCase().includes(query));
}
function syncCountry() {
  countryHidden.value = (countrySearch.value || '').trim();
}
countrySearch.addEventListener('focus', function() {
  countrySelect.classList.add('open');
  renderCountryList(filterCountries(countrySearch.value));
});
countrySearch.addEventListener('input', function() {
  syncCountry();
  countrySelect.classList.add('open');
  renderCountryList(filterCountries(countrySearch.value));
});
countrySearch.addEventListener('change', syncCountry);
countrySearch.addEventListener('blur', syncCountry);
document.addEventListener('click', function(e) {
  if (!countrySelect.contains(e.target)) {
    countrySelect.classList.remove('open');
  }
});
countrySearch.closest('form').addEventListener('submit', syncCountry);
renderCountryList(filterCountries(countrySearch.value));
</script>
<script>
  (function(){
    var ptHour = document.getElementById('pt_hour');
    var ptMin = document.getElementById('pt_min');
    var ptHidden = document.getElementById('pickup_time');
    if (!ptHour || !ptMin || !ptHidden) return;

    function getAmPm() {
      var checked = document.querySelector('input[name="pt_ampm"]:checked');
      return checked ? checked.value : '';
    }
    function to24(h, m, ap) {
      if (!h || !ap) return '';
      var hr = parseInt(h, 10);
      if (ap === 'AM' && hr === 12) hr = 0;
      else if (ap === 'PM' && hr !== 12) hr += 12;
      return String(hr).padStart(2,'0') + ':' + String(m || 0).padStart(2,'0');
    }
    function from24(val) {
      if (!val) return;
      var parts = val.split(':');
      var hr = parseInt(parts[0], 10);
      var m = parseInt(parts[1], 10) || 0;
      var ap = hr >= 12 ? 'PM' : 'AM';
      var h12 = hr % 12; if (h12 === 0) h12 = 12;
      ptHour.value = h12;
      ptMin.value = m;
      var radios = document.querySelectorAll('input[name="pt_ampm"]');
      radios.forEach(function(r){ r.checked = r.value === ap; });
    }
    function sync() {
      ptHidden.value = to24(ptHour.value, ptMin.value, getAmPm());
    }
    ptHour.addEventListener('change', sync);
    ptMin.addEventListener('input', function(){
      this.value = this.value.replace(/\D/g,'');
      var v = parseInt(this.value, 10);
      if (v > 60) this.value = '60';
      if (this.value.length > 2) this.value = this.value.slice(0,2);
      sync();
    });
    document.querySelectorAll('input[name="pt_ampm"]').forEach(function(r){
      r.addEventListener('change', sync);
    });

    if (ptHidden.value) from24(ptHidden.value);
  })();
</script>
<script>
  (function(){
    var paymentPlanEl = document.getElementById('payment_plan');
    var dpWrapEl = document.getElementById('down_payment_amount_wrap');
    var dpInputEl = document.getElementById('down_payment_amount');
    if (!paymentPlanEl || !dpWrapEl || !dpInputEl) return;
    function toggleDpField() {
      var isDownPayment = paymentPlanEl.value === 'down_payment';
      dpWrapEl.style.display = isDownPayment ? 'block' : 'none';
      dpInputEl.required = isDownPayment;
      if (!isDownPayment) dpInputEl.value = '';
    }
    paymentPlanEl.addEventListener('change', toggleDpField);
    toggleDpField();
  })();
</script>
<script>
  const sd = document.getElementById('start_date');
  const ed = document.getElementById('end_date');
  function syncMin() {
    if (sd && ed) {
      ed.min = sd.value || '';
      if (ed.value && sd.value && ed.value < sd.value) {
        ed.value = sd.value;
      }
    }
  }
  sd && sd.addEventListener('change', syncMin);
  document.addEventListener('DOMContentLoaded', syncMin);
</script>
<script>
  const editor = document.getElementById('travel_plans_editor');
  const hidden = document.getElementById('travel_plans_input');
  document.querySelectorAll('#travel_plans_wysiwyg [data-cmd]').forEach(btn => {
    btn.addEventListener('click', function() {
      const cmd = this.getAttribute('data-cmd');
      const val = this.getAttribute('data-value');
      document.execCommand(cmd, false, val || null);
      hidden.value = editor.innerHTML;
    });
  });
  document.getElementById('add_link_btn').addEventListener('click', function() {
    const url = prompt('Enter URL');
    if (url) {
      document.execCommand('createLink', false, url);
      hidden.value = editor.innerHTML;
    }
  });
  document.getElementById('clear_format_btn').addEventListener('click', function() {
    document.execCommand('removeFormat', false, null);
    hidden.value = editor.innerHTML;
  });
  editor.addEventListener('input', function() {
    hidden.value = editor.innerHTML;
  });
  // Pastikan value tersimpan saat submit
  editor.closest('form').addEventListener('submit', function() {
    hidden.value = editor.innerHTML;
  });
</script>
<script>
  (function(){
    var el = document.getElementById('down_payment_amount');
    if (!el) return;
    function fmt(v){
      var s = String(v || '').replace(/\D/g,'');
      if (!s) return '';
      var n = parseInt(s, 10);
      if (!isFinite(n)) return '';
      return new Intl.NumberFormat('id-ID').format(n);
    }
    function unfmt(v){
      return String(v || '').replace(/\D/g,'');
    }
    el.value = fmt(el.value);
    el.addEventListener('input', function(){ this.value = fmt(this.value); });
    var f = el.closest('form');
    if (f) {
      f.addEventListener('submit', function(){ el.value = unfmt(el.value); });
    }
  })();
</script>
<script>
document.querySelectorAll('.service-picker').forEach(function(picker){
  var select = picker.querySelector('.service-select');
  var tagsContainer = picker.querySelector('.service-tags');
  var inputName = picker.dataset.name;

  function refreshOptions(){
    var selected = Array.from(tagsContainer.querySelectorAll('.service-tag')).map(function(t){ return t.dataset.id; });
    Array.from(select.options).forEach(function(opt){
      if(!opt.value) return;
      opt.disabled = selected.indexOf(opt.value) !== -1;
    });
  }

  select.addEventListener('change', function(){
    var opt = this.options[this.selectedIndex];
    if(!opt || !opt.value) return;
    var id = opt.value;
    var name = opt.dataset.name || opt.text;
    if(tagsContainer.querySelector('[data-id="'+id+'"]')){ this.value=''; return; }

    var tag = document.createElement('span');
    tag.className = 'service-tag';
    tag.dataset.id = id;
    tag.style.cssText = 'display:inline-flex;align-items:center;gap:.3rem;background:#e0e7ff;color:#2563eb;padding:.25rem .6rem;border-radius:8px;font-size:.82rem;font-weight:600;';
    tag.innerHTML = name + ' <button type="button" class="service-tag-remove" style="background:none;border:none;color:#2563eb;font-size:1rem;line-height:1;padding:0;cursor:pointer;">&times;</button>';
    tagsContainer.appendChild(tag);

    var hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = inputName;
    hidden.value = id;
    tag.appendChild(hidden);

    tag.querySelector('.service-tag-remove').addEventListener('click', function(){
      tag.remove();
      refreshOptions();
    });

    this.value = '';
    refreshOptions();
  });

  refreshOptions();
});
</script>
@endunless
@endsection
