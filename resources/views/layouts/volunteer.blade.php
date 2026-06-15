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

        <!-- Notification Bell -->
        <div class="flex items-center gap-4">

            <a href="#" class="relative">

                <span class="material-symbols-outlined text-3xl text-gray-600 hover:text-red-500">
                    notifications
                </span>

                <span
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center">
                    0
                </span>

            </a>

        </div>

    </header>

    <div class="flex h-[calc(100vh-64px)] mt-16">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-white border-r hidden lg:flex flex-col">


            <nav class="p-4 space-y-2 text-sm">

                <nav class="p-4 space-y-2 text-sm">

                    <a href="{{ route('volunteer.dashboard') }}" class="block px-3 py-2 rounded
        {{ request()->is('volunteer/dashboard') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('volunteer.applications') }}" class="block px-3 py-2 rounded
        {{ request()->is('volunteer/applications*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                        My Applications
                    </a>
                    <a href="{{ route('volunteer.assignments') }}" class="block px-3 py-2 rounded
   {{ request()->is('volunteer/assignments*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">

                        My Assignments

                    </a>

                    <a href="{{ route('volunteer.history') }}" class="block px-3 py-2 rounded
        {{ request()->is('volunteer/history*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                        History
                    </a>

                    <a href="{{ route('volunteer.attendance') }}" class="block px-3 py-2 rounded
        {{ request()->is('volunteer/attendance*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                        Attendance
                    </a>

                    <a href="{{ url('/profile') }}" class="block px-3 py-2 rounded
{{ request()->is('profile*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                        My Profile
                    </a>

                </nav>

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