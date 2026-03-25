<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Email Body Templates</h2>
            <a href="{{ route('email-body-templates.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + New Template
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            Available placeholder: <code class="bg-blue-100 px-1 rounded">@{{ investor_name }}</code> — replaced with the investor's organisation name when the email is sent.
                        </p>
                    </div>

                    @if($templates->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject Suggestion</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($templates as $template)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $template->name }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $template->subject_suggestion ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('email-body-templates.edit', $template) }}"
                                                   class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('email-body-templates.destroy', $template) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-3 rounded"
                                                            onclick="return confirm('Delete this template?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No templates yet. Create your first template.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>