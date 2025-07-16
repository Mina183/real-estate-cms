<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Real Estate Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#e4e2d7] text-[#0e2442] min-h-screen flex flex-col lg:flex-row">

    <!-- Left column: content -->
    <div class="w-full md:w-1/2 flex flex-col px-6 sm:px-10 md:px-16 lg:px-24 xl:px-32 py-12 bg-[#e4e2d7] relative items-center md:items-start text-center md:text-left">
        
    <!-- Top-left nav (only visible on md+ for better mobile centering) -->
    <div class="absolute top-6 left-6 md:block hidden">
        <a href="/login" class="text-lg md:text-xl font-semibold border-2 border-[#0e2442] px-4 py-1 rounded hover:bg-[#0e2442] hover:text-white transition">
            Log in
        </a>
    </div>

    <!-- Mobile nav at top center -->
    <div class="block md:hidden mb-4">
        <a href="/login" class="text-lg md:text-xl font-semibold border-2 border-[#0e2442] px-4 py-1 rounded hover:bg-[#0e2442] hover:text-white transition">
            Log in
        </a>
    </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col justify-center items-center md:items-start max-w-[90%] sm:max-w-[80%] md:max-w-[640px]">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                Your Clients. Your Deals. Fully Secure.
            </h1>

            <p class="mb-4 text-sm sm:text-base md:text-lg max-w-prose">
                Track client registrations, deals, and history — all securely stored and access-controlled.
            </p>

            <ul class="list-disc list-inside space-y-2 mb-8 text-sm sm:text-base md:text-lg text-left">
                <li>Exclusive access to your own clients only</li>
                <li>Passport validation to prevent duplicates</li>
                <li>Role-based, secure user access</li>
            </ul>

            @auth
                @php
                    $role = auth()->user()->role;
                @endphp

                @if($role === 'channel_partner' || $role === 'sub_partner')
                    <a href="{{ route('clients.index') }}" class="bg-[#0e2442] text-white px-5 py-2 rounded hover:opacity-90 transition w-fit animate-pulse">
                        View My Clients
                    </a>
                @elseif($role === 'admin' || $role === 'superadmin')
                    <a href="{{ route('admin.clients.index') }}" class="bg-[#0e2442] text-white px-5 py-2 rounded hover:opacity-90 transition w-fit">
                        View Partners and Clients
                    </a>
                @endif
            @endauth
        </div>
    </div>

<!-- Right column: logo -->
<div class="w-full lg:w-1/2 bg-[#0e2442] text-white relative flex flex-col items-center justify-center py-12 lg:py-0">

    <!-- Top-right nav for large screens only -->
    <div class="hidden lg:block absolute top-6 right-8">
        <a href="/register" class="text-lg font-medium hover:underline border border-white px-3 py-1 rounded">
            Register
        </a>
    </div>

    <!-- Mobile register button -->
    <div class="lg:hidden mb-6">
        <a href="/register" class="text-lg font-medium hover:underline border border-white px-3 py-1 rounded">
            Register
        </a>
    </div>

    <!-- Logo center -->
    <img id="logo" src="/images/logo.png" alt="Poseidon Real Estate Logo"
         class="opacity-0 transition-opacity duration-[3000ms] ease-in-out w-40 sm:w-52 md:w-72 lg:w-[22rem] xl:w-[26rem] h-auto" />
</div>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const logo = document.getElementById('logo');
            if (logo) {
                logo.classList.add('opacity-100');
            }
        });
    </script>

</body>

<style>
</style>

</html>