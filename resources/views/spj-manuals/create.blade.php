@extends('layouts.app', ['title' => 'Tambah SPJ Manual'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  .search-select { position: relative; }
  .search-select .search-input { width: 100%; padding: .75rem .9rem; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: var(--text); }
  .search-select .search-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 10; background: #fff; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,.08); max-height: 220px; overflow-y: auto; display: none; }
  .search-select.open .search-dropdown { display: block; }
  .search-select .search-item { padding: .5rem .75rem; cursor: pointer; font-size: .9rem; }
  .search-select .search-item:hover { background: #f8fafc; }
  .search-select .search-item.selected { background: #eff6ff; color: #2563eb; font-weight: 600; }
  .search-select .search-empty { padding: .6rem .75rem; color: var(--muted); font-size: .85rem; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    <form method="POST" action="{{ route('spj-manuals.store') }}">
      @csrf
      <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
          <h1>Tambah SPJ Manual</h1>
          <div class="subtitle">Buat SPJ manual baru.</div>
        </div>
        <div><a class="btn" href="{{ route('spj-manuals.index') }}">Kembali</a></div>
      </div>
      <div class="form-grid">
        <div class="col-6">
          <div class="field">
            <label for="driver_name">Nama Pengemudi</label>
            <div class="search-select" id="driver_select">
              <input id="driver_search" class="search-input" type="text" placeholder="Ketik nama pengemudi..." autocomplete="off" value="{{ old('driver_name') }}">
              <input type="hidden" name="driver_name" id="driver_name" value="{{ old('driver_name') }}">
              <div class="search-dropdown" id="driver_dropdown"></div>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="customer_name">Nama Pelanggan</label>
            <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="customer_contact">Kontak Pelanggan</label>
            <input id="customer_contact" type="text" name="customer_contact" value="{{ old('customer_contact') }}">
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="country_of_origin">Negara Asal</label>
            <div class="search-select" id="country_select">
              <input id="country_search" class="search-input" type="text" placeholder="Ketik nama negara..." autocomplete="off" value="{{ old('country_of_origin') }}">
              <input type="hidden" name="country_of_origin" id="country_of_origin_hidden" value="{{ old('country_of_origin') }}">
              <div class="search-dropdown" id="country_dropdown"></div>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="passenger_count">Jumlah Penumpang</label>
            <input id="passenger_count" type="number" name="passenger_count" value="{{ old('passenger_count', 1) }}" min="1" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="start_date">Tanggal Mulai</label>
            <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="pickup_time">Waktu Jemput</label>
            <input id="pickup_time" type="time" name="pickup_time" value="{{ old('pickup_time') }}" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="service_type">Layanan</label>
            <div class="search-select" id="service_select">
              <input id="service_search" class="search-input" type="text" placeholder="Ketik nama layanan..." autocomplete="off" value="{{ old('service_type') }}">
              <input type="hidden" name="service_type" id="service_type_hidden" value="{{ old('service_type') }}">
              <div class="search-dropdown" id="service_dropdown"></div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label for="pickup_address">Alamat Jemput</label>
            <textarea id="pickup_address" name="pickup_address" style="width:100%; border:1px solid #cbd5e1; border-radius:12px; padding:.75rem .9rem;" required>{{ old('pickup_address') }}</textarea>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="service_duration">Durasi Layanan</label>
            <input id="service_duration" type="text" name="service_duration" value="{{ old('service_duration') }}" placeholder="Contoh: 12 jam">
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="payment_plan">Rencana Pembayaran</label>
            <select id="payment_plan" name="payment_plan">
              <option value="">Pilih rencana pembayaran</option>
              <option value="down_payment" {{ old('payment_plan') == 'down_payment' ? 'selected' : '' }}>Down Payment</option>
              <option value="payment_full_transfer" {{ old('payment_plan') == 'payment_full_transfer' ? 'selected' : '' }}>Payment Full Transfer</option>
              <option value="payment_full_on_driver" {{ old('payment_plan') == 'payment_full_on_driver' ? 'selected' : '' }}>Payment Full On Driver</option>
            </select>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label for="trip_details">Rincian Perjalanan</label>
            <textarea id="trip_details" name="trip_details" style="width:100%; border:1px solid #cbd5e1; border-radius:12px; padding:.75rem .9rem; min-height:120px;">{{ old('trip_details') }}</textarea>
          </div>
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
  const MITRAS = [
    @foreach($mitras as $m)
      { id: {{ $m->id }}, name: "{{ addslashes($m->full_name) }}" }{{ $loop->last ? '' : ',' }}
    @endforeach
  ];
  const wrap = document.getElementById('driver_select');
  const searchInput = document.getElementById('driver_search');
  const hiddenInput = document.getElementById('driver_name');
  const dropdown = document.getElementById('driver_dropdown');

  function render(q) {
    const s = (q || '').toLowerCase().trim();
    const items = MITRAS.filter(m => m.name.toLowerCase().includes(s));
    dropdown.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'search-empty';
      empty.textContent = 'Tidak ada hasil';
      dropdown.appendChild(empty);
      return;
    }
    items.forEach(function(item) {
      const el = document.createElement('div');
      el.className = 'search-item' + (hiddenInput.value === item.name ? ' selected' : '');
      el.textContent = item.name;
      el.addEventListener('click', function() {
        hiddenInput.value = item.name;
        searchInput.value = item.name;
        wrap.classList.remove('open');
      });
      dropdown.appendChild(el);
    });
  }

  searchInput.addEventListener('focus', function() {
    wrap.classList.add('open');
    render(searchInput.value);
  });
  searchInput.addEventListener('input', function() {
    hiddenInput.value = '';
    wrap.classList.add('open');
    render(searchInput.value);
  });
  document.addEventListener('click', function(e) {
    if (!wrap.contains(e.target)) wrap.classList.remove('open');
  });
</script>
<script>
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
      empty.textContent = 'Tidak ada hasil';
      countryDropdown.appendChild(empty);
      return;
    }
    items.forEach(function(c) {
      const el = document.createElement('div');
      el.className = 'search-item' + (countryHidden.value === c ? ' selected' : '');
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
    countryHidden.value = '';
    countrySelect.classList.add('open');
    renderCountryList(filterCountries(countrySearch.value));
  });
  document.addEventListener('click', function(e) {
    if (!countrySelect.contains(e.target)) countrySelect.classList.remove('open');
  });
</script>
<script>
  const SERVICES = [
    @foreach($services as $s)
      { id: {{ $s->id }}, name: "{{ addslashes($s->name) }}" }{{ $loop->last ? '' : ',' }}
    @endforeach
  ];
  const serviceSelect = document.getElementById('service_select');
  const serviceSearch = document.getElementById('service_search');
  const serviceHidden = document.getElementById('service_type_hidden');
  const serviceDropdown = document.getElementById('service_dropdown');
  function renderServiceList(items) {
    serviceDropdown.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'search-empty';
      empty.textContent = 'Tidak ada hasil';
      serviceDropdown.appendChild(empty);
      return;
    }
    items.forEach(function(item) {
      const el = document.createElement('div');
      el.className = 'search-item' + (serviceHidden.value === item.name ? ' selected' : '');
      el.textContent = item.name;
      el.addEventListener('click', function() {
        serviceSearch.value = item.name;
        serviceHidden.value = item.name;
        serviceSelect.classList.remove('open');
      });
      serviceDropdown.appendChild(el);
    });
  }
  function filterServices(q) {
    const s = (q || '').toLowerCase().trim();
    if (!s) return SERVICES.slice();
    return SERVICES.filter(m => m.name.toLowerCase().includes(s));
  }
  serviceSearch.addEventListener('focus', function() {
    serviceSelect.classList.add('open');
    renderServiceList(filterServices(serviceSearch.value));
  });
  serviceSearch.addEventListener('input', function() {
    serviceHidden.value = '';
    serviceSelect.classList.add('open');
    renderServiceList(filterServices(serviceSearch.value));
  });
  document.addEventListener('click', function(e) {
    if (!serviceSelect.contains(e.target)) serviceSelect.classList.remove('open');
  });
</script>
<script>
  document.querySelector('form').addEventListener('submit', function() {
    var driverSearch = document.getElementById('driver_search');
    var driverHidden = document.getElementById('driver_name');
    if (!driverHidden.value && driverSearch.value) driverHidden.value = driverSearch.value;

    var countrySearch = document.getElementById('country_search');
    var countryHidden = document.getElementById('country_of_origin_hidden');
    if (!countryHidden.value && countrySearch.value) countryHidden.value = countrySearch.value;

    var serviceSearch = document.getElementById('service_search');
    var serviceHidden = document.getElementById('service_type_hidden');
    if (!serviceHidden.value && serviceSearch.value) serviceHidden.value = serviceSearch.value;
  });
</script>
@endsection
