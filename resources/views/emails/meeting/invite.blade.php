<x-mail::message>
<x-mail::header :url="config('app.url')">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;">
</x-mail::header>
# Hello {{ $partner->name }},

You have been invited to a team meeting.

<x-mail::panel>
**Title:** {{ $meeting->title }}  
@if($meeting->description)
**Description:** {{ $meeting->description }}  
@endif
**Start Time:** {{ \Carbon\Carbon::parse($meeting->start_time)->format('F j, Y g:i A') }}
</x-mail::panel>

<x-mail::button :url="route('calendar.index')">
View Meeting
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
