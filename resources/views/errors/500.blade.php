<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Figtree', sans-serif;
        }
        
        .error-container {
            max-width: 500px;
            text-align: center;
            color: white;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 0 rgba(0,0,0,0.1);
        }
        
        .error-icon {
            font-size: 6rem;
            margin-bottom: 2rem;
            color: #ff6b6b;
        }
        
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .error-description {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 3rem;
        }
        
        .error-details {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
            font-family: monospace;
            font-size: 0.9rem;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .btn-home {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-server"></i>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-message">Internal Server Error</h1>
        <p class="error-description">
            Something went wrong on our servers. 
            Our team has been notified and is working to fix the issue.
        </p>
        
        @if(app()->environment('local') && isset($exception))
            <div class="error-details">
                <strong>Error Details:</strong><br>
                {{ $exception->getMessage() }}
            </div>
        @endif
        
        <div class="mt-4">
            <a href="javascript:location.reload()" class="btn-home me-3">
                <i class="fas fa-redo me-2"></i>Refresh
            </a>
            <a href="{{ route('dashboard') }}" class="btn-home">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
        </div>
        
        <div class="mt-4">
            <small class="text-white-50">
                If the problem persists, please contact support.
            </small>
        </div>
    </div>
</body>
</html>