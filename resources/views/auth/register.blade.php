@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-6">
            <div class="premium-card p-4 p-lg-5">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-gradient">Create Admin Account</h2>
                    <p class="text-secondary">Daftarkan akun administrator baru</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label for="name" class="form-label fw-semibold small text-uppercase tracking-wider">Full Name</label>
                            <input id="name" type="text" class="form-control form-control-lg premium-card border-0 bg-light bg-opacity-50 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-4">
                            <label for="email" class="form-label fw-semibold small text-uppercase tracking-wider">Email Address</label>
                            <input id="email" type="email" class="form-control form-control-lg premium-card border-0 bg-light bg-opacity-50 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="john@example.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label fw-semibold small text-uppercase tracking-wider">Password</label>
                            <input id="password" type="password" class="form-control form-control-lg premium-card border-0 bg-light bg-opacity-50 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="password-confirm" class="form-label fw-semibold small text-uppercase tracking-wider">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control form-control-lg premium-card border-0 bg-light bg-opacity-50" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-premium py-3 fs-5">
                            Register Account
                        </button>
                    </div>

                    <div class="text-center">
                        <span class="text-secondary small">Already have an account?</span>
                        <a href="{{ route('login') }}" class="text-decoration-none small fw-bold ms-1">Sign In Instead</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
