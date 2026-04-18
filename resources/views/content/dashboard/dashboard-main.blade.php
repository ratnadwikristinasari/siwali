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

    {{-- setelah ini baru load dashboard.js --}}

    @section('content')
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
            @notrole('kaprodi', 'kajur', 'student')
                @include('content.dashboard.partials.dashboard-dsn')
            @endnotrole
        @endrole
        @role('orang_tua')
            @include('content.dashboard.partials.dashboard-ortu')
        @endrole
    @endsection
