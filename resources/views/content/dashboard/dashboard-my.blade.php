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
        <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    @endsection

    {{-- setelah ini baru load dashboard.js --}}
    @section('content')
        <div class="row gy-6">
            @include('content.dashboard.partials.dashboard-dsn')
        </div>
    @endsection
