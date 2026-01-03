@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">
            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Create new password</h5>
                    <p class="text-muted">Your new password must be different from previous used password</p>
                </div>

                <div class="p-2">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <div class="position-relative input-custom-icon">
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ $email ?? old('email') }}" required readonly>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">New Password</label>
                            <div class="position-relative input-custom-icon">
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="position-relative input-custom-icon">
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="mb-0">Remember It ? 
                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Signin </a>
            </p>
        </div>
    </div>
</div>
@endsection