@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Welcome Back</h4>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="login" class="form-label text-muted small fw-bold">Email or Username</label>
            <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" placeholder="Enter email or username" required>
            @error('login')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="password" class="form-label text-muted small fw-bold">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small text-muted" for="remember">
                    Remember me
                </label>
            </div>
            <a href="#" class="small text-decoration-none" style="color: #764ba2;">Forgot Password?</a>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
        <p class="text-center small text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: #764ba2;">Register</a></p>
    </form>
@endsection
