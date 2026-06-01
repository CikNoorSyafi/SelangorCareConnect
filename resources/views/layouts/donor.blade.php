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

                <a href="/donor/dashboard" class="block px-3 py-2 rounded
        {{ request()->is('donor/dashboard') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Dashboard
                </a>

                <a href="/donor/donation" class="block px-3 py-2 rounded
        {{ request()->is('donor/donation*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Make Donation
                </a>

                <a href="/donor/history" class="block px-3 py-2 rounded
        {{ request()->is('donor/history*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Donation History
                </a>

                <a href="/donor/receipts" class="block px-3 py-2 rounded
        {{ request()->is('donor/receipts*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Tax Receipts
                </a>

                <a href="/donor/profile" class="block px-3 py-2 rounded
        {{ request()->is('donor/profile*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    My Profile
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