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

    </header>

    <div class="flex h-[calc(100vh-64px)] mt-16">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-white border-r hidden lg:flex flex-col">


            <nav class="p-4 space-y-2 text-sm">

                <a href="/dashboard"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('dashboard') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <a href="/campaign"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('campaign*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">campaign</span>
                    <span>My Campaign</span>
                </a>


                <a href="/volunteers"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('volunteers*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">groups</span>
                    <span>Volunteer Management</span>
                </a>

                <a href="/donation"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('donation*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">payments</span>
                    <span>Donation</span>
                </a>

                <a href="/communication"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('communication*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <span class="material-symbols-outlined text-xl">forum</span>
                    <span>Communication</span>
                </a>

                <a href="/profile"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                    {{ request()->is('profile*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
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