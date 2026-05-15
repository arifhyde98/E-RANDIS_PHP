<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-admin rounded-3 mb-4">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="sidebarCollapse" class="btn btn-light rounded-3 shadow-sm border">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="fw-bold text-navy mb-0 d-none d-md-block">@yield('title', 'Admin Dashboard')</h5>
        </div>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            @guest
                <!-- Guest Links -->
            @else
                <!-- Notification -->
                <a href="#" class="text-secondary position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </a>

                <!-- Theme Toggle -->
                <button type="button" id="themeToggle" class="btn btn-light rounded-3 shadow-sm border text-secondary px-2 py-1">
                    <i class="bi bi-moon-stars fs-5" id="themeIcon"></i>
                </button>
                
                <div class="vr mx-1"></div>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark fw-medium" data-bs-dropdown-toggle="dropdown" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->avatar_url }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="rounded-circle me-2 border shadow-sm" 
                             style="width: 35px; height: 35px; object-fit: cover;">
                        <div class="d-none d-md-block text-start lh-1">
                            <div class="fw-bold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                            <small class="text-secondary" style="font-size: 0.75rem;">{{ Auth::user()->role->label() }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 rounded-3">
                        <li><a class="dropdown-item py-2" href="{{ route('profile.index') }}"><i class="bi bi-person me-2 text-secondary"></i> Profil Saya</a></li>
                        @if(Auth::user()->role === \App\Enums\UserRole::SUPERADMIN)
                            <li><a class="dropdown-item py-2" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2 text-secondary"></i> Pengaturan Sistem</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger fw-medium" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
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
