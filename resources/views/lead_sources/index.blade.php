<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            {{ __('Your Lead Sources') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        {{-- Add New Lead Source --}}
        <div class="bg-white p-6 shadow-md rounded mb-6">
            <form action="{{ route('lead-sources.store') }}" method="POST">
                @csrf
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">Lead Source Name</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                    @error('name')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mt-4">
                    <x-primary-button>{{ __('Add Lead Source') }}</x-primary-button>
                </div>
            </form>
        </div>

        {{-- List of Lead Sources --}}
        <div class="bg-white p-6 shadow-md rounded">
            <h3 class="text-lg font-semibold mb-4">Existing Lead Sources</h3>
            <div class="max-h-[300px] overflow-auto pr-2">
                <ul class="space-y-2">
                    @forelse ($leadSources as $source)
                        <li class="border-b pb-2">{{ $source->name }}</li>
                    @empty
                        <li class="text-gray-500">You have not added any lead sources yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <a href="{{ route('dashboard') }}"
        class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold px-4 py-2 rounded mb-4 mt-3 transition">
            ← Back to Dashboard
        </a>
    </div>
</x-app-layout>
