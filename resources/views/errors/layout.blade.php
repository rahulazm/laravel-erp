<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel ERP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-content {
            text-align: center;
            max-width: 600px;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }
        
        .error-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .error-message {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .error-actions {
            margin-top: 2rem;
        }
        
        .btn-error {
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .btn-error-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }
        
        .btn-error-primary:hover {
            background-color: #0056b3;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-error-secondary {
            background-color: transparent;
            color: #007bff;
            border: 1px solid #007bff;
        }
        
        .btn-error-secondary:hover {
            background-color: #007bff;
            color: white;
            transform: translateY(-2px);
        }
        
        .error-details {
            margin-top: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            text-align: left;
        }
        
        .error-details-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #dc3545;
        }
        
        @media (max-width: 768px) {
            .error-title {
                font-size: 2rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
            
            .error-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-error {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1 class="error-title">@yield('code', 'Error')</h1>
            
            <p class="error-message">
                @yield('message')
            </p>
            
            <div class="error-actions">
                <a href="{{ url()->previous() }}" class="btn-error btn-error-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Go Back
                </a>
                <a href="{{ route('dashboard') }}" class="btn-error btn-error-primary">
                    <i class="fas fa-home me-2"></i>Go Home
                </a>
            </div>
            
            @hasSection('details')
                <div class="error-details">
                    <div class="error-details-title">Error Details:</div>
                    @yield('details')
                </div>
            @endif
            
            @if(app()->environment('local') && isset($exception) && $exception instanceof Throwable)
                <div class="error-details mt-3">
                    <div class="error-details-title">Debug Information:</div>
                    <pre class="mb-0 small">{{ $exception->getMessage() }}
{{ $exception->getFile() }}:{{ $exception->getLine() }}</pre>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>