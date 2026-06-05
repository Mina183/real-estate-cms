<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Access PIN — Triton Investor Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">

            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Set Your Access PIN
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Triton Real Estate Fund — Investor Portal
                </p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg px-5 py-4 text-sm text-blue-800">
                <p class="font-semibold mb-1">One-time setup</p>
                <p>Choose a 6-digit PIN to protect your portal access. You may share this PIN with authorised colleagues at your organisation who need access to this account.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('investor.pin.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-1">
                        Choose a 6-digit PIN
                    </label>
                    <input id="pin" name="pin" type="password"
                           inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="new-password"
                           placeholder="••••••"
                           class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 text-center text-2xl tracking-widest placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-darker focus:border-brand-darker"
                           autofocus required>
                    <p class="mt-1 text-xs text-gray-400">Numbers only, exactly 6 digits</p>
                </div>

                <div>
                    <label for="pin_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm PIN
                    </label>
                    <input id="pin_confirmation" name="pin_confirmation" type="password"
                           inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="new-password"
                           placeholder="••••••"
                           class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 text-center text-2xl tracking-widest placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-darker focus:border-brand-darker"
                           required>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-brand-darker hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-darker transition">
                    Set PIN &amp; Enter Portal
                </button>
            </form>

        </div>
    </div>
</body>
</html>
