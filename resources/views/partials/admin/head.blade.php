<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>{{ config('app.name') }} | Dashboard</title>

<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
<!-- IonIcons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">

@livewireStyles

<style>
    /* Custom styling for Flux menu items in dropdown */
    .flux-dropdown-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0.5rem 1rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        transition: background-color 0.15s ease-in-out;
        cursor: pointer;
    }

    .flux-dropdown-item:hover {
        color: #16181b;
        text-decoration: none;
        background-color: #f8f9fa;
    }

    .flux-dropdown-item:focus {
        outline: 0;
        background-color: #f8f9fa;
    }

    .flux-dropdown-item .flux-icon {
        width: 0.5rem;
        height: 0.5rem;
        margin-right: 0.5rem;
        opacity: 0.75;
    }
</style>

