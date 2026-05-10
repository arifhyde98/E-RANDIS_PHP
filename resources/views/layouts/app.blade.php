<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-RANDIS PHP') }}</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 260px;
        }
        body {
            background-color: #f8fafc;
            overflow-x: hidden;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: #fff;
            color: #1e293b;
            transition: all 0.3s;
            min-height: 100vh;
            border-right: 1px solid rgba(0,0,0,0.05);
            z-index: 999;
        }
        #sidebar .sidebar-header {
            padding: 2rem 1.5rem;
        }
        #sidebar ul.components {
            padding: 0 1rem;
        }
        #sidebar ul li a {
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            display: block;
            color: #64748b;
            text-decoration: none;
            border-radius: 0.75rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
            font-weight: 500;
        }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: #4f46e5;
            background: #f1f5f9;
        }
        #sidebar ul li a i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        #content {
            width: 100%;
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .navbar-admin {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <h3 class="fw-bold text-gradient mb-0">E-RANDIS</h3>
                    <small class="text-secondary fw-medium">Monitoring System</small>
                </a>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ Request::is('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                
                <li class="mt-4 mb-2 ps-3">
                    <small class="text-uppercase fw-bold text-muted small tracking-wider">Data Master</small>
                </li>
                
                <li class="{{ Request::is('vehicles*') ? 'active' : '' }}">
                    <a href="{{ route('vehicles.index') }}"><i class="bi bi-car-front"></i> Data Kendaraan</a>
                </li>
                
                <li class="{{ Request::is('users*') ? 'active' : '' }}">
                    <a href="#"><i class="bi bi-people"></i> Data Pegawai</a>
                </li>

                <li class="mt-4 mb-2 ps-3">
                    <small class="text-uppercase fw-bold text-muted small tracking-wider">Laporan</small>
                </li>
                <li>
                    <a href="#"><i class="bi bi-file-earmark-pdf"></i> Rekapitulasi</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-admin rounded-4 mb-4 px-3">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light rounded-3 d-lg-none">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center gap-3">
                        @guest
                            <!-- Guest Links -->
                        @else
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark fw-bold" data-bs-toggle="dropdown">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end premium-card border-0 shadow-lg p-2 mt-2">
                                    <li><a class="dropdown-item rounded-3" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item rounded-3 text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endguest
                    </div>
                </div>
            </nav>

            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const sidebar = document.getElementById('sidebar');
            if(sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
