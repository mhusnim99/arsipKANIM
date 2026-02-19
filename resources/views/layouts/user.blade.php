<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Styles -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        /* ============ SIDEBAR CUSTOM UI ============ */
        .sidebar {
            background: linear-gradient(180deg, #1E3A8A, #374785); /* navy gradient sama seperti admin */
        }

        .sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, .9);
            border-radius: 12px;
            margin: 4px 10px;
            transition: .3s;
        }

        .sidebar .nav-item .nav-link:hover {
            background: rgba(255, 255, 255, .15);
            transform: translateX(4px);
        }

        .sidebar .nav-item.active .nav-link {
            background: linear-gradient(135deg, #3B82F6, #1E40AF);
            box-shadow: 0 4px 15px rgba(0, 0, 0, .15);
        }

        .sidebar-brand {
            background: none; /* hilangkan putih */
            border-radius: 0;
            margin-bottom: 10px;
        }

        .sidebar-brand-text {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fff; /* teks putih tetap */
        }

        .sidebar-divider {
            border-color: rgba(255, 255, 255, .15);
        }

        /* ============ TOPBAR ============ */
        .topbar {
            background: linear-gradient(90deg, #1E3A8A, #374785);
        }

        .topbar .nav-link,
        .topbar .text-gray-600 {
            color: #fff !important;
        }

        /* Avatar tetap emas/kuning */
        .avatar {
            background: linear-gradient(135deg, #f6d365, #fda085);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            font-weight: 700;
        }
    </style>

    @stack('styles')
</head>

<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center py-4" href="{{ url('/home') }}">
            <div class="sidebar-brand-icon">
                <img src="{{ asset('img/logo_imigrasi.png') }}" style="width:38px">
            </div>
            <div class="sidebar-brand-text mx-3 text-uppercase">
                Sistem Arsiparis
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item {{ request()->routeIs('user.pengiriman') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.pengiriman') }}">
                <i class="fas fa-fw fa-paper-plane"></i>
                <span>Pengiriman Berkas</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('user.pengiriman.ditolak') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.pengiriman.ditolak') }}">
                <i class="fas fa-fw fa-times-circle"></i>
                <span>Berkas Ditolak</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0 bg-navy" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
                           role="button" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline small font-weight-bold">
                                {{ Auth::user()->name }}
                            </span>
                            <div class="img-profile rounded-circle avatar">
                                {{ strtoupper(Auth::user()->name[0]) }}
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item text-danger" href="#"
                               data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>
            </nav>

            <div class="container-fluid">
                @yield('main-content')
            </div>

        </div>
    </div>

</div>

<!-- Scroll to Top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-gradient-danger text-white">
                <h5 class="modal-title">Logout</h5>
                <button class="close text-white" type="button" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">Yakin ingin keluar dari sistem?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <a class="btn btn-danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

@yield('scripts')

</body>
</html>
