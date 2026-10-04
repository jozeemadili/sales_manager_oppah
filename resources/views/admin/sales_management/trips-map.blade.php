@extends('layouts.admin.master')
@section('title')
Trips Map
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<style>
    #tripsMap { height: 560px; border-radius: 6px; }
    .truck-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }
</style>
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Trips Map</h3>
    @endslot
    <li class="breadcrumb-item">Logistics</li>
    <li class="breadcrumb-item active">Trips Map</li>
  @endcomponent

  <div class="container-fluid">
      <div class="card">
          <div class="card-body">
              <form method="get" action="{{ route('trips-map') }}" class="row g-2 align-items-end mb-3">
                  <div class="col-md-3">
                      <label class="col-form-label">From</label>
                      <input class="form-control" type="date" name="from" value="{{ $from }}">
                  </div>
                  <div class="col-md-3">
                      <label class="col-form-label">To</label>
                      <input class="form-control" type="date" name="to" value="{{ $to }}">
                  </div>
                  <div class="col-md-3">
                      <label class="col-form-label">Truck</label>
                      <select class="form-select" name="truck_id">
                          <option value="">All trucks</option>
                          @foreach($trucks as $truck)
                              <option value="{{ $truck->id }}" @selected((string) $truckId === (string) $truck->id)>{{ $truck->plate_no }}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-3">
                      <button class="btn btn-primary w-100" type="submit">Show</button>
                  </div>
              </form>

              <div class="mb-2 small" id="tripsLegend"></div>

              @if($points->count())
                  <div id="tripsMap"></div>
              @else
                  <div class="alert alert-light border">No locations recorded for this period.</div>
              @endif

              @if($noLocation->count())
              <h6 class="mt-4">Saved without location ({{ $noLocation->count() }})</h6>
              <div class="table-responsive">
                  <table class="table table-sm">
                      <thead><tr><th>Time</th><th>Truck</th><th>Trip</th><th>Action</th><th>By</th><th>Reason</th></tr></thead>
                      <tbody>
                          @foreach($noLocation as $loc)
                          <tr>
                              <td>{{ $loc->created_at->format('d M Y H:i') }}</td>
                              <td>{{ optional(optional($loc->route)->our_truck)->plate_no }}</td>
                              <td><a href="{{ route('route-preview', $loc->route_id) }}">{{ optional($loc->route)->trip_no }}</a></td>
                              <td>{{ $loc->eventLabel() }}</td>
                              <td>{{ optional($loc->user)->first_name }}</td>
                              <td><span class="badge bg-warning text-dark">{{ $loc->status }}</span></td>
                          </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
              @endif
          </div>
      </div>
  </div>

  @if($points->count())
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
  <script>
  (function () {
      var points = @json($points);
      var colors = ['#1565c0', '#2e7d32', '#c62828', '#6a1b9a', '#ef6c00', '#00838f', '#5d4037', '#ad1457'];
      var map = L.map('tripsMap');
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);

      var byTruck = {};
      points.forEach(function (p) { (byTruck[p.truck] = byTruck[p.truck] || []).push(p); });

      var all = [], legend = [];
      Object.keys(byTruck).forEach(function (truck, i) {
          var color = colors[i % colors.length], line = [];
          byTruck[truck].forEach(function (p, j) {
              var last = j === byTruck[truck].length - 1;
              L.circleMarker([p.lat, p.lng], { radius: last ? 9 : 6, color: color, fillColor: color, fillOpacity: last ? 0.9 : 0.5, weight: 2 })
                  .addTo(map)
                  .bindPopup('<b>' + truck + '</b>' + (last ? ' (latest)' : '') + '<br>Trip: <a href="{{ url('v1/route/preview') }}/' + p.route_id + '">' + (p.trip || p.route_id) + '</a><br>' + p.event + '<br>' + p.time + '<br>' + (p.by || '') + ' &middot; &plusmn;' + p.accuracy + ' m');
              line.push([p.lat, p.lng]);
              all.push([p.lat, p.lng]);
          });
          if (line.length > 1) { L.polyline(line, { color: color, weight: 2, opacity: 0.6 }).addTo(map); }
          legend.push('<span class="truck-dot" style="background:' + color + '"></span>' + truck + ' (' + line.length + ')');
      });

      document.getElementById('tripsLegend').innerHTML = legend.join(' &nbsp; ') + ' &nbsp; <span class="text-muted">&middot; big dot = latest position</span>';
      map.fitBounds(L.latLngBounds(all).pad(0.2), { maxZoom: 14 });
  })();
  </script>
  @endif
@endsection
