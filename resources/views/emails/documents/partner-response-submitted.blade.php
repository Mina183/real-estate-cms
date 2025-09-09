<x-mail::message>
    <x-slot name="header">
    <div style="text-align: center;">
        <h1 style="color: #0e2442; font-size: 24px; margin: 0;">Poseidon Real Estate</h1>
    </div>
    </x-slot>
# Partner Response Received

Hello {{ $admin->name }},

{{ $partner->name }} has submitted a response to the document "{{ $document->title }}".

**Response Details:**
- **Document:** {{ $document->title }}
- **Partner:** {{ $partner->name }}
- **Submitted:** {{ now()->format('F j, Y \a\t g:i A') }}
- **Status:** Waiting for admin approval

The response is now ready for your review and approval.

<x-mail::button :url="route('admin.documents.show', $document->id)">
Review Response
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>