@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-5">
            <div class="premium-card p-4 p-lg-5">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-gradient">Admin Login</h2>
                    <p class="text-secondary">Sistem Monitoring E-RANDIS PHP</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold small text-uppercase tracking-wider">Email Address</label>
                        <input id="email" type="email" class="form-control form-control-lg premium-card border-0 bg-light bg-opacity-50 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="password" class="form-label fw-semibold small text-uppercase tracking-wider mb-0">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small fw-medium" href="{{ route('password.request') }}">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>
                        <input id="password" type="password" class="form-control form-control-lg premium-card border-0 bg-light bg-opacity-50 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">
                                Keep me logged in
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-premium py-3 fs-5">
                            Sign In to Dashboard
                        </button>
                    </div>

                    @if (Route::has('register'))
                        <div class="text-center">
                            <span class="text-secondary small">Don't have an account?</span>
                            <a href="{{ route('register') }}" class="text-decoration-none small fw-bold ms-1">Register Now</a>
                        </div>
                    @endif
                </form>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Back to Homepage
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
