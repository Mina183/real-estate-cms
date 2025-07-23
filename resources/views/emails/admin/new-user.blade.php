<x-mail::message>
    <x-slot name="header">
    <div style="text-align: center;">
        <h1 style="color: #0e2442; font-size: 24px; margin: 0;">Poseidon Real Estate</h1>
    </div>
    </x-slot>
# New User Registration Requires Approval

A new user has registered and is waiting for approval.

**Name:** {{ $user->name }}  
**Email:** {{ $user->email }}<br>
**Requested Role:** {{ ucfirst($user->requested_role ?? 'guest') }}

<x-mail::button :url="url('/admin/users')">
Review and Approve Users
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
