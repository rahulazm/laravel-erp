@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">
            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Welcome Back!</h5>
                    <p class="text-muted">Sign in to continue to ERP System.</p>
                </div>
                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="position-relative input-custom-icon">
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required autofocus>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password-input">Password</label>
                            <div class="position-relative input-custom-icon">
                                <input type="password" class="form-control" id="password-input" 
                                       name="password" required>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <button class="btn btn-link position-absolute top-50 end-0 translate-middle-y" 
                                        type="button" id="password-addon">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" 
                                   id="auth-remember-check" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="auth-remember-check">
                                Remember me
                            </label>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-primary w-100" type="submit">Sign In</button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="mt-4 text-center">
                                <a href="{{ route('password.request') }}" class="text-muted">
                                    <i class="fas fa-key me-1"></i> Forgot your password?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> ERP System. 
                Crafted with <i class="fas fa-heart text-danger"></i> by Your Company</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('password-addon').addEventListener('click', function () {
    const passwordInput = document.getElementById('password-input');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
@endpush