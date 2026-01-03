@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">
            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Forgot Password?</h5>
                    <p class="text-muted">Reset password with ERP System</p>
                    <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop" 
                               colors="primary:#0ab39c" class="avatar-xl"></lord-icon>
                </div>

                <div class="alert alert-borderless alert-warning text-center mb-2 mx-2" role="alert">
                    Enter your email and instructions will be sent to you!
                </div>

                <div class="p-2">
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <div class="position-relative input-custom-icon">
                                <input type="email" class="form-control" name="email" 
                                       value="{{ old('email') }}" required>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-success w-100" type="submit">Send Reset Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="mb-0">Wait, I remember my password... 
                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Click here </a>
            </p>
        </div>
    </div>
</div>
@endsection