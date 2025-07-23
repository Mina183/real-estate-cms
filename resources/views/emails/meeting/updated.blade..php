<x-mail::message>
    <x-slot name="header">
    <div style="text-align: center;">
        <h1 style="color: #0e2442; font-size: 24px; margin: 0;">Poseidon Real Estate</h1>
    </div>
    </x-slot>
# Hello {{ $partner->name }},

The following meeting has been updated:

<x-mail::panel>
**Title:** {{ $meeting->title }}  
@if($meeting->description)
**Description:** {{ $meeting->description }}  
@endif
**New Start Time:** {{ \Carbon\Carbon::parse($meeting->start_time)->format('F j, Y g:i A') }}
</x-mail::panel>

<x-mail::button :url="route('calendar.index')">
View Updated Meeting
</x-mail::button>

Thanks,  
{{ config('app.name') }}
</x-mail::message>