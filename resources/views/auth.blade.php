<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SelangorCareConnect+</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#ec1313",
                        yellowSel: "#ffcc00",
                        darkbg: "#121212"
                    }
                }
            }
        }
    </script>
    <style>
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none !important;
        }
    </style>
</head>

<body class="h-screen overflow-hidden bg-gray-100 font-sans">

    <!-- HEADER -->

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

        <!-- LEFT IMAGE SECTION -->

        <div class="w-1/2 h-full relative overflow-hidden">

            <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433"
                class="absolute inset-0 w-full h-full object-cover">

            <div class="absolute inset-0 bg-gradient-to-t from-black via-red-900/70 to-transparent"></div>

            <div class="relative z-10 text-white p-14 flex flex-col justify-end h-full pb-20">

                <span class="bg-yellowSel text-black px-3 py-1 text-xs font-bold w-fit rounded mb-4">
                    OFFICIAL INITIATIVE
                </span>

                <h1 class="text-5xl font-extrabold leading-tight">
                    Empowering <span class="text-yellowSel">Selangor</span> Communities
                </h1>

                <p class="mt-4 text-white/90 max-w-md">
                    Join the premier platform connecting volunteers, donors, and organizers.
                    Together we build a stronger, more resilient state.
                </p>

                <div class="flex gap-10 mt-10 border-t border-white/30 pt-6">
                    <div>
                        <p class="text-3xl font-bold text-yellowSel">15k+</p>
                        <p class="text-sm">Active Volunteers</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-yellowSel">RM 2.4M</p>
                        <p class="text-sm">Donations Raised</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-yellowSel">500+</p>
                        <p class="text-sm">NGOs Registered</p>
                    </div>
                </div>

            </div>

        </div>

        <!-- RIGHT FORM SECTION -->
        <div class="flex-1 h-full flex items-center justify-center bg-white p-10">

            <div class="w-full max-w-lg">
                @if(session('success'))

                    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-300">

                        {{ session('success') }}

                    </div>

                @endif

                <h2 class="text-3xl font-bold mb-2">Get Started</h2>
                <p class="text-gray-500 mb-6">Create an account to begin your journey of giving back.</p>


                <!-- TAB -->
                <div class="flex bg-gray-100 rounded-lg p-1 mb-6">
                    <button id="tabLogin" class="flex-1 py-2 rounded-lg font-medium text-gray-500">
                        Sign In
                    </button>

                    <button id="tabRegister" class="flex-1 py-2 rounded-lg bg-white shadow font-medium">
                        Create Account
                    </button>
                </div>

                <!-- LOGIN FORM -->
                <div id="loginForm" class="hidden">

                    <h2 class="text-3xl font-bold mb-2">Sign in</h2>
                    <p class="text-gray-500 mb-6">
                        Access your dashboard to manage volunteering, donations, and activities across Selangor.
                    </p>

                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                        @csrf

                        <!-- EMAIL -->
                        <div>
                            <label class="text-sm font-medium">Email</label>
                            <input name="email" type="email" placeholder="Enter your email"
                                class="w-full border p-3 rounded-lg mt-1">
                        </div>

                        <!-- PASSWORD -->
                        <div>
                            <div class="flex justify-between text-sm">
                                <label>Password</label>
                                <a href="#" class="text-red-500 font-semibold">Forgot Password?</a>
                            </div>

                            <input name="password" type="password" placeholder="Enter your password"
                                class="w-full border p-3 rounded-lg mt-1">
                        </div>

                        <!-- BUTTON -->
                        <button type="submit" class="w-full bg-primary text-white p-3 rounded-lg font-bold mt-2">
                            Sign In →
                        </button>

                        <!-- DIVIDER -->
                        <div class="flex items-center gap-3 my-6">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <p class="text-xs text-gray-400 uppercase">Or continue with</p>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>

                        <!-- SOCIAL -->
                        <div class="space-y-3">

                            <button type="button"
                                class="w-full flex items-center justify-center gap-2 border rounded-lg h-11 bg-white">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5">
                                Continue with Google
                            </button>

                            <button type="button"
                                class="w-full flex items-center justify-center gap-2 border rounded-lg h-11 bg-white">
                                X Continue with X
                            </button>

                        </div>

                        <!-- REGISTER LINK -->
                        <p class="text-sm text-center mt-6">
                            Don't have an account?
                            <a href="#" id="goRegister" class="text-red-500 font-semibold">Register</a>
                        </p>

                    </form>

                </div>

                <!-- REGISTER FORM -->
                <div id="registerForm">
                    <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                        @csrf


                        <!-- REGISTER FORM -->
                        <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label class="font-semibold">I am joining as:</label>

                                <div class="grid grid-cols-3 gap-4 mt-3">

                                    <!-- VOLUNTEER -->
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="role" value="volunteer" checked class="peer hidden">

                                        <div class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-transparent 
bg-gray-100 hover:bg-gray-200 
peer-checked:border-red-500 peer-checked:bg-red-50 
transition-all h-28">

                                            <div
                                                class="w-10 h-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-2">
                                                ❤️
                                            </div>

                                            <span class="text-sm font-semibold">Volunteer</span>
                                        </div>

                                        <!-- CHECK ICON -->
                                        <div
                                            class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-red-500">
                                            ✔
                                        </div>

                                    </label>

                                    <!-- DONOR -->
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="role" value="donor" class="peer hidden">

                                        <div class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-transparent 
bg-gray-100 hover:bg-gray-200 
peer-checked:border-red-500 peer-checked:bg-red-50 
transition-all h-28">

                                            <div
                                                class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-500 flex items-center justify-center mb-2">
                                                💰
                                            </div>

                                            <span class="text-sm font-semibold">Donor</span>
                                        </div>

                                        <div
                                            class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-red-500">
                                            ✔
                                        </div>

                                    </label>

                                    <!-- ORGANIZER -->
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="role" value="organizer" class="peer hidden">

                                        <div class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-transparent 
bg-gray-100 hover:bg-gray-200 
peer-checked:border-red-500 peer-checked:bg-red-50 
transition-all h-28">

                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center mb-2">
                                                👥
                                            </div>

                                            <span class="text-sm font-semibold">Organizer</span>
                                        </div>

                                        <div
                                            class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-red-500">
                                            ✔
                                        </div>

                                    </label>

                                </div>
                            </div>

                            <!-- FULL NAME -->
                            <div>
                                <label class="text-sm font-medium">Full Name</label>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0" />
                                        </svg>
                                    </span>

                                    <input name="name" type="text" placeholder="e.g. Ahmad bin Ali"
                                        class="w-full pl-10 border p-3 rounded-lg">
                                </div>
                            </div>

                            <!-- EMAIL -->
                            <div>
                                <label class="text-sm font-medium">Email Address</label>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 7.5l9 6 9-6M4.5 19.5h15a1.5 1.5 0 001.5-1.5v-12A1.5 1.5 0 0019.5 4.5h-15A1.5 1.5 0 003 6v12A1.5 1.5 0 004.5 19.5z" />
                                        </svg>
                                    </span>

                                    <input name="email" type="email" placeholder="Ahmad@gmail.com"
                                        class="w-full pl-10 border p-3 rounded-lg">
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <!-- PASSWORD -->
                                <div>
                                    <label class="text-sm font-medium">Password</label>

                                    <div class="relative mt-1">

                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.5 10.5V7.875a4.5 4.5 0 10-9 0V10.5M6.75 10.5h10.5a1.5 1.5 0 011.5 1.5v6a1.5 1.5 0 01-1.5 1.5H6.75A1.5 1.5 0 015.25 18v-6a1.5 1.5 0 011.5-1.5z" />
                                            </svg>
                                        </span>

                                        <input id="password" name="password" type="password" placeholder="••••••••"
                                            class="w-full pl-10 pr-10 border p-3 rounded-lg">

                                        <!-- EYE ICON -->
                                        <span id="togglePassword"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400">

                                            <!-- EYE OPEN -->
                                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                    c4.478 0 8.268 2.943 9.542 7
                    -1.274 4.057-5.064 7-9.542 7
                    -4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>

                                            <!-- EYE CLOSED -->
                                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19
                    c-4.478 0-8.268-2.943-9.542-7
                    a9.956 9.956 0 012.042-3.368M6.223 6.223A9.956 9.956 0 0112 5
                    c4.478 0 8.268 2.943 9.542 7
                    a9.956 9.956 0 01-4.043 5.06" />
                                                <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor"
                                                    stroke-width="2" />
                                            </svg>

                                        </span>
                                    </div>

                                    <!-- PASSWORD REQUIREMENTS (stay under password only) -->
                                    <div class="text-xs mt-2 space-y-1">
                                        <p id="length" class="text-gray-400">• Minimum 8 characters</p>
                                        <p id="uppercase" class="text-gray-400">• At least 1 uppercase letter</p>
                                        <p id="number" class="text-gray-400">• At least 1 number</p>
                                    </div>
                                </div>


                                <!-- CONFIRM PASSWORD -->
                                <div>
                                    <label class="text-sm font-medium">Confirm Password</label>

                                    <div class="relative mt-1">

                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12l2 2 4-4" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                                            </svg>
                                        </span>

                                        <input id="confirmPassword" name="password_confirmation" type="password"
                                            placeholder="••••••••" class="w-full pl-10 pr-10 border p-3 rounded-lg">

                                        <!-- EYE ICON -->
                                        <span id="toggleConfirmPassword"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400">

                                            <!-- EYE OPEN -->
                                            <svg id="eyeOpenConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                    c4.478 0 8.268 2.943 9.542 7
                    -1.274 4.057-5.064 7-9.542 7
                    -4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>

                                            <!-- EYE CLOSED -->
                                            <svg id="eyeClosedConfirm" xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                    c4.478 0 8.268 2.943 9.542 7
                    -1.274 4.057-5.064 7-9.542 7
                    -4.477 0-8.268-2.943-9.542-7z" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor"
                                                    stroke-width="2" />

                                            </svg>

                                        </span>
                                    </div>
                                </div>

                            </div>

                            <label class="text-sm text-gray-500">
                                <input type="checkbox" name="terms"> I agree to Terms of Service & Privacy Policy
                            </label>

                            <button type="submit"
                                class="w-full bg-primary text-white p-3 rounded-lg font-bold shadow-lg">
                                Create Account
                            </button>

                            <!-- DIVIDER -->
                            <div class="flex items-center gap-3 my-6">
                                <div class="flex-1 h-px bg-gray-200"></div>
                                <p class="text-xs text-gray-400 font-semibold uppercase">Or continue with</p>
                                <div class="flex-1 h-px bg-gray-200"></div>
                            </div>

                            <!-- SOCIAL BUTTONS -->
                            <div class="flex flex-col sm:flex-row gap-3">

                                <!-- GOOGLE -->
                                <button type="button"
                                    class="flex-1 flex items-center justify-center gap-2 border border-gray-100 rounded-lg h-11 bg-white hover:bg-gray-50 transition">

                                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">

                                    <span class="text-sm font-medium">Continue with Google</span>
                                </button>

                                <!-- X -->
                                <button type="button"
                                    class="flex-1 flex items-center justify-center gap-2 border border-gray-00 rounded-lg h-11 bg-white hover:bg-gray-50 transition">

                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z" />
                                    </svg>

                                    <span class="text-sm font-medium">Continue with X</span>
                                </button>

                            </div>

                            <!-- FOOTER TEXT -->
                            <p class="text-xs text-center text-gray-400 mt-6">
                                © 2026 PSM 2 Project. All rights reserved and subject to the Terms of Service and
                                Privacy Policy.<br>
                            </p>

                        </form>

                </div>

            </div>

        </div>

        <script>
            const tabLogin = document.getElementById('tabLogin');
            const tabRegister = document.getElementById('tabRegister');

            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            tabLogin.addEventListener('click', () => {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');

                tabLogin.classList.add('bg-white', 'shadow', 'text-black');
                tabRegister.classList.remove('bg-white', 'shadow', 'text-black');
            });

            tabRegister.addEventListener('click', () => {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');

                tabRegister.classList.add('bg-white', 'shadow', 'text-black');
                tabLogin.classList.remove('bg-white', 'shadow', 'text-black');
            });

        </script>
        <script>
            // TOGGLE PASSWORD VISIBILITY
            const togglePassword = document.getElementById('togglePassword');
            const passwordInputField = document.getElementById('password');

            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            togglePassword.addEventListener('click', function () {
                if (passwordInputField.type === 'password') {
                    passwordInputField.type = 'text';

                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    passwordInputField.type = 'password';

                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            });



            // TOGGLE CONFIRM PASSWORD VISIBILITY
            const toggleConfirm = document.getElementById('toggleConfirmPassword');
            const confirmField = document.getElementById('confirmPassword');

            const eyeOpenConfirm = document.getElementById('eyeOpenConfirm');
            const eyeClosedConfirm = document.getElementById('eyeClosedConfirm');

            toggleConfirm.addEventListener('click', function () {
                if (confirmField.type === 'password') {
                    confirmField.type = 'text';

                    eyeOpenConfirm.classList.add('hidden');
                    eyeClosedConfirm.classList.remove('hidden');
                } else {
                    confirmField.type = 'password';

                    eyeOpenConfirm.classList.remove('hidden');
                    eyeClosedConfirm.classList.add('hidden');
                }
            });

            // PASSWORD VALIDATION
            const passwordInput = document.getElementById('password');

            passwordInput.addEventListener('input', function () {
                const value = passwordInput.value;

                // RULES
                const hasLength = value.length >= 8;
                const hasUpper = /[A-Z]/.test(value);
                const hasNumber = /[0-9]/.test(value);

                // UPDATE UI
                document.getElementById('length').className = hasLength ? 'text-green-500' : 'text-gray-400';
                document.getElementById('uppercase').className = hasUpper ? 'text-green-500' : 'text-gray-400';
                document.getElementById('number').className = hasNumber ? 'text-green-500' : 'text-gray-400';
            });

        </script>
        @if(session('showLogin'))

            <script>

                document.addEventListener(
                    'DOMContentLoaded',
                    function () {

                        document
                            .getElementById('tabLogin')
                            .click();

                    }
                );

            </script>

        @endif
</body>

</html>