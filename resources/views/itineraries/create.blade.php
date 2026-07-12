@extends('layouts.app', ['title' => 'Add Itinerary'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  .search-select { position: relative; }
  .search-input { width: 100%; padding: .75rem .9rem; border-radius: 12px; border: 1px solid #cbd5e1; height: 44px; box-sizing: border-box; }
  .search-dropdown { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,.08); max-height: 220px; overflow-y: auto; z-index: 30; display: none; }
  .search-select.open .search-dropdown { display: block; }
  .search-item { padding: .6rem .9rem; cursor: pointer; font-size: .9rem; }
  .search-item:hover { background: #f1f5f9; }
  .search-empty { padding: .6rem .9rem; color: var(--muted); font-size: .9rem; }
  .day-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; background: #f8fafc; }
  .day-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: .75rem; }
  .day-header h3 { margin: 0; font-size: 1rem; }
  .activity-row { display: grid; grid-template-columns: 32px 100px 100px 1fr 36px; gap: .5rem; align-items: center; margin-bottom: .5rem; }
  .activity-row input[type="time"] { padding: .6rem .9rem; border: 1px solid #cbd5e1; border-radius: 10px; height: 40px; }
  .activity-row input[type="text"] { padding: .6rem .9rem; border: 1px solid #cbd5e1; border-radius: 10px; height: 40px; width: 100%; box-sizing: border-box; }
  .btn-move { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 6px; width: 28px; height: 28px; cursor: pointer; font-size: .75rem; display: flex; align-items: center; justify-content: center; padding: 0; }
  .btn-move:hover { background: #e2e8f0; color: #334155; }
  .btn-remove { background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; }
  .btn-remove:hover { background: #fecaca; }
  .btn-add { background: #eff6ff; color: #2563eb; border: 1px dashed #93c5fd; border-radius: 10px; padding: .5rem 1rem; cursor: pointer; font-weight: 600; font-size: .85rem; }
  .btn-add:hover { background: #dbeafe; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
  @media (max-width: 640px) {
    .activity-row { grid-template-columns: 1fr 1fr; gap: .4rem; }
    .activity-row .act-time-from,
    .activity-row .act-time-to { grid-column: span 1; }
    .activity-row .act-activity { grid-column: span 2; }
    .activity-row .act-arrows { grid-column: span 2; display: flex; gap: .25rem; justify-content: flex-start; }
    .activity-row .act-actions { grid-column: span 2; display: flex; justify-content: flex-end; }
  }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    @if($errors->any())
      <div style="margin-bottom:1rem; padding:.75rem 1rem; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; color:#991b1b;">
        <ul style="margin:0; padding-left:1.25rem;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('itineraries.store') }}" id="itineraryForm">
      @csrf
      <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
          <h1>Add Itinerary</h1>
          <div class="subtitle">Create a new travel itinerary.</div>
        </div>
        <div><a class="btn" href="{{ route('itineraries.index') }}">Back</a></div>
      </div>
      <div class="form-grid">
        @if(auth()->user()->role === 'super_admin')
        <div class="col-6">
          <div class="field">
            <label for="user_search">User</label>
            <div class="search-select" id="user_select">
              <input id="user_search" class="search-input" type="text" placeholder="Search user name..." autocomplete="off" value="{{ old('user_search') }}">
              <input type="hidden" name="user_id" id="user_id_hidden" value="{{ old('user_id') }}">
              <div class="search-dropdown" id="user_dropdown"></div>
            </div>
          </div>
        </div>
        @else
          <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        @endif
        <div class="col-6">
          <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
              <option value="" disabled {{ old('status') ? '' : 'selected' }}>Select Status</option>
              @foreach(['draft', 'active', 'done'] as $status)
                <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label for="title">Title</label>
            <input id="title" type="text" name="title" value="{{ old('title') }}" required>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" style="width:100%; border:1px solid #cbd5e1; border-radius:12px; padding:.75rem .9rem;">{{ old('description') }}</textarea>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="start_date">Start Date</label>
            <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="end_date">End Date</label>
            <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}" required>
          </div>
        </div>
      </div>

      <div style="margin-top:1.5rem; margin-bottom:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <h2 style="margin:0;">Day Details</h2>
          <button type="button" class="btn-add" id="addDay">+ Add Day</button>
        </div>
      </div>

      <div id="daysContainer"></div>

      <div class="actions" style="margin-top:1rem;">
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const USERS = [
  @foreach($users as $u)
    { id: {{ $u->id }}, name: "{{ addslashes($u->name) }}", email: "{{ addslashes($u->email) }}" },
  @endforeach
];

let dayIndex = 0;

function addUserSearchListeners() {
  const selectWrap = document.getElementById('user_select');
  const searchInput = document.getElementById('user_search');
  const hiddenInput = document.getElementById('user_id_hidden');
  const dropdown = document.getElementById('user_dropdown');

  function renderUsers(items) {
    dropdown.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'search-empty';
      empty.textContent = 'No results';
      dropdown.appendChild(empty);
      return;
    }
    items.forEach(function(item) {
      const el = document.createElement('div');
      el.className = 'search-item';
      el.textContent = item.name + ' (' + item.email + ')';
      el.addEventListener('click', function() {
        searchInput.value = item.name;
        hiddenInput.value = item.id;
        selectWrap.classList.remove('open');
      });
      dropdown.appendChild(el);
    });
  }

  function filterUsers(q) {
    const s = (q || '').toLowerCase().trim();
    return USERS.filter(function(u) {
      return u.name.toLowerCase().includes(s) || u.email.toLowerCase().includes(s);
    });
  }

  searchInput.addEventListener('focus', function() {
    selectWrap.classList.add('open');
    renderUsers(filterUsers(searchInput.value));
  });
  searchInput.addEventListener('input', function() {
    selectWrap.classList.add('open');
    hiddenInput.value = '';
    renderUsers(filterUsers(searchInput.value));
  });
  document.addEventListener('click', function(e) {
    if (!selectWrap.contains(e.target)) selectWrap.classList.remove('open');
  });
}

function getStartDate() {
  return document.getElementById('start_date').value;
}

function calcDate(startDate, dayNum) {
  if (!startDate) return '';
  var d = new Date(startDate + 'T00:00:00');
  d.setDate(d.getDate() + dayNum - 1);
  var y = d.getFullYear();
  var m = String(d.getMonth() + 1).padStart(2, '0');
  var dd = String(d.getDate()).padStart(2, '0');
  return y + '-' + m + '-' + dd;
}

function addDay() {
  var container = document.getElementById('daysContainer');
  var startDate = getStartDate();
  var existingDays = container.querySelectorAll('.day-card');
  var currentDayNum = existingDays.length + 1;
  var dateStr = calcDate(startDate, currentDayNum);

  var html = '<div class="day-card" data-day-index="' + dayIndex + '">'
    + '<div class="day-header">'
    + '<h3>Day ' + currentDayNum + ' <input type="hidden" name="days[' + dayIndex + '][day_number]" value="' + currentDayNum + '"><input type="hidden" name="days[' + dayIndex + '][date]" value="' + dateStr + '"></h3>'
    + '<button type="button" class="btn-remove" onclick="removeDay(this)" title="Remove Day">&times;</button>'
    + '</div>'
    + '<div class="activities-container" data-day-idx="' + dayIndex + '"></div>'
    + '<button type="button" class="btn-add" onclick="addActivity(this, ' + dayIndex + ')">+ Add Activity</button>'
    + '</div>';
  container.insertAdjacentHTML('beforeend', html);
  dayIndex++;
}

function removeDay(btn) {
  btn.closest('.day-card').remove();
  renumberDays();
}

function renumberDays() {
  var cards = document.querySelectorAll('.day-card');
  var startDate = getStartDate();
  cards.forEach(function(card, i) {
    card.querySelector('h3').innerHTML = 'Day ' + (i + 1) + ' <input type="hidden" name="days[' + i + '][day_number]" value="' + (i + 1) + '"><input type="hidden" name="days[' + i + '][date]" value="' + calcDate(startDate, i + 1) + '">';
    card.dataset.dayIndex = i;
    var actContainer = card.querySelector('.activities-container');
    actContainer.dataset.dayIdx = i;
    renumberActivities(actContainer);
  });
  dayIndex = cards.length;
}

function addActivity(btn, di) {
  const container = btn.previousElementSibling;
  const actIdx = container.querySelectorAll('.activity-row').length;
  const html = `
    <div class="activity-row">
      <div class="act-arrows" style="display:flex; flex-direction:column; gap:2px;">
        <button type="button" class="btn-move" onclick="moveActivity(this, -1)" title="Move Up">&#9650;</button>
        <button type="button" class="btn-move" onclick="moveActivity(this, 1)" title="Move Down">&#9660;</button>
      </div>
      <input type="time" class="act-time-from" name="days[${di}][activities][${actIdx}][time_from]" required>
      <input type="time" class="act-time-to" name="days[${di}][activities][${actIdx}][time_to]" required>
      <input type="text" class="act-activity" name="days[${di}][activities][${actIdx}][activity]" placeholder="Activity..." required>
      <div class="act-actions"><button type="button" class="btn-remove" onclick="removeActivity(this)" title="Remove">&times;</button></div>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', html);
}

function removeActivity(btn) {
  btn.closest('.activity-row').remove();
}

function moveActivity(btn, direction) {
  var row = btn.closest('.activity-row');
  var container = row.parentElement;
  var rows = Array.from(container.querySelectorAll('.activity-row'));
  var idx = rows.indexOf(row);
  var targetIdx = idx + direction;
  if (targetIdx < 0 || targetIdx >= rows.length) return;
  if (direction === -1) {
    container.insertBefore(row, rows[targetIdx]);
  } else {
    container.insertBefore(row, rows[targetIdx].nextSibling);
  }
  renumberActivities(container);
}

function renumberActivities(container) {
  var dayCard = container.closest('.day-card');
  var di = dayCard.dataset.dayIndex;
  container.querySelectorAll('.activity-row').forEach(function(row, j) {
    row.querySelector('.act-time-from').name = 'days[' + di + '][activities][' + j + '][time_from]';
    row.querySelector('.act-time-to').name = 'days[' + di + '][activities][' + j + '][time_to]';
    row.querySelector('.act-activity').name = 'days[' + di + '][activities][' + j + '][activity]';
  });
}

document.getElementById('addDay').addEventListener('click', addDay);

document.getElementById('start_date').addEventListener('change', function() {
  renumberDays();
});

if (document.getElementById('user_select')) {
  addUserSearchListeners();
}
addDay();
</script>
@endsection
