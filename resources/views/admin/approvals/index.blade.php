<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Pending User Approvals</h2>
    </x-slot>

    <div class="container mx-auto p-4">
        @if(session('success'))
            <div class="bg-green-100 p-2 mb-4">{{ session('success') }}</div>
        @endif

        @if($pendingUsers->count())
            <ul>
                @foreach($pendingUsers as $user)
                    <li class="mb-3 border-b pb-2">
                        <strong>{{ $user->name }}</strong> ({{ $user->email }})

                        <form method="POST" action="{{ route('approve_user', $user) }}" class="inline-block ml-4">
                            @csrf
                            @method('PATCH')
                            <button class="bg-blue-500 text-white px-2 py-1 rounded border border-blue-700">Approve</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No users pending approval.</p>
        @endif
    </div>
</x-app-layout>