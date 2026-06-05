<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Investors') }}
            </h2>
            <div class="flex space-x-2">
                @can('create', App\Models\Investor::class)
                    <a href="{{ route('investors.send-email.bulk') }}" 
                    class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                        ✉️ Bulk Send Email
                    </a>
                    <a href="{{ route('investors.create') }}" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        + New Investor
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-5">
                <form method="GET" action="{{ route('investors.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Name or jurisdiction…"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="min-w-36">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Stage</label>
                        <select name="stage" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">All stages</option>
                            @foreach(['prospect','eligibility_confirmed','portal_access_granted','subscription_signed','kyc_in_progress','kyc_completed','funded','monitored'] as $s)
                                <option value="{{ $s }}" {{ request('stage') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-36">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                        <select name="type" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">All types</option>
                            @foreach(['individual','corporate','family_office','spv','fund','bank'] as $t)
                                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-40">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Assigned To</label>
                        <select name="assigned_to" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Anyone</option>
                            @foreach($managers as $m)
                                <option value="{{ $m->id }}" {{ request('assigned_to') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700 transition">Filter</button>
                        @if(request()->hasAny(['search','stage','type','assigned_to']))
                            <a href="{{ route('investors.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-semibold rounded-md hover:bg-gray-200 transition">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if($investors->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Organization
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stage
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fund
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Assigned To
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($investors as $investor)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $investor->organization_name ?? $investor->legal_entity_name ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $investor->jurisdiction }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ ucfirst($investor->investor_type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($investor->stage === 'prospect') bg-gray-100 text-gray-800
                                                    @elseif($investor->stage === 'eligibility_confirmed') bg-yellow-100 text-yellow-800
                                                    @elseif($investor->stage === 'portal_access_granted') bg-blue-100 text-blue-800
                                                    @elseif($investor->stage === 'subscription_signed') bg-indigo-100 text-indigo-800
                                                    @elseif($investor->stage === 'kyc_in_progress') bg-purple-100 text-purple-800
                                                    @elseif($investor->stage === 'kyc_completed') bg-green-100 text-green-800
                                                    @elseif($investor->stage === 'funded') bg-teal-100 text-teal-800
                                                    @elseif($investor->stage === 'monitored') bg-orange-100 text-orange-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ str_replace('_', ' ', ucfirst($investor->stage)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($investor->status === 'pending') bg-gray-100 text-gray-800
                                                    @elseif($investor->status === 'in_review') bg-yellow-100 text-yellow-800
                                                    @elseif($investor->status === 'qualified') bg-green-100 text-green-800
                                                    @elseif($investor->status === 'action_required') bg-red-100 text-red-800
                                                    @elseif($investor->status === 'on_hold') bg-orange-100 text-orange-800
                                                    @elseif($investor->status === 'rejected') bg-red-100 text-red-800
                                                    @endif">
                                                    {{ str_replace('_', ' ', ucfirst($investor->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $investor->fund->fund_name ?? 'Not assigned' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $investor->assignedTo->name ?? 'Unassigned' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                {{-- Everyone who can see the list can view details --}}
                                                @can('view', $investor)
                                                    <a href="{{ route('investors.show', $investor) }}" 
                                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                                        View
                                                    </a>
                                                @endcan
                                                
                                                {{-- Only authorized users can edit --}}
                                                @can('update', $investor)
                                                    <a href="{{ route('investors.edit', $investor) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">
                                                        Edit
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $investors->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No investors</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new investor.</p>
                            
                            {{-- Only show create button if user has permission --}}
                            @can('create', App\Models\Investor::class)
                                <div class="mt-6">
                                    <a href="{{ route('investors.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        + New Investor
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>