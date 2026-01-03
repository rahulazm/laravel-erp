<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Too Many Requests</title>
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
        
        .countdown {
            font-size: 2rem;
            font-weight: 700;
            margin: 20px 0;
            color: #ffd43b;
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
            <i class="fas fa-tachometer-alt"></i>
        </div>
        <div class="error-code">429</div>
        <h1 class="error-message">Too Many Requests</h1>
        <p class="error-description">
            You have sent too many requests in a given amount of time.
            Please wait before trying again.
        </p>
        <div class="countdown" id="countdown">30</div>
        <a href="{{ route('dashboard') }}" class="btn-home" id="retry-btn" style="display: none;">
            <i class="fas fa-redo me-2"></i>Try Again
        </a>
    </div>

    <script>
        let countdown = 30;
        const countdownElement = document.getElementById('countdown');
        const retryBtn = document.getElementById('retry-btn');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                countdownElement.style.display = 'none';
                retryBtn.style.display = 'inline-block';
            }
        }, 1000);
    </script>
</body>
</html>