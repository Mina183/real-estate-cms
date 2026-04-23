<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Room Viewers</h2>
        <p class="text-sm text-gray-500 mt-1">External stakeholders with read-only access to the data room</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Login URL info --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-800">Login URL to share with viewers</p>
                    <p class="text-sm text-blue-700 font-mono mt-1">{{ url('/investor/login') }}</p>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ url('/investor/login') }}').then(()=>this.textContent='Copied!')"
                        class="text-xs font-semibold text-blue-700 bg-blue-100 hover:bg-blue-200 px-3 py-2 rounded-lg transition whitespace-nowrap">
                    Copy Link
                </button>
            </div>

            {{-- Create form --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Create New Viewer Account</h3>
                </div>
                <form action="{{ route('data-room-viewers.store') }}" method="POST" class="px-6 py-5">
                    @csrf
                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g. John Smith"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="director@example.com"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required
                                   placeholder="Min. 8 characters"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <button type="submit"
                                class="px-5 py-2 text-sm font-semibold text-white bg-brand-darker rounded-lg hover:opacity-90 transition shadow-sm">
                            Create Viewer Account
                        </button>
                        <p class="text-xs text-gray-400">Access: Folders 1–11 · Read-only · No admin access</p>
                    </div>
                </form>
            </div>

            {{-- Existing viewers --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Existing Viewer Accounts</h3>
                </div>

                @if($viewers->isEmpty())
                    <div class="px-6 py-8 text-center text-sm text-gray-400">
                        No viewer accounts yet. Create one above.
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Login</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($viewers as $viewer)
                                <tr class="{{ $viewer->is_active ? '' : 'opacity-50' }}">
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $viewer->investor->legal_entity_name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $viewer->email }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $viewer->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $viewer->last_login_at ? $viewer->last_login_at->format('d M Y, H:i') : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($viewer->is_active)
                                            <span class="px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Active</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-500 rounded-full">Deactivated</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($viewer->is_active)
                                            <form action="{{ route('data-room-viewers.destroy', $viewer->id) }}" method="POST"
                                                  onsubmit="return confirm('Deactivate this account?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-xs text-red-600 hover:text-red-800 font-medium">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
