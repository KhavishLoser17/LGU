<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Local Government Unit 2')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


    @stack('styles')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

</head>
<body class="font-montserrat text-gray-900 bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="h-15 bg-blue-900 text-white flex items-center justify-between px-3 sticky top-0 z-50">
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn" class="lg:hidden text-white text-xl p-2" aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Title -->
        <div class="font-extrabold tracking-wider text-lg">
            LOCAL GOVERNMENT UNIT 2
        </div>

        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="profile-dropdown" class="flex items-center gap-2 bg-white text-gray-900 px-3 py-2 rounded hover:bg-gray-100 transition-colors">
                <div class="w-8 h-8 rounded-full overflow-hidden">
                    <img src="{{ asset('images/default-avatar.jpg') }}" alt="User" class="w-full h-full object-cover">
                </div>
                <i class="fa-solid fa-caret-down"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="profile-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 hidden">
                <a href="" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fa-regular fa-user mr-2"></i>
                    Profile
                </a>
                <a href="" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fa-solid fa-gear mr-2"></i>
                    Settings
                </a>
                <hr class="my-1">
                <button id="logout-btn" class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>
                    Logout
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Backdrop -->
    <div id="mobile-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Main Layout -->
<div class="flex-1 flex min-h-[calc(100vh-60px)]">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content -->
        <main id="main-content" class="flex-1 p-5 lg:ml-0 transition-all duration-300">
            <div class="bg-white rounded-xl shadow-lg p-5">
                @yield('content')
            </div>
        </main>
  </div>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Logout</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to log out?</p>
                <div class="flex justify-end gap-3">
                    <button id="cancel-logout" class="px-4 py-2 text-gray-600 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button id="confirm-logout" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700 transition-colors">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobile-backdrop');
        const mainContent = document.getElementById('main-content');

        mobileMenuBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        });

        backdrop?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });

        // Profile dropdown
        const profileDropdown = document.getElementById('profile-dropdown');
        const profileMenu = document.getElementById('profile-menu');

        profileDropdown?.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            profileMenu?.classList.add('hidden');
        });

        // Logout modal
        const logoutBtn = document.getElementById('logout-btn');
        const logoutModal = document.getElementById('logout-modal');
        const cancelLogout = document.getElementById('cancel-logout');
        const confirmLogout = document.getElementById('confirm-logout');

        logoutBtn?.addEventListener('click', () => {
            logoutModal.classList.remove('hidden');
            profileMenu?.classList.add('hidden');
        });

        cancelLogout?.addEventListener('click', () => {
            logoutModal.classList.add('hidden');
        });

        confirmLogout?.addEventListener('click', () => {
            window.location.href = '';
        });

        // Close modal when clicking backdrop
        logoutModal?.addEventListener('click', (e) => {
            if (e.target === logoutModal) {
                logoutModal.classList.add('hidden');
            }
        });

        // Sidebar navigation groups
        document.querySelectorAll('.nav-group-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const navGroup = toggle.closest('.nav-group');
                const sublist = navGroup.querySelector('.nav-sublist');
                const caret = toggle.querySelector('.caret');

                navGroup.classList.toggle('open');

                if (navGroup.classList.contains('open')) {
                    sublist.style.maxHeight = sublist.scrollHeight + 'px';
                    caret?.classList.add('rotate-180');
                } else {
                    sublist.style.maxHeight = '0';
                    caret?.classList.remove('rotate-180');
                }
            });
        });

        // Set current year in footer
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>
