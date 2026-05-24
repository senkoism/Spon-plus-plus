<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Spon++') }} - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.5rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .sidebar-link {
            padding: 12px 20px;
            border-radius: 10px;
            color: #4a5568;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            margin-bottom: 5px;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(118, 75, 162, 0.1);
            color: #764ba2;
            font-weight: 600;
        }
        .btn-logout {
            color: #e53e3e;
            background: none;
            border: none;
            padding: 0;
            font-weight: 500;
        }
        .btn-logout:hover {
            color: #c53030;
        }
    </style>
</head>
<body>
    <nav class="navbar sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Spon++</a>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted small">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout small">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card p-3 mb-4">
                    <a href="{{ route('dashboard') }}" class="sidebar-link active">
                        Dashboard
                    </a>
                    <a href="#" class="sidebar-link">
                        My Courses
                    </a>
                    <a href="#" class="sidebar-link">
                        Profile
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
