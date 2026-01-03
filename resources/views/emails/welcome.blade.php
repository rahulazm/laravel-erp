@extends('emails.layout')

@section('title', 'Welcome to ' . config('app.name'))

@section('content')
    <h2 class="email-title">Welcome aboard!</h2>
    
    <div class="email-body">
        <p>Hello {{ $user->name }},</p>
        
        <p>Welcome to <strong>{{ config('app.name') }}</strong>! We're excited to have you join our team.</p>
        
        <div class="alert alert-info">
            <p><strong>Your account details:</strong></p>
            <ul>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Employee ID:</strong> {{ $user->employee_id }}</li>
                <li><strong>Department:</strong> {{ ucfirst($user->department) }}</li>
                <li><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</li>
            </ul>
        </div>
        
        <p>You now have access to our ERP system where you can:</p>
        <ul>
            <li>Create and manage sales orders</li>
            <li>Work with Bill of Materials (BOMs)</li>
            <li>Manage inventory items</li>
            <li>Track production workflows</li>
            <li>Generate reports and analytics</li>
        </ul>
        
        <p>To get started, please log in to your account using the button below:</p>
    </div>
    
    <div class="email-actions">
        <a href="{{ route('login') }}" class="btn btn-primary">Login to Your Account</a>
    </div>
    
    <div class="email-body">
        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        
        <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
    </div>
@endsection