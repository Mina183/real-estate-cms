<x-mail::message>
<x-mail::header :url="config('app.url')">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;">
</x-mail::header>
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
