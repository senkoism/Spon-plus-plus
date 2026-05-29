@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Welcome Back</h4>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form id="login-form" action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="login_identifier" class="form-label text-muted small fw-bold">Email or Username</label>
            <input type="text" class="form-control @error('login') is-invalid @enderror" id="login_identifier" name="login" value="{{ old('login') }}" placeholder="Enter email or username" required>
            @error('login')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="password" class="form-label text-muted small fw-bold">Password</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <i data-lucide="eye" style="width:20px;height:20px"></i>
                </span>
            </div>
        </div>

        <script>
            function togglePassword(id) {
                const input = document.getElementById(id);
                const toggle = input.nextElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.innerHTML = '<i data-lucide="eye-off" style="width:20px;height:20px"></i>';
                } else {
                    input.type = 'password';
                    toggle.innerHTML = '<i data-lucide="eye" style="width:20px;height:20px"></i>';
                }
                lucide.createIcons();
            }

            // Change 5 — Remember Me: Save email only
            window.addEventListener('DOMContentLoaded', () => {
                const savedEmail = localStorage.getItem('remembered_email');
                const identifierInput = document.getElementById('login_identifier');
                const rememberCheckbox = document.getElementById('remember_me');

                if (savedEmail && identifierInput) {
                    identifierInput.value = savedEmail;
                    if (rememberCheckbox) rememberCheckbox.checked = true;
                }
            });

            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('login-form')?.addEventListener('submit', () => {
                    const identifierInput = document.getElementById('login_identifier');
                    const rememberCheckbox = document.getElementById('remember_me');

                    if (rememberCheckbox?.checked) {
                        localStorage.setItem('remembered_email', identifierInput.value);
                    } else {
                        localStorage.removeItem('remembered_email');
                    }
                });
            });
        </script>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label small text-muted" for="remember_me">
                    Remember me
                </label>
            </div>
            <a href="#" class="small text-decoration-none" style="color: #764ba2;">Forgot Password?</a>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
        <p class="text-center small text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: #764ba2;">Register</a></p>
    </form>
@endsection
