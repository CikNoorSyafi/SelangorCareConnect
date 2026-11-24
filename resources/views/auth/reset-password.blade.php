<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SelangorCareConnect+</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#ec1313', yellowSel: '#ffcc00' } } }
        };
    </script>
</head>
<body class="min-h-screen bg-gray-100 font-sans">
    <header class="h-16 flex items-center px-6 md:px-8 bg-white border-b border-gray-100 shadow-sm">
        <a href="{{ route('auth') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/selangorcareconnect.png') }}" alt="SelangorCareConnect Logo" class="h-12 object-contain">
            <span class="font-bold text-lg text-gray-900">SelangorCareConnect+</span>
        </a>
    </header>

    <main class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-10">
        <section class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 md:p-10">
            <div class="w-14 h-14 rounded-full bg-red-50 text-primary flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm10-11V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900">Create a new password</h1>
            <p class="text-gray-500 mt-2 mb-6">Choose a new password for your SelangorCareConnect+ account.</p>

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        placeholder="At least 8 characters"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-gray-500">Use at least 8 characters, including one uppercase letter and one number.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        placeholder="Re-enter your new password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100">
                </div>

                <button type="submit" class="w-full rounded-lg bg-primary px-4 py-3 font-bold text-white hover:bg-red-700 transition">
                    Reset Password
                </button>
            </form>

            <a href="{{ route('auth') }}" class="mt-6 flex items-center justify-center gap-2 text-sm font-semibold text-gray-600 hover:text-primary">
                <span aria-hidden="true">←</span> Back to Sign In
            </a>
        </section>
    </main>
</body>
</html>
