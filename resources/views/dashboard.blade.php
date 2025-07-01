<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Logged in as: {{ auth()->user()->role }}
        </div>
    </x-slot>
<div class="min-h-screen bg-gray-100 ">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex min-h-screen">
        @if(auth()->user()->role === 'channel_partner')
            <!-- Sidebar -->
            <aside class="w-72 bg-[#0e2442] text-gray-100 py-8 px-4 space-y-4 rounded-r-lg shadow-md">
                <h3 class="text-lg font-bold mb-4">Navigation</h3>
                <nav class="flex flex-col gap-2">
                    <a href="{{ route('lead-sources.index') }}"
                       class="block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                        Register / Review Lead Sources
                    </a>
                    <a href="{{ route('clients.index') }}"
                       class="block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                        Register / Review / Edit Clients
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 py-6 px-8 bg-gray-100">
                <div class="max-w-5xl mx-auto">
                    <h3 class="text-2xl font-bold text-[#0e2442] mb-4">Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="text-gray-700">
                        Use the sidebar to access and manage your lead sources and client data.
                    </p>
                </div>
            </main>
        @elseif(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
            <div class="py-6 px-8 flex-1 bg-gray-100">
                <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
                    <p class="text-lg font-semibold text-blue-500 mb-2">
                        Admin dashboard placeholder
                    </p>
                    <ul class="list-disc list-inside text-gray-800 dark:text-gray-300">
                        <li>Review all channel partners</li>
                        <li>Manage client access</li>
                        <li>Approve new users</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
