@extends('layouts.app', ['title' => 'Edit Booking'])

@section('content')
<div class="card" style="max-width: 760px; margin: 30px auto 0;">
  <div class="card-body">
    <form method="POST" action="{{ route('bookings.update', $booking) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
          <h1>Edit Booking</h1>
          <div class="subtitle">Perbarui detail perjalanan dan preferensi Anda.</div>
        </div>
        <div><a class="btn" href="{{ route('bookings.index') }}">Kembali</a></div>
      </div>
      <div class="form-grid">
        <div class="col-6">
          <div class="field">
            <label for="customer_name">Customer Name <span style="color:red;">*</span></label>
            <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name', $booking->customer_name) }}" placeholder="Full name" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="contact_number">Contact Number <span style="color:red;">*</span></label>
            <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', $booking->contact_number) }}" placeholder="Phone number" required>
          </div>
        </div>
        <div class="col-4">
          <div class="field">
            <label for="number_of_passengers">Number of Passengers <span style="color:red;">*</span></label>
            <input id="number_of_passengers" type="number" name="number_of_passengers" value="{{ old('number_of_passengers', $booking->number_of_passengers) }}" placeholder="Number of passengers" required>
          </div>
        </div>
        <div class="col-4">
          <div class="field">
            <label for="country_of_origin">Country of Origin <span style="color:red;">*</span></label>
            <div class="search-select" id="country_select">
              <input id="country_search" class="search-input" type="text" placeholder="Type to search country" autocomplete="off" value="{{ old('country_of_origin', $booking->country_of_origin) }}" required>
              <input type="hidden" name="country_of_origin" id="country_of_origin_hidden" value="{{ old('country_of_origin', $booking->country_of_origin) }}">
              <div class="search-dropdown" id="country_dropdown"></div>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="field">
            <label for="pickup_location">Pickup Location <span style="color:red;">*</span></label>
            <input id="pickup_location" type="text" name="pickup_location" value="{{ old('pickup_location', $booking->pickup_location) }}" placeholder="Pickup location" required>
          </div>
        </div>
        <div class="col-3">
          <div class="field">
            <label for="start_date">Start Date <span style="color:red;">*</span></label>
            <input id="start_date" type="date" name="start_date" value="{{ old('start_date', \Illuminate\Support\Carbon::parse($booking->booking_date)->format('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="col-3">
          <div class="field">
            <label for="end_date">End Date <span style="color:red;">*</span></label>
            <input id="end_date" type="date" name="end_date" value="{{ old('end_date', \Illuminate\Support\Carbon::parse($booking->end_date)->format('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="col-3">
          <div class="field">
            <label for="pickup_time">Pickup Time (12H) <span style="color:red;">*</span></label>
            <div style="display:flex; gap:.4rem; align-items:center; flex-wrap:nowrap;">
              <select id="pt_hour" name="pt_hour" required style="width:65px; padding:.5rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem; appearance:auto; flex-shrink:0;">
                <option value="">HH</option>
                @foreach(range(1,12) as $h)
                  <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
              </select>
              <span style="font-weight:700; font-size:1.1rem; color:#64748b; flex-shrink:0;">:</span>
              <select id="pt_min" name="pt_min" required style="width:65px; padding:.5rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem; appearance:auto; flex-shrink:0;">
                <option value="">MM</option>
                @foreach(range(0,60) as $m)
                  <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                @endforeach
              </select>
              <div style="display:flex; border:1px solid var(--border); border-radius:8px; overflow:hidden; flex-shrink:0;">
                <label for="pt_ampm_am" style="margin:0; cursor:pointer;">
                  <input type="radio" id="pt_ampm_am" name="pt_ampm" value="AM" required style="display:none;">
                  <span class="pt-ampm-btn">AM</span>
                </label>
                <label for="pt_ampm_pm" style="margin:0; cursor:pointer;">
                  <input type="radio" id="pt_ampm_pm" name="pt_ampm" value="PM" style="display:none;">
                  <span class="pt-ampm-btn">PM</span>
                </label>
              </div>
            </div>
            <style>
              .pt-ampm-btn { display:block; padding:.5rem .9rem; font-size:.85rem; font-weight:600; color:#64748b; background:#f8fafc; transition:all .15s; white-space:nowrap; }
              .pt-ampm-btn:hover { background:#e2e8f0; }
              input[name="pt_ampm"]:checked + .pt-ampm-btn { background:#3b82f6; color:#fff; }
            </style>
            <input type="hidden" id="pickup_time" name="pickup_time" value="{{ old('pickup_time', \Illuminate\Support\Carbon::parse($booking->pickup_time)->format('H:i')) }}">
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="vehicle_id">Car of Choice <span style="color:red;">*</span></label>
            <select id="vehicle_id" name="vehicle_id" required>
              <option value="">Select car</option>
              @foreach($vehicles as $v)
                <option value="{{ $v->id }}" data-mitra-id="{{ $v->mitra_id }}" {{ old('vehicle_id', $booking->vehicle_id) == $v->id ? 'selected' : '' }}>
                  {{ $v->make }} {{ $v->model }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="service_id">Services <span style="color:red;">*</span></label>
            <select id="service_id" name="service_id" required>
              <option value="">Select service</option>
              @foreach($services as $service)
                <option value="{{ $service->id }}" {{ old('service_id', $booking->service_id) == $service->id ? 'selected' : '' }}>
                  {{ $service->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="itinerary_id">Itinerary</label>
            <select id="itinerary_id" name="itinerary_id">
              <option value="">No itinerary (optional)</option>
              @foreach($itineraries as $it)
                <option value="{{ $it->id }}" {{ old('itinerary_id', $booking->itinerary_id) == $it->id ? 'selected' : '' }}>
                  {{ $it->title }} — {{ $it->user->name ?? '' }} ({{ $it->start_date->format('d M Y') }} - {{ $it->end_date->format('d M Y') }})
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="mitra_id">Mitra (Owner)</label>
            <select id="mitra_id" name="mitra_id">
              <option value="">Select mitra</option>
              @foreach($mitras as $m)
                <option value="{{ $m->id }}" {{ old('mitra_id', $booking->mitra_id) == $m->id ? 'selected' : '' }}>
                  {{ $m->full_name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label for="travel_plans_editor">Travel Plans</label>
            <style>
              .wysiwyg { border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#fff; }
              .w-toolbar { display:flex; gap:.25rem; flex-wrap:wrap; padding:.4rem; background:#f8fafc; border-bottom:1px solid var(--border); }
              .w-btn { display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .6rem; border:1px solid #e5e7eb; border-radius:8px; background:#ffffff; color:#1f2937; font-size:.9rem; }
              .w-btn:hover { background:#f1f5f9; }
              .w-content { min-height:160px; padding:.8rem .9rem; line-height:1.6; font-size:.95rem; outline:none; }
              .w-content:empty:before { content: attr(data-placeholder); color:#9ca3af; }
              .w-sep { width:1px; height:28px; background:#e5e7eb; margin:0 .25rem; }
              .w-content ul { padding-left: 1.25rem; list-style: disc; margin:.25rem 0; }
              .w-content ol { padding-left: 1.25rem; list-style: decimal; margin:.25rem 0; }
              .w-content li { margin:.15rem 0; }
            </style>
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
              <div id="travel_plans_editor" class="w-content" contenteditable="true" data-placeholder="Describe your trip plan, destinations, and duration.">{!! old('travel_plans', $booking->travel_plans) !!}</div>
            </div>
            <input type="hidden" name="travel_plans" id="travel_plans_input" value="{{ old('travel_plans', $booking->travel_plans) }}">
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="info_source">Where do you get the information from? <span style="color:red;">*</span></label>
            <select name="info_source" id="info_source" required>
              <option value="">Select source</option>
              <option value="instagram" {{ old('info_source', $booking->info_source) == 'instagram' ? 'selected' : '' }}>Instagram</option>
              <option value="ai" {{ old('info_source', $booking->info_source) == 'ai' ? 'selected' : '' }}>AI</option>
              <option value="website" {{ old('info_source', $booking->info_source) == 'website' ? 'selected' : '' }}>Website</option>
              <option value="other" {{ old('info_source', $booking->info_source) == 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
        </div>
        <div class="col-6" id="info_source_other_wrap" style="display: {{ old('info_source', $booking->info_source) == 'other' ? 'block' : 'none' }};">
          <div class="field">
            <label for="info_source_other">Specify Source <span style="color:red;">*</span></label>
            <input id="info_source_other" type="text" name="info_source_other" value="{{ old('info_source_other', $booking->info_source_other) }}" placeholder="e.g., friend recommendation">
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="group_id">Company <span style="color:red;">*</span></label>
            <select id="group_id" name="group_id" required>
              <option value="">Select company</option>
              @foreach($groups as $g)
                <option value="{{ $g->id }}" {{ old('group_id', $booking->group_id) == $g->id ? 'selected' : '' }}>
                  {{ $g->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="payment_plan">Payment Plan <span style="color:red;">*</span></label>
            <select id="payment_plan" name="payment_plan" required>
              <option value="down_payment" {{ old('payment_plan', $booking->payment_plan) == 'down_payment' ? 'selected' : '' }}>Down Payment</option>
              <option value="payment_full_transfer" {{ old('payment_plan', $booking->payment_plan) == 'payment_full_transfer' ? 'selected' : '' }}>Payment Full Transfer</option>
              <option value="payment_full_on_driver" {{ old('payment_plan', $booking->payment_plan) == 'payment_full_on_driver' ? 'selected' : '' }}>Payment Full On Driver</option>
            </select>
          </div>
        </div>
        <div class="col-6" id="down_payment_amount_wrap" style="display: {{ old('payment_plan', $booking->payment_plan) === 'down_payment' ? 'block' : 'none' }};">
          <div class="field">
            <label for="down_payment_amount">Down Payment Amount (IDR) <span style="color:red;">*</span></label>
            <input id="down_payment_amount" type="text" inputmode="numeric" name="down_payment_amount" value="{{ old('down_payment_amount', $booking->down_payment_amount !== null ? number_format($booking->down_payment_amount, 0, '.', '') : '') }}" placeholder="Masukkan nominal down payment">
          </div>
        </div>
        @if(auth()->user()?->role === 'super_admin')
        <div class="col-6">
          <div class="field">
            <label for="price">Harga (IDR)</label>
            <input id="price" type="text" inputmode="numeric" name="price" value="{{ old('price', number_format($booking->price ?? 0, 0, '.', '')) }}" placeholder="Masukkan harga total">
          </div>
        </div>
        @endif
        <div class="col-6">
          <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
              <option value="">(default)</option>
              <option value="baru_masuk" {{ old('status', $booking->status) == 'baru_masuk' ? 'selected' : '' }}>Baru Masuk</option>
              <option value="konfirmasi" {{ old('status', $booking->status) == 'konfirmasi' ? 'selected' : '' }}>Konfirmasi</option>
              <option value="dijadwalkan" {{ old('status', $booking->status) == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
              <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancel</option>
              <option value="selesai_pelayanan" {{ old('status', $booking->status) == 'selesai_pelayanan' ? 'selected' : '' }}>Selesai Pelayanan</option>
              <option value="selesai_administrasi_fee" {{ old('status', $booking->status) == 'selesai_administrasi_fee' ? 'selected' : '' }}>Selesai Administrasi Fee</option>
            </select>
          </div>
        </div>
        @if(auth()->user()?->role === 'super_admin')
        <div class="col-6">
          <div class="field">
            <label for="manual_invoice_file">Upload Invoice Manual (PDF)</label>
            <input id="manual_invoice_file" type="file" name="manual_invoice_file" accept="application/pdf">
            @if($booking->invoice?->manual_invoice_path)
              <div class="subtitle" style="margin-top:.35rem;">
                File saat ini:
                <a href="{{ asset('storage/' . $booking->invoice->manual_invoice_path) }}" target="_blank" rel="noopener">
                  Lihat Invoice Manual
                </a>
              </div>
            @endif
          </div>
        </div>
        @endif
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
document.getElementById('info_source').addEventListener('change', function() {
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
countrySearch.addEventListener('focus', function() {
  countrySelect.classList.add('open');
  renderCountryList(filterCountries(countrySearch.value));
});
countrySearch.addEventListener('input', function() {
  countrySelect.classList.add('open');
  renderCountryList(filterCountries(countrySearch.value));
});
document.addEventListener('click', function(e) {
  if (!countrySelect.contains(e.target)) {
    countrySelect.classList.remove('open');
  }
});
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
    ptMin.addEventListener('change', sync);
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
    const url = prompt('Masukkan URL');
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
  (function(){
    var el = document.getElementById('price');
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
@endsection
