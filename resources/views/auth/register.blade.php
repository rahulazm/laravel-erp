@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">
            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Create New Account</h5>
                    <p class="text-muted">Get your free ERP account now</p>
                </div>

                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="position-relative input-custom-icon">
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required autofocus>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="position-relative input-custom-icon">
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <div class="position-relative input-custom-icon">
                                <input type="text" class="form-control" id="employee_id" name="employee_id" 
                                       value="{{ old('employee_id') }}" required>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-id-card"></i>
                                </span>
                            </div>
                            @error('employee_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <option value="engineering" {{ old('department') == 'engineering' ? 'selected' : '' }}>Engineering</option>
                                <option value="production" {{ old('department') == 'production' ? 'selected' : '' }}>Production</option>
                                <option value="quality" {{ old('department') == 'quality' ? 'selected' : '' }}>Quality</option>
                                <option value="purchase" {{ old('department') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="sales" {{ old('department') == 'sales' ? 'selected' : '' }}>Sales</option>
                                <option value="admin" {{ old('department') == 'admin' ? 'selected' : '' }}>Administration</option>
                            </select>
                            @error('department')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <div class="position-relative input-custom-icon">
                                <input type="text" class="form-control" id="designation" name="designation" 
                                       value="{{ old('designation') }}" required>
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-briefcase"></i>
                                </span>
                            </div>
                            @error('designation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
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
                            <button class="btn btn-primary w-100" type="submit">Create Account</button>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="mb-0">Already have an account ? 
                                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Login</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> ERP System. 
                All rights reserved.</p>
        </div>
    </div>
</div>
@endsection