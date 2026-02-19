<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Styles -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Nunito', sans-serif; /* font diubah */
        }

        body {
            display: flex;
            align-items: stretch;
            justify-content: stretch;
            background: linear-gradient(135deg,#1E3A8A,#374785);
            position: relative;
        }

        /* BULATAN TRANSPARAN DI BACKGROUND */
        body::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -100px;
            left: -150px;
            pointer-events: none;
        }
        body::after {
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            bottom: -80px;
            right: -100px;
            pointer-events: none;
        }

        /* FULL SCREEN CARD */
        .login-card {
            display: flex;
            width: 90%;
            max-width: 1200px;
            min-height: 600px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 50px rgba(0,0,0,0.3);
        }

        /* LEFT PANEL */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg,#1E3A8A,#3B82F6);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 3rem 2rem;
            position: relative;
        }

        /* tambahan bulatan di panel kiri */
        .login-left::before {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            top: 50px;
            left: 30px;
        }
        .login-left::after {
            content: "";
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            bottom: 40px;
            right: 20px;
        }

        .login-left img {
            width: 180px; /* logo lebih besar */
            margin-bottom: 2rem; /* jarak ke judul */
        }

        .login-title {
            font-size: 2rem; /* judul lebih besar */
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            text-align: center;
        }

        .login-left p {
            font-size: 1.1rem;
            line-height: 1.5;
            opacity: 0.85;
            text-align: center;
            max-width: 220px;
        }

        /* RIGHT PANEL */
        .login-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .login-form-container {
            width: 100%;
            max-width: 400px;
            background: rgba(255,255,255,0.9);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }

        .login-form-container h1 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1E3A8A;
            font-family: 'Nunito', sans-serif;
        }

        .login-form-container p {
            color: #4B5563;
            margin-bottom: 2rem;
            font-family: 'Nunito', sans-serif;
        }

        .form-control-user {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,1);
            border: 1px solid rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            font-family: 'Nunito', sans-serif;
        }

        .form-control-user:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 10px rgba(59,130,246,0.3);
            background: rgba(255,255,255,1);
        }

        .btn-login {
            background: linear-gradient(135deg,#3B82F6,#1E40AF);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            font-family: 'Nunito', sans-serif;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
            opacity: 0.95;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-card {
                flex-direction: column;
            }
            .login-left {
                display: none;
            }
            .login-right {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    @yield('main-content')

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>
</html>
