<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-RANDIS PHP') }} - Admin Dashboard</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light" id="theme-root">
    <script>
        // Pre-initialization theme check to avoid flicker
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.getElementById('theme-root').setAttribute('data-theme', savedTheme);
            if(savedTheme === 'dark') {
                document.body.classList.remove('bg-light');
            }
        })();
    </script>
    <div class="wrapper">
        @include('layouts.partials.sidebar')

        <!-- Page Content -->
        <div id="content">
            @include('layouts.partials.navbar')

            <!-- Main Content Area -->
            <main class="animate-on-scroll">
                @yield('content')
            </main>
            
            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- Theme Toggle & UI Interactivity JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Intersection Observer for fade-in animations
            const observerOptions = { threshold: 0.1 };
            const scrollObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                        scrollObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                scrollObserver.observe(el);
            });

            // 2. Theme Logic
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const root = document.getElementById('theme-root');
            
            function updateTheme(theme) {
                root.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                
                if (theme === 'dark') {
                    if(themeIcon) themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
                    document.body.classList.remove('bg-light');
                } else {
                    if(themeIcon) themeIcon.classList.replace('bi-sun', 'bi-moon-stars');
                    document.body.classList.add('bg-light');
                }
            }
            
            const currentTheme = localStorage.getItem('theme') || 'light';
            updateTheme(currentTheme);
            
            if(themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const newTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    updateTheme(newTheme);
                });
            }
            
            // 3. Sidebar Logic
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            
            if(sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                    if(window.innerWidth > 768) {
                        content.style.marginLeft = sidebar.classList.contains('active') ? '0' : '260px';
                    }
                });
            }
            
            window.addEventListener('resize', function() {
                if(window.innerWidth <= 768) {
                    sidebar.classList.add('active');
                    content.style.marginLeft = '0';
                } else {
                    sidebar.classList.remove('active');
                    content.style.marginLeft = '260px';
                }
            });
            
            if(window.innerWidth <= 768) {
                sidebar.classList.add('active');
                content.style.marginLeft = '0';
            }
        });
    </script>
</body>
</html>
