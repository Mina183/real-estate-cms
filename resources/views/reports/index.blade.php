<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Placement Agent Report --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Investors by Placement Agent</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                A list of all investors introduced through a placement agent, including the agent's name and email.
                                Opens directly in Excel.
                            </p>
                        </div>
                        <a href="{{ route('reports.placement-agents-export') }}"
                           class="ml-4 flex-shrink-0 bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-5 rounded inline-flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Download Excel (.csv)</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
