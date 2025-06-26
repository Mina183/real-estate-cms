<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Real Estate Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#e4e2d7] text-[#0e2442] min-h-screen flex">

    <!-- Left column: content -->
    <div class="w-1/2 flex flex-col px-16 py-12 relative bg-[#e4e2d7]">
        <!-- Top-left nav -->
        <div class="absolute top-6 left-8">
            <a href="/login" class="text-lg font-medium hover:underline">Log in</a>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col justify-center">
            <h1 class="text-3xl font-bold mb-6">Your Clients. Your Deals. Fully Secure.</h1>

            <p class="mb-4 max-w-md">
                Real estate agents get full control over their portfolio. Work in a private workspace with no overlap between agents.
            </p>

            <p class="mb-6 max-w-md">
                Track client registrations, deals, and history — all securely stored and access-controlled.
            </p>

            <ul class="list-disc list-inside space-y-2 mb-8">
                <li>Exclusive access to your own clients only</li>
                <li>Passport validation to prevent duplicates</li>
                <li>Role-based, secure user access</li>
            </ul>

            <a href="/my-clients" class="bg-[#0e2442] text-white px-5 py-2 rounded hover:opacity-90 transition w-fit animate-pulse">
                View My Clients
            </a>
        </div>
    </div>

    <!-- Right column: logo -->
    <div class="w-1/2 bg-[#0e2442] text-white relative flex items-center justify-center">
        <!-- Top-right nav -->
        <div class="absolute top-6 right-8">
            <a href="/register" class="text-lg font-medium hover:underline border border-white px-3 py-1 rounded">Register</a>
        </div>

        <!-- Logo center -->
        <img id="logo" src="/images/logo.png" alt="Poseidon Real Estate Logo" class="opacity-0 transition-opacity duration-[3000ms] ease-in-out w-55 h-auto" />
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