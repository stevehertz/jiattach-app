<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"/>

<title>{{ config('app.name') }} | Student Portal</title>

<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">

<style>
    /* Custom styling for student portal */
    .student-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    .student-sidebar {
        background-color: #f8f9fa;
    }

    .opportunity-card {
        border-left: 4px solid #007bff;
        transition: transform 0.2s;
    }

    .opportunity-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .avatar-initials {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
    }

    .profile-complete-bar {
        height: 5px;
        background-color: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
    }

    .profile-complete-fill {
        height: 100%;
        background-color: #28a745;
        transition: width 0.3s ease;
    }
</style>
