@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">
            <div class="card-body p-4">
                <div class="text-center">
                    <div class="avatar-lg mx-auto">
                        <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-2">
                        <h4>Confirm Password</h4>
                        <p class="text-muted">
                            This is a secure area of the application. Please confirm your password before continuing.
                        </p>
                    </div>
                </div>

                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <div class="position-relative input-custom-icon">
                                <input type="password" class="form-control" id="password" 
                                       name="password" required autocomplete="current-password">
                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-primary w-100" type="submit">Confirm Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection