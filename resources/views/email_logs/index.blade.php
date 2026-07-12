@extends('layouts.app', ['title' => 'Log Email'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .container { max-width: 100% !important; width: 100%; margin: 0 auto; padding: .75rem 1rem 1rem; box-sizing: border-box; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; min-width: 0; max-width: 100%; }
  .content-card { max-width: 100%; margin: 0; min-width: 0; overflow: hidden; }
  .content-card .card-body { overflow-wrap: break-word; word-wrap: break-word; min-width: 0; max-width: 100%; box-sizing: border-box; }
  .elog-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 1rem; min-width: 0; max-width: 100%; }
  .elog-col-4 { grid-column: span 4; }
  .elog-col-3 { grid-column: span 3; }
  .email-log-error {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: copy;
    max-width: 100%;
  }
  .email-log-error:hover { text-decoration: underline; }
  .email-log-error-tooltip {
    position: fixed;
    z-index: 1200;
    max-width: min(420px, calc(100vw - 2rem));
    background: #111827;
    color: #ffffff;
    border-radius: 12px;
    padding: .75rem .9rem;
    box-shadow: 0 16px 30px rgba(15, 23, 42, .35);
    font-size: .88rem;
    line-height: 1.45;
    pointer-events: none;
    white-space: normal;
    display: none;
    visibility: hidden;
  }
  @media (max-width: 1200px) {
    .dashboard-wrap { grid-template-columns: 1fr; overflow: hidden; }
    .dashboard-sidebar { position: static; max-height: none; overflow: hidden; min-width: 0; }
  }
  @media (max-width: 768px) {
    .elog-col-4, .elog-col-3 { grid-column: span 12; }
    .elog-stats { grid-template-columns: 1fr !important; }
    .card { box-shadow: none; }
    .content-card { overflow: hidden; }
    .desktop-table { display: none !important; }
    .mobile-cards { display: block !important; }
  }
  .mobile-cards { display: none; }
  .mobile-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: .75rem; }
  .mobile-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; margin-bottom: .5rem; }
  .mobile-card-time { font-weight: 700; color: #0f172a; font-size: .9rem; }
  .mobile-card-recipient { font-weight: 600; color: #0f172a; font-size: .9rem; word-break: break-word; }
  .mobile-card-meta { font-size: .82rem; color: #6b7280; margin-bottom: .5rem; }
  .mobile-card-error { font-size: .82rem; color: #b91c1c; font-weight: 600; margin-top: .25rem; cursor: copy; }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="card content-card">
    <div class="card-body">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
      <div>
        <h1 style="margin-bottom:.35rem;">Log Email</h1>
        <div class="subtitle">Email delivery history for invoice notifications and test emails.</div>
      </div>
      <div class="actions" style="margin-top:0;">
        <a class="btn" href="{{ route('dashboard') }}">Back</a>
      </div>
    </div>

    <div class="elog-grid elog-stats" style="margin-top:1rem;">
      <div class="elog-col-4">
        <div class="card" style="border-color:#dbeafe; background:linear-gradient(180deg,#eff6ff 0%, #ffffff 100%);">
          <div class="card-body" style="padding:1rem 1.1rem;">
            <div class="subtitle">Total Log</div>
            <div style="font-size:1.9rem; font-weight:800; color:#1d4ed8; line-height:1.1;">{{ number_format($stats['total'] ?? 0) }}</div>
          </div>
        </div>
      </div>
      <div class="elog-col-4">
        <div class="card" style="border-color:#dcfce7; background:linear-gradient(180deg,#f0fdf4 0%, #ffffff 100%);">
          <div class="card-body" style="padding:1rem 1.1rem;">
            <div class="subtitle">Sent</div>
            <div style="font-size:1.9rem; font-weight:800; color:#16a34a; line-height:1.1;">{{ number_format($stats['sent'] ?? 0) }}</div>
          </div>
        </div>
      </div>
      <div class="elog-col-4">
        <div class="card" style="border-color:#fee2e2; background:linear-gradient(180deg,#fef2f2 0%, #ffffff 100%);">
          <div class="card-body" style="padding:1rem 1.1rem;">
            <div class="subtitle">Failed</div>
            <div style="font-size:1.9rem; font-weight:800; color:#dc2626; line-height:1.1;">{{ number_format($stats['failed'] ?? 0) }}</div>
          </div>
        </div>
      </div>
    </div>

    <form method="GET" action="{{ route('email-logs.index') }}" class="card" style="margin-top:1rem; border-color:#dbeafe; background:#f8fbff;">
      <div class="card-body">
        <div class="elog-grid">
          <div class="elog-col-4">
            <div class="field">
              <label>Search</label>
              <input type="text" name="q" value="{{ $q }}" placeholder="Search email, subject, or error">
            </div>
          </div>
          <div class="elog-col-3">
            <div class="field">
              <label>Status</label>
              <select name="status">
                <option value="">All</option>
                <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
              </select>
            </div>
          </div>
          <div class="elog-col-3">
            <div class="field">
              <label>From Date</label>
              <input type="date" name="date_from" value="{{ $dateFrom }}">
            </div>
          </div>
          <div class="elog-col-3">
            <div class="field">
              <label>To Date</label>
              <input type="date" name="date_to" value="{{ $dateTo }}">
            </div>
          </div>
          <div class="elog-col-3">
            <div class="field">
              <label style="white-space:nowrap;">Per Page</label>
              <select name="per_page">
                <option value="10" {{ (int) $perPage === 10 ? 'selected' : '' }}>10</option>
                <option value="50" {{ (int) $perPage === 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ (int) $perPage === 100 ? 'selected' : '' }}>100</option>
              </select>
            </div>
          </div>
        </div>

        <div class="actions" style="margin-top:1rem; justify-content:space-between; flex-wrap:wrap;">
          <div class="subtitle">
            Showing {{ $emailLogs->firstItem() ?? 0 }} - {{ $emailLogs->lastItem() ?? 0 }} of {{ number_format($emailLogs->total()) }} results
          </div>
          <div class="actions" style="margin-top:0;">
            <a class="btn" href="{{ route('email-logs.index') }}">Reset</a>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
          </div>
        </div>
      </div>
    </form>

    {{-- Desktop table --}}
    <div class="card desktop-table" style="margin-top:1rem;">
      <div class="table-wrap" style="margin-top:0;">
        <table class="table" style="width:100%; border-collapse:separate; border-spacing:0 12px;">
          <thead>
            <tr>
              <th style="padding:.95rem 1rem; text-align:left; font-weight:600; color:var(--text); font-size:.85rem;">Time</th>
              <th style="padding:.95rem 1rem; text-align:left; font-weight:600; color:var(--text); font-size:.85rem;">Recipient</th>
              <th style="padding:.95rem 1rem; text-align:left; font-weight:600; color:var(--text); font-size:.85rem;">Subject</th>
              <th style="padding:.95rem 1rem; text-align:left; font-weight:600; color:var(--text); font-size:.85rem;">Status</th>
              <th style="padding:.95rem 1rem; text-align:left; font-weight:600; color:var(--text); font-size:.85rem;">Error</th>
            </tr>
          </thead>
          <tbody>
            @forelse($emailLogs as $log)
            <tr>
              <td style="padding:.95rem 1rem; vertical-align:top; line-height:1.5; word-break:break-word;">
                <div style="font-weight:700;">{{ optional($log->created_at)->format('d M Y') }}</div>
                <div class="subtitle">{{ optional($log->created_at)->format('H:i:s') }}</div>
              </td>
              <td style="padding:.95rem 1rem; vertical-align:top; line-height:1.5; word-break:break-word;">
                <div style="font-weight:700;">{{ $log->to_email }}</div>
                @if($log->booking_id || $log->invoice_id)
                  <div class="subtitle">
                    @if($log->booking_id) Booking #{{ $log->booking_id }}@endif
                    @if($log->booking_id && $log->invoice_id) · @endif
                    @if($log->invoice_id) Invoice #{{ $log->invoice_id }}@endif
                  </div>
                @endif
              </td>
              <td style="padding:.95rem 1rem; vertical-align:top; line-height:1.5; word-break:break-word;">
                <div style="font-weight:700;">{{ $log->subject }}</div>
              </td>
              <td style="padding:.95rem 1rem; vertical-align:top; line-height:1.5; word-break:break-word;">
                @php
                  $statusClass = $log->status === 'sent'
                    ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;'
                    : 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;';
                @endphp
                <span style="display:inline-flex; align-items:center; padding:.35rem .75rem; border-radius:999px; font-weight:800; font-size:.85rem; {{ $statusClass }}">
                  {{ ucfirst($log->status) }}
                </span>
              </td>
              <td style="padding:.95rem 1rem; vertical-align:top; line-height:1.5; word-break:break-word;">
                @if($log->error_message)
                  <div
                    class="email-log-error"
                    style="color:#b91c1c; font-weight:600;"
                    title="Click to copy full message"
                    data-copy-text="{{ e($log->error_message) }}"
                  >{{ $log->error_message }}</div>
                @else
                  <div class="subtitle">-</div>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" style="padding:2rem;">
                <div style="text-align:center;">
                  <div style="font-size:1.05rem; font-weight:700; margin-bottom:.25rem;">No email logs yet</div>
                  <div class="subtitle">Try changing the filter or send a test email first.</div>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:1rem;">
      @forelse($emailLogs as $log)
      <div class="mobile-card">
        <div class="mobile-card-header">
          <div>
            <div class="mobile-card-time">{{ optional($log->created_at)->format('d M Y H:i:s') }}</div>
            <div class="mobile-card-recipient" style="margin-top:.25rem;">{{ $log->to_email }}</div>
            @if($log->booking_id || $log->invoice_id)
              <div class="mobile-card-meta" style="margin-top:.15rem;">
                @if($log->booking_id) Booking #{{ $log->booking_id }}@endif
                @if($log->booking_id && $log->invoice_id) · @endif
                @if($log->invoice_id) Invoice #{{ $log->invoice_id }}@endif
              </div>
            @endif
          </div>
          @php
            $statusClass = $log->status === 'sent'
              ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;'
              : 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;';
          @endphp
          <span style="display:inline-flex; align-items:center; padding:.35rem .75rem; border-radius:999px; font-weight:800; font-size:.82rem; white-space:nowrap; flex-shrink:0; {{ $statusClass }}">
            {{ ucfirst($log->status) }}
          </span>
        </div>
        @if($log->error_message)
          <div
            class="mobile-card-error email-log-error"
            title="Click to copy full message"
            data-copy-text="{{ e($log->error_message) }}"
          >{{ $log->error_message }}</div>
        @endif
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">
        <div style="font-weight:700; margin-bottom:.25rem;">No email logs yet</div>
        Try changing the filter or send a test email first.
      </div>
      @endforelse
      @if($emailLogs->hasPages())
      <div style="margin-top:.75rem;">
        {{ $emailLogs->links() }}
      </div>
      @endif
    </div>

    <div style="margin-top:1rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
      <div class="subtitle">
        Page {{ $emailLogs->currentPage() }} of {{ $emailLogs->lastPage() }}
      </div>
      <div>{{ $emailLogs->links() }}</div>
    </div>
  </div>
</div>
</div>

<div id="emailLogErrorTooltip" class="email-log-error-tooltip"></div>
<script>
  (function () {
    const tooltip = document.getElementById('emailLogErrorTooltip');
    let hideTimer = null;

    function showTooltip(target) {
      const text = target.getAttribute('data-copy-text') || target.textContent || '';
      if (!text.trim()) return;
      tooltip.textContent = text;
      tooltip.style.display = 'block';

      const rect = target.getBoundingClientRect();
      const top = Math.max(12, rect.top - 12);
      const left = Math.min(window.innerWidth - 20, rect.left);
      tooltip.style.top = `${top}px`;
      tooltip.style.left = `${Math.max(12, left)}px`;
    }

    function hideTooltip() {
      tooltip.style.display = 'none';
      tooltip.textContent = '';
    }

    document.addEventListener('mouseover', function (event) {
      const target = event.target.closest('.email-log-error');
      if (!target) return;
      clearTimeout(hideTimer);
      showTooltip(target);
    });

    document.addEventListener('mouseout', function (event) {
      const target = event.target.closest('.email-log-error');
      if (!target) return;
      hideTimer = setTimeout(hideTooltip, 120);
    });

    document.addEventListener('click', async function (event) {
      const target = event.target.closest('.email-log-error');
      if (!target) return;

      const text = target.getAttribute('data-copy-text') || target.textContent || '';
      if (!text.trim()) return;

      try {
        await navigator.clipboard.writeText(text);
        target.title = 'Copied!';
        const originalText = target.textContent;
        target.textContent = 'Copied';
        setTimeout(() => {
          target.textContent = originalText;
          target.title = 'Click to copy full message';
        }, 900);
      } catch (_) {
        window.prompt('Copy this error message:', text);
      }
    });

    document.addEventListener('scroll', hideTooltip, true);
    window.addEventListener('resize', hideTooltip);
  })();
</script>
@endsection
