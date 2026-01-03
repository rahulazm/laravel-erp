<!DOCTYPE html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Laravel ERP') }}</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap Css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons Css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- App Css-->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .auth-page-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.75rem;
        }
        
        .input-custom-icon {
            position: relative;
        }
        
        .input-custom-icon input {
            padding-left: 40px;
        }
        
        .input-custom-icon span {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 4;
        }
        
        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo .logo {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            text-decoration: none;
        }
        
        .auth-logo .logo i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .password-addon {
            cursor: pointer;
            color: #6c757d;
        }
        
        .password-addon:hover {
            color: #2c3e50;
        }
        
        .auth-bg {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="1440" height="1024" viewBox="0 0 1440 1024" fill="none"><path d="M0 0L1440 1024H0V0Z" fill="url(%23paint0_linear)"/><defs><linearGradient id="paint0_linear" x1="720" y1="0" x2="720" y2="1024" gradientUnits="userSpaceOnUse"><stop stop-color="%23667eea"/><stop offset="1" stop-color="%23764ba2"/></linearGradient></defs></svg>');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }
        
        .auth-full-page-content {
            min-height: 100vh;
            display: flex;
        }
        
        .auth-review-carousel .carousel-item {
            padding: 2rem;
        }
        
        .avatar-xl {
            height: 5rem;
            width: 5rem;
        }
        
        .alert-borderless {
            border: 1px solid transparent;
        }
    </style>

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-page-wrapper pt-5">
        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="auth-logo">
                            <a href="{{ route('dashboard') }}" class="logo">
                                <i class="fas fa-industry"></i>
                                {{ config('app.name', 'Laravel ERP') }}
                            </a>
                        </div>
                        
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-white">
                                &copy; <script>document.write(new Date().getFullYear())</script> {{ config('app.name', 'Laravel ERP') }}.
                                Crafted with <i class="fas fa-heart text-danger"></i> by Your Company
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>

    @stack('scripts')
</body>

</html>