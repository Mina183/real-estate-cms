<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Triton</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <img src="/images/logo.png" alt="Logo" class="h-20 w-auto mx-auto">
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Two-Factor Authentication</h2>
                <p class="mt-2 text-sm text-gray-600">Enter the 6-digit code from your authenticator app</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('investor.2fa.check') }}">
                    @csrf
                    <div class="mb-4">
                        <input 
                            type="text" 
                            name="code" 
                            maxlength="6"
                            placeholder="000000"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autofocus
                        >
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                        Verify
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('investor.login') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>