@extends('layouts.auth')

@section('main-content')
<div class="d-flex justify-content-center align-items-center w-100 h-100">
    <div class="login-card">

        <!-- LEFT PANEL -->
        <div class="login-left d-none d-lg-flex flex-column align-items-center justify-content-center">
            <img src="{{ asset('img/logo_imigrasi.png') }}" alt="Logo">
            <h4 class="mt-1 login-title">SISTEM KEARSIPAN</h4>
            <p class="small mt-1">
                Kantor Imigrasi Kelas I Khusus TPI Surabaya
            </p>
        </div>

        <!-- RIGHT PANEL -->
        <div class="login-right">
            <div class="login-form-container">
                <h1 class="h4 font-weight-bold">Login Sistem</h1>
                <p class="small">Masuk untuk melanjutkan ke dashboard</p>

                @if ($errors->any())
                    <div class="alert alert-danger border-left-danger">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <input type="email"
                               class="form-control form-control-user"
                               name="email"
                               placeholder="Email"
                               value="{{ old('email') }}"
                               required autofocus>
                    </div>

                    <div class="form-group">
                        <input type="password"
                               class="form-control form-control-user"
                               name="password"
                               placeholder="Password"
                               required>
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="custom-control custom-checkbox small">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label" for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login btn-user btn-block text-white shadow-sm">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center small text-gray-500">
                    © {{ date('Y') }} Sistem Arsiparis Imigrasi
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
