@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">
            <div class="card-body p-4">
                <div class="text-center">
                    <div class="avatar-lg mx-auto">
                        <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-2">
                        <h4>Verify your email</h4>
                        <p class="text-muted">
                            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success" role="alert">
                            A new verification link has been sent to the email address you provided during registration.
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                Resend Verification Email
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> ERP System.</p>
        </div>
    </div>
</div>
@endsection