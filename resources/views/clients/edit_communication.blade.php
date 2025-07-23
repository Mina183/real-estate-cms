<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ✏️ Edit Communication Note
        </h2>
        <div class="mt-2">
            <a href="{{ route('clients.index', ['tab' => 'contact']) }}"
               class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Client List
            </a>
        </div>
    </x-slot>

    <div class="py-10 px-6 max-w-3xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('clients.communications.update', [$client, $communication]) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Date</label>
                    <input type="date" name="date" value="{{ old('date', $communication->date) }}"
                           required class="w-full border-gray-300 rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Action</label>
                    <input type="text" name="action" value="{{ old('action', $communication->action) }}"
                           class="w-full border-gray-300 rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Update</label>
                    <textarea name="update" rows="3"
                              class="w-full border-gray-300 rounded px-3 py-2">{{ old('update', $communication->update) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Feedback</label>
                    <textarea name="feedback" rows="3"
                              class="w-full border-gray-300 rounded px-3 py-2">{{ old('feedback', $communication->feedback) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Outcome</label>
                    <textarea name="outcome" rows="3"
                              class="w-full border-gray-300 rounded px-3 py-2">{{ old('outcome', $communication->outcome) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-[#0e2442] text-white px-5 py-2 rounded hover:bg-opacity-90 font-semibold">
                        💾 Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>