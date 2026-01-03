<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        /* Reset styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        /* Email container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .email-header .logo {
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        
        .email-header .logo i {
            margin-right: 10px;
        }
        
        /* Content */
        .email-content {
            padding: 30px;
        }
        
        .email-title {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .email-body {
            color: #555;
            margin-bottom: 30px;
        }
        
        /* Actions */
        .email-actions {
            margin: 30px 0;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            margin: 0 5px;
        }
        
        .btn-primary {
            background-color: #3498db;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
        }
        
        /* Footer */
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        
        .footer-links {
            margin-top: 15px;
        }
        
        .footer-links a {
            color: #3498db;
            text-decoration: none;
            margin: 0 10px;
        }
        
        /* Table styles for data display */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .data-table tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Alert boxes */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            
            .email-content {
                padding: 20px;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <a href="{{ config('app.url') }}" class="logo">
                <i class="fas fa-industry"></i>{{ config('app.name', 'ERP System') }}
            </a>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'ERP System') }}. All rights reserved.</p>
            
            <div class="footer-links">
                <a href="{{ config('app.url') }}">Home</a> |
                <a href="{{ config('app.url') }}/privacy">Privacy Policy</a> |
                <a href="{{ config('app.url') }}/terms">Terms of Service</a> |
                <a href="{{ config('app.url') }}/contact">Contact Us</a>
            </div>
            
            <p style="margin-top: 15px; font-size: 12px; color: #95a5a6;">
                This email was sent to {{ $to ?? 'you' }}. 
                If you believe you received this email in error, please ignore it.
            </p>
        </div>
    </div>
</body>
</html>