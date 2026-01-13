<!DOCTYPE html>

<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Si Wali </title>
    <meta name="description"
        content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
    <meta name="keywords"
        content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />


    <!-- Include Styles -->
    @include('layouts/sections/styles')

    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')

    <style>
        /* === SWEETALERT FULLSCREEN LOCK === */
        .swal2-container {
            position: fixed !important;
            inset: 0 !important;

            /* HARUS lebih besar dari sidebar & navbar */
            z-index: 2000 !important;

            backdrop-filter: blur(6px);
            background: rgba(0, 0, 0, 0.55);
        }

        /* Popup selalu paling atas */
        .swal2-popup {
            z-index: 2001 !important;
        }

        /* Lock scroll halaman */
        body.swal2-shown {
            overflow: hidden !important;
        }

        body.swal2-shown .layout-menu,
        body.swal2-shown .layout-navbar {
            pointer-events: none;
        }
    </style>


</head>

<body>

    <!-- Layout Content -->
    @yield('layoutContent')
    <!--/ Layout Content -->


    <!-- Include Scripts -->
    @include('layouts/sections/scripts')

</body>

</html>
