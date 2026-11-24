<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SelangorCareConnect+</title>

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <!--@vite('resources/js/app.js') -->

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#ec1313",
                        "selangor-yellow": "#ffcc00"
                    }
                }
            }
        }
    </script>

</head>

<body class="bg-gray-50 font-sans">

    <!-- HEADER (STANDARDIZED FOR ALL PAGES) -->
    @php

        $unreadCount = 0;

        if (session('user')) {

            $unreadCount = \App\Models\UserNotification::where(
                'user_id',
                session('user.id')
            )
                ->where(
                    'is_read',
                    false
                )
                ->count();
        }

    @endphp
    <header
        class="fixed top-0 left-0 w-full z-50 h-16 flex justify-between items-center px-8 bg-white border-b border-gray-100 shadow-sm">

        <div class="flex items-center gap-3">
            <img src="{{ asset('images/selangorcareconnect.png') }}" alt="SelangorCareConnect Logo"
                class="h-12 object-contain">

            <div>
                <h2 class="font-bold text-lg">SelangorCareConnect+</h2>
                <p class="text-xs text-gray-500">Official Community Portal</p>
            </div>
        </div>
        <div class="flex items-center gap-4">

            <a href="/donor/notifications" class="relative flex items-center">

                <span class="material-symbols-outlined text-3xl text-gray-600">
                    notifications
                </span>

                @if($unreadCount > 0)

                    <span
                        class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center">

                        {{ $unreadCount }}

                    </span>

                @endif

            </a>

        </div>
    </header>

    <div class="flex h-[calc(100vh-64px)] mt-16">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-white border-r hidden lg:flex flex-col">


            <nav class="p-4 space-y-2 text-sm">

                <!-- DONOR DASHBOARD -->
                <a href="/donor/dashboard"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('donor/dashboard')
                        ? 'bg-red-50 text-red-600 font-semibold'
                        : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <p class="px-3 pt-5 pb-1 text-[11px] uppercase tracking-wider font-bold text-gray-400">
                    Donation
                </p>

                <!-- CAMPAIGN FUND -->
                <a href="/donor/donation"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('donor/donation*')
                        ? 'bg-red-50 text-red-600 font-semibold'
                        : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">campaign</span>
                    <span>Campaign Fund</span>
                </a>

                <!-- GENERAL FUND -->
                <a href="/donor/fund"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('donor/fund*')
                        ? 'bg-red-50 text-red-600 font-semibold'
                        : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">volunteer_activism</span>
                    <span>General Fund</span>
                </a>

                <!-- DONATION HISTORY -->
                <a href="/donor/history"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('donor/history*')
                        ? 'bg-red-50 text-red-600 font-semibold'
                        : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                    <span>Donation History</span>
                </a>

                <p class="px-3 pt-5 pb-1 text-[11px] uppercase tracking-wider font-bold text-gray-400">
                    Account
                </p>

                <!-- MY PROFILE -->
                <a href="/profile"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('profile*')
                        ? 'bg-red-50 text-red-600 font-semibold'
                        : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">person</span>
                    <span>My Profile</span>
                </a>

            </nav>

            <div class="mt-auto p-4 border-t">
                <a href="{{ route('logout') }}" class="block w-full text-sm text-gray-500 hover:text-red-500">
                    Sign Out
                </a>
            </div>

        </aside>

        <!-- CONTENT -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>

</body>

</html>