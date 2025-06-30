<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
        <div class="text-sm text-gray-500">
            Logged in as: {{ auth()->user()->role }}
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 text-gray-800 dark:text-gray-100">

                @if(auth()->user()->role === 'channel_partner')
                    <h3 class="text-lg font-semibold mb-4">Channel Partner Dashboard</h3>

                    <div class="space-y-4">
                        <a href="{{ route('lead-sources.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Register / Review Your Lead Sources
                        </a>
                        <br>
                        <br>
                        <a href="{{ route('clients.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Register / Review / Edit Your Clients
                        </a>
                    </div>
                    @elseif(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                        <div class="mt-6 space-y-4">
                            <p class="text-lg font-semibold text-blue-400">
                                Admin dashboard placeholder
                            </p>
                            <ul class="list-disc list-inside text-black-200">
                                <li>Review all channel partners</li>
                                <li>Manage client access</li>
                                <li>Approve new users</li>
                            </ul>
                        </div>
                    @endif

            </div>
        </div>
    </div>
</x-app-layout>
