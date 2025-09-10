<x-mail::message>
    <x-slot name="header">
    <div style="text-align: center;">
        <h1 style="color: #0e2442; font-size: 24px; margin: 0;">Poseidon Real Estate</h1>
    </div>
    </x-slot>
# New Document Assigned

Hello {{ $partner->name }},

A new document has been assigned to you by {{ $uploader->name }}.

**Document Details:**
- **Title:** {{ $document->title }}
- **Assigned by:** {{ $uploader->name }}
- **Upload Date:** {{ $document->created_at->format('F j, Y \a\t g:i A') }}
- **Status:** {{ ucfirst(str_replace('_', ' ', $document->status)) }}

@if($document->status === 'waiting_partner_action')
**Action Required:** This document requires your response. Please upload the requested files or information.
@elseif($document->status === 'review_only')
**For Review:** This document is for your review and acknowledgment only.
@endif

<x-mail::button :url="route('partner.documents.index')">
View Document
</x-mail::button>

Please log into your account to review this document and take any required action.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>