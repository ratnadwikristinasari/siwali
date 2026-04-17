@extends('layouts/contentNavbarLayout')

@section('title', 'Data Program Studi')

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#search').on('keyup', function() {
                let debounceTimer;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    $('#form-filter').submit();
                }, 1000);
            });
        });
    </script>
@endsection

@section('vendor-pagination')
    @vite('resources/views/vendor/pagination/bootstrap-page.blade.php')
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Data Program Studi</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="row g-2 align-items-center my-3 mx-1">
                    <div class="col-12 col-md">
                        <form action="{{ route('dataprodi') }}" method="GET" id="form-filter">
                            <div class="row g-2 justify-content-md-end">
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="input-group input-group-sm">
                                        <input type="search" class="form-control" placeholder="Cari..." id="search"
                                            name="search" value="{{ request('search') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-truncate">No</th>
                                <th class="text-truncate">Program Studi</th>
                                <th class="text-truncate">Koordinator</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prodis as $index => $listprodi)
                                <tr>
                                    <td>{{ $prodis->firstItem() + $index }}</td>
                                    <td>
                                        {{ $listprodi['name'] }}
                                    </td>
                                    <td>
                                        {{ isset($listprodi['head']['name']) ? $listprodi['head']['name'] : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $prodis->links('pagination::bootstrap-page') }}
                </div>
            </div>
        </div>
    </div>
@endsection
