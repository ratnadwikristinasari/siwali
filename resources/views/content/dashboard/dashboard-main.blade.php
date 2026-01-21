    @extends('layouts/contentNavbarLayout')

    @section('title', 'Dashboard - Analytics')

    @section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
    @endsection

    @section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
    @endsection

    @section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
    @endsection

    <script>
    window.ipkTopData = {
        categories: @json($ipkTopData['categories'] ?? []),
        series: @json($ipkTopData['series'] ?? [])
    };
</script>

{{-- setelah ini baru load dashboard.js --}}
<script src="{{ asset('assets/js/dashboard.js') }}"></script>


@role('student')
@include('content.dashboard.partials.dashboard-mhs')
@endrole 

@role('kajur')
@include('content.dashboard.partials.dashboard-kjr')
@endrole 

@role('kaprodi')
@include('content.dashboard.partials.dashboard-kps')
@endrole 

@role('lecturer')
@include('content.dashboard.partials.dashboard-dsn')
@endrole

