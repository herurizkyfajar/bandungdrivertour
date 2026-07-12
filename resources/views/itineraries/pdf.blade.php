<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: sans-serif; font-size: 13px; color: #1f2937; margin: 0; padding: 0; }

    /* COVER PAGE */
    .cover { page-break-after: always; }
    .cover img { width: 100%; display: block; }
    .cover-route {
      font-size: 10px;
      color: rgba(255,255,255,0.55);
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 14px;
    }
    .cover-title {
      font-size: 26px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 6px;
      font-style: italic;
    }
    .cover-date {
      font-size: 15px;
      color: rgba(255,255,255,0.9);
      margin-bottom: 10px;
    }
    .cover-badge {
      display: inline-block;
      padding: 5px 24px;
      background-color: #1a2530;
      color: #ffffff;
      border-radius: 18px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 16px;
    }
    .cover-prepared {
      font-size: 11px;
      color: rgba(255,255,255,0.55);
      margin-bottom: 2px;
    }
    .cover-name {
      font-size: 16px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 2px;
    }
    .cover-email {
      font-size: 12px;
      color: rgba(255,255,255,0.65);
      margin-bottom: 10px;
    }
    .cover-website {
      font-size: 11px;
      color: rgba(255,255,255,0.45);
      border-top: 1px solid rgba(255,255,255,0.1);
      padding-top: 10px;
      margin-top: 6px;
    }

    /* CONTENT PAGES */
    .content { padding: 30px 35px; }
    .content h2 { font-size: 18px; margin: 0 0 6px; color: #1e3a5f; }
    .meta { color: #6b7280; font-size: 12px; margin-bottom: 16px; }
    h3 { font-size: 14px; margin: 18px 0 8px; background: #1e3a5f; color: #fff; padding: 7px 12px; border-radius: 4px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th { text-align: left; background: #e8eef6; padding: 7px 10px; border: 1px solid #cbd5e1; font-size: 12px; color: #1e3a5f; font-weight: 700; }
    td { padding: 7px 10px; border: 1px solid #e5e7eb; font-size: 12px; }
    .time-col { width: 115px; white-space: nowrap; font-weight: 600; color: #2563eb; }
    .desc { font-size: 12px; color: #6b7280; margin-bottom: 14px; line-height: 1.6; }
    .footer { margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
  </style>
</head>
<body>

  <!-- COVER PAGE -->
  <div class="cover">
    <img src="{{ $coverBase64 }}" />
  </div>

  <!-- CONTENT PAGES -->
  <div class="content">
    <h2>{{ $itinerary->title }}</h2>
    <div class="meta">
      {{ $itinerary->start_date->format('d M Y') }} &mdash; {{ $itinerary->end_date->format('d M Y') }}
      &nbsp;|&nbsp; {{ $itinerary->days->count() }} days
      &nbsp;|&nbsp; Prepared for: {{ $itinerary->user->name }}
    </div>

    @if($itinerary->description)
      <div class="desc">{{ $itinerary->description }}</div>
    @endif

    @foreach($itinerary->days as $day)
      <h3>Day {{ $day->day_number }} &mdash; {{ $day->date->format('d M Y, l') }}</h3>
      @if($day->activities->count())
        <table>
          <thead>
            <tr>
              <th class="time-col">Time</th>
              <th>Activity</th>
            </tr>
          </thead>
          <tbody>
            @foreach($day->activities as $act)
              <tr>
                <td class="time-col">{{ substr($act->time_from, 0, 5) }} - {{ substr($act->time_to, 0, 5) }}</td>
                <td>{{ $act->activity }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p style="font-size:12px; color:#94a3b8;">No activities yet.</p>
      @endif
    @endforeach

    <div class="footer">
      BDT Rental &mdash; Bandung Driver Tour &nbsp;|&nbsp; www.bandungdrivertour.com &nbsp;|&nbsp; Itinerary #{{ $itinerary->id }}
    </div>
  </div>

</body>
</html>
