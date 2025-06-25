@extends('layouts.app')

@section('content')
<div class="container" style="margin: 30px;">
    <h1 class="text-xl font-bold mb-4">Pending User Approvals</h1>

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
                        <button class="bg-blue-500 text-white px-2 py-1 rounded" style="border: 1px solid; background-color: blue;">Approve</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <p>No users pending approval.</p>
    @endif
</div>
@endsection