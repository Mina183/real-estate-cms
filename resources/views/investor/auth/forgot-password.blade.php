<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Triton</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <img src="/images/logo.png" alt="Logo" class="h-20 w-auto mx-auto">
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Forgot Password</h2>
                <p class="mt-2 text-sm text-gray-600">Enter your email and we'll send you a reset link.</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('investor.password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autofocus
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                        Send Reset Link
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