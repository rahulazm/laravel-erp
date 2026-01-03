@extends('emails.layout')

@section('title', 'Password Reset Request')

@section('content')
    <h2 class="email-title">Password Reset</h2>
    
    <div class="email-body">
        <p>Hello,</p>
        
        <p>We received a request to reset your password for your {{ config('app.name') }} account.</p>
        
        <div class="alert alert-warning">
            <p><strong>Note:</strong> This password reset link will expire in 60 minutes.</p>
            <p>If you did not request a password reset, please ignore this email.</p>
        </div>
        
        <p>To reset your password, click the button below:</p>
    </div>
    
    <div class="email-actions">
        <a href="{{ $resetUrl }}" class="btn btn-primary">Reset Password</a>
    </div>
    
    <div class="email-body">
        <p>Or copy and paste the following URL into your browser:</p>
        <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; word-break: break-all;">
            {{ $resetUrl }}
        </div>
        
        <p style="margin-top: 20px;">If you're having trouble clicking the password reset button, copy and paste the URL above into your web browser.</p>
        
        <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
    </div>
@endsection