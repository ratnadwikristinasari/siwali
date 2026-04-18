    @extends('layouts/contentNavbarLayout')

    @section('title', 'Dashboard')

    @section('content')
        <div class="row gy-6">
            @include('content.dashboard.partials.dashboard-dsn', ['isFromMyDashboard' => true])
        </div>
    @endsection
