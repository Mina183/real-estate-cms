<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Two-Factor Authentication
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        Verify Your Identity
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Enter the 6-digit code from your authenticator app.
                    </p>
                </div>

                <form method="POST" action="{{ route('2fa.check') }}">
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
            </div>
        </div>
    </div>
</x-app-layout>