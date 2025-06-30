<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Clients
        </h2>
    </x-slot>

    <div class="p-6">
        <div x-data="{ tab: 'basic' }">
            <div class="mb-4">
                <button @click="tab = 'basic'" class="px-4 py-2 bg-blue-600 text-white rounded">Basic</button>
                <button @click="tab = 'finance'" class="px-4 py-2 bg-blue-600 text-white rounded ml-2">Finance</button>
                <button @click="tab = 'contact'" class="px-4 py-2 bg-blue-600 text-white rounded ml-2">Communication</button>
            </div>

            <div x-show="tab === 'basic'">
                <h3 class="font-semibold text-lg mb-2">Basic Information</h3>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">DOB</th>
                            <th class="border px-4 py-2">Passport</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td class="border px-4 py-2">{{ $client->full_name }}</td>
                                <td class="border px-4 py-2">{{ $client->dob }}</td>
                                <td class="border px-4 py-2">{{ $client->passport_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div x-show="tab === 'finance'" x-cloak>
                <h3 class="font-semibold text-lg mb-2">Finance Information</h3>
                {{-- Add finance-related table here --}}
            </div>

            <div x-show="tab === 'contact'" x-cloak>
                <h3 class="font-semibold text-lg mb-2">Communication</h3>
                {{-- Add communication-related table here --}}
            </div>
        </div>
    </div>
</x-app-layout>