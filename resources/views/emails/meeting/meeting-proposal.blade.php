<x-mail::message>
    <x-slot name="header">
    <div style="text-align: center;">
        <h1 style="color: #0e2442; font-size: 24px; margin: 0;">Poseidon Real Estate</h1>
    </div>
    </x-slot>
# New Meeting Proposal

Hello {{ $admin->name }},

{{ $partner->name }} has submitted a new meeting proposal that requires your review and approval.

**Meeting Details:**
- **Title:** {{ $meeting->title }}
- **Proposed by:** {{ $partner->name }}
- **Start Time:** {{ $meeting->start_time->format('F j, Y \a\t g:i A') }}
@if($meeting->end_time)
- **End Time:** {{ $meeting->end_time->format('F j, Y \a\t g:i A') }}
@endif
@if($meeting->description)
- **Description:** {{ $meeting->description }}
@endif

**Invited Partners:**
@foreach($meeting->attendees as $attendee)
- {{ $attendee->name }}
@endforeach

<x-mail::button :url="route('admin.meeting.proposals')">
Review Proposals
</x-mail::button>

You can approve the proposal as-is or edit it before approval. Once approved, all invited partners will receive meeting invitations.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>