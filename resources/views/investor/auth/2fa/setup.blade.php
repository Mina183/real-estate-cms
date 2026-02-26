<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Two-Factor Authentication - Triton</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <img src="/images/logo.png" alt="Logo" class="h-20 w-auto mx-auto">
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Set Up Two-Factor Authentication</h2>
                <p class="mt-2 text-sm text-gray-600">Scan the QR code with your authenticator app</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-center mb-6">
                    {!! QrCode::size(200)->generate($qrCodeUrl) !!}
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Manual entry key:</p>
                    <code class="text-sm font-mono text-gray-800 break-all">{{ $secret }}</code>
                </div>

                <form method="POST" action="{{ route('investor.2fa.enable') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Enter the 6-digit code to confirm setup:
                        </label>
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
                        Enable Two-Factor Authentication
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>