@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Create Account</h4>

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label text-muted small fw-bold">Full Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="username" class="form-label text-muted small fw-bold">Username</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="johndoe123" required>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label text-muted small fw-bold">Email Address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="password" class="form-label text-muted small fw-bold">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label text-muted small fw-bold">Confirm</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
        <p class="text-center small text-muted">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: #764ba2;">Login</a></p>
    </form>
@endsection
