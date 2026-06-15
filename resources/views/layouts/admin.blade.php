<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SelangorCareConnect+ | Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

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

    <!-- HEADER -->
    <header
        class="fixed top-0 left-0 w-full z-50 h-16 flex justify-between items-center px-8 bg-white border-b border-gray-100 shadow-sm">

        <div class="flex items-center gap-3">
            <img src="{{ asset('images/selangorcareconnect.png') }}" alt="SelangorCareConnect Logo"
                class="h-12 object-contain">

            <div>
                <h2 class="font-bold text-lg">SelangorCareConnect+</h2>
                <p class="text-xs text-gray-500">Administrator Console</p>
            </div>
        </div>

        <div class="text-sm text-gray-600">
            {{ session('user.name') }}
            <span class="ml-2 px-2 py-0.5 text-xs rounded bg-red-50 text-red-600 font-semibold">Administrator</span>
        </div>

    </header>

    <div class="flex h-[calc(100vh-64px)] mt-16">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-white border-r hidden lg:flex flex-col">

            <nav class="p-4 space-y-2 text-sm">

                <a href="/admin/dashboard" class="block px-3 py-2 rounded
                {{ request()->is('admin/dashboard') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Dashboard
                </a>

                <a href="/admin/users" class="block px-3 py-2 rounded
                {{ request()->is('admin/users*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Manage Users
                </a>

                <a href="/admin/organizations" class="block px-3 py-2 rounded
                {{ request()->is('admin/organizations*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Verify Organizations
                </a>

                <a href="/admin/notifications" class="block px-3 py-2 rounded
                {{ request()->is('admin/notifications*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Send Notification
                </a>

                <a href="/admin/reports" class="block px-3 py-2 rounded
                {{ request()->is('admin/reports*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Reports
                </a>

                <a href="/admin/logs" class="block px-3 py-2 rounded
                {{ request()->is('admin/logs*') ? 'bg-red-50 text-red-500 font-semibold' : 'hover:bg-red-50' }}">
                    Activity Logs
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

            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-50 text-green-700 text-sm border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</body>

</html>
