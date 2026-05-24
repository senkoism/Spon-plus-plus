<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Spon++') }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            transition: transform 0.3s ease;
        }
        .auth-card:hover {
            transform: translateY(-5px);
        }
        .btn-primary {
            background: #764ba2;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #667eea;
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4);
        }
        .form-control {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.2);
            border-color: #764ba2;
        }
        .auth-title {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 30px;
            text-align: center;
        }
        .brand-logo {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="brand-logo">Spon++</div>
        @yield('content')
    </div>
</body>
</html>
