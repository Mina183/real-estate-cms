<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter PIN — Triton Investor Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">

            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Enter Your PIN
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Triton Real Estate Fund — Investor Portal
                </p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('investor.pin.check') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-1 text-center">
                        6-digit access PIN
                    </label>
                    <input id="pin" name="pin" type="password"
                           inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="current-password"
                           placeholder="••••••"
                           class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 text-center text-2xl tracking-widest placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-darker focus:border-brand-darker"
                           autofocus required>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-brand-darker hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-darker transition">
                    Continue
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-4">
                Forgot your PIN? Contact your relationship manager to have it reset.
            </p>

            <div class="text-center">
                <form method="POST" action="{{ route('investor.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 underline">
                        Log out
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>
