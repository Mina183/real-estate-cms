<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview — {{ $emailDraft->subject }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f3f4f6; font-family: Arial, sans-serif; }

        /* Control bar */
        .toolbar {
            background: #1e293b;
            color: #e2e8f0;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .toolbar-left { font-size: 13px; }
        .toolbar-left strong { color: #f8fafc; }
        .toolbar-left .meta { color: #94a3b8; font-size: 12px; margin-top: 2px; }
        .toolbar-right { display: flex; gap: 8px; flex-shrink: 0; }
        .btn {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-gray   { background: #475569; color: #fff; }
        .btn-gray:hover   { background: #334155; }
        .btn-blue   { background: #3b82f6; color: #fff; }
        .btn-blue:hover   { background: #2563eb; }
        .btn-green  { background: #16a34a; color: #fff; }
        .btn-green:hover  { background: #15803d; }
        .btn-teal   { background: #0d9488; color: #fff; }
        .btn-teal:hover   { background: #0f766e; }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-draft    { background: #e5e7eb; color: #374151; }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }

        /* Email wrapper */
        .email-wrapper { padding: 24px; }

        /* Approve bar */
        .approve-bar {
            max-width: 640px;
            margin: 0 auto 16px;
            background: #fefce8;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }
    </style>
</head>
<body>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <div>
                <strong>{{ $investor->organization_name ?? $investor->legal_entity_name }}</strong>
                <span class="badge badge-{{ $emailDraft->status === 'pending_approval' ? 'pending' : $emailDraft->status }}" style="margin-left:8px;">
                    {{ ucfirst(str_replace('_', ' ', $emailDraft->status)) }}
                </span>
            </div>
            <div class="meta">Subject: {{ $subject }}</div>
        </div>
        <div class="toolbar-right">
            @can('update', $emailDraft)
                <a href="{{ route('email-drafts.edit', $emailDraft) }}" class="btn btn-blue">Edit</a>
            @endcan
            @if($emailDraft->status === 'approved' && $emailDraft->created_by_user_id === auth()->id())
                <form method="POST" action="{{ route('email-drafts.send', $emailDraft) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-teal"
                            onclick="return confirm('Send this email now?')">Send Email</button>
                </form>
            @endif
            <button onclick="window.close()" class="btn btn-gray">Close</button>
        </div>
    </div>

    <div class="email-wrapper">

        {{-- Approve prompt for admin --}}
        @can('approve', $emailDraft)
            @if($emailDraft->status === 'pending_approval')
            <div class="approve-bar">
                <span>This draft is awaiting your approval.</span>
                <form method="POST" action="{{ route('email-drafts.approve', $emailDraft) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-green"
                            onclick="return confirm('Approve this draft for sending?')">✓ Approve Draft</button>
                </form>
            </div>
            @endif
        @endcan

        {{-- Recipient info --}}
        <div style="max-width:640px; margin:0 auto 12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px 16px; font-size:13px; color:#374151;">
            <div><span style="color:#6b7280; min-width:32px; display:inline-block;">To:</span>
                @if($primaryContact)
                    <strong>{{ $primaryContact->full_name }}</strong> &lt;{{ $primaryContact->email }}&gt;
                @else
                    <span style="color:#ef4444;">No primary contact with email found</span>
                @endif
            </div>
            @if(!empty($emailDraft->cc_emails))
            <div style="margin-top:4px;"><span style="color:#6b7280; min-width:32px; display:inline-block;">CC:</span>
                {{ implode(', ', $emailDraft->cc_emails) }}
            </div>
            @endif
        </div>

        {{-- Rendered email (reuses the email template inline) --}}
        <div style="max-width:640px; margin:0 auto; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.1); border-radius:4px; overflow:hidden;">
            <div style="background:#1B3A5C; padding:24px 32px;">
                <span style="color:white;font-size:18px;font-weight:bold;">Triton Real Estate Fund</span>
            </div>
            <div style="padding:32px;">
                @if($onBehalf)
                <div style="font-size:13px;color:#666;margin-bottom:24px;border-bottom:1px solid #eee;padding-bottom:16px;">
                    This email is sent on behalf of <strong>{{ $onBehalf->name }}{{ $onBehalf->title ? ', ' . $onBehalf->title : '' }}</strong>
                </div>
                @endif

                <div style="font-size:15px;color:#333;white-space:pre-wrap;">{!! $body !!}</div>

                @if($signature)
                <div style="margin-top:32px;padding-top:24px;border-top:1px solid #eee;font-size:13px;color:#555;">
                    {!! $signature->signature_html !!}
                </div>
                @endif

                <div style="margin-top:32px;padding:16px;background:#f8f9fa;border:1px solid #e2e4e8;border-radius:4px;font-size:11px;color:#666;line-height:1.5;">
                    <strong style="display:block;margin-bottom:6px;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;color:#444;">Important Notice</strong>
                    This communication is confidential and intended solely for persons who qualify as Professional Clients
                    as defined by the Dubai Financial Services Authority (DFSA). It is not directed at, and must not be
                    relied upon by, Retail Clients. This document does not constitute an offer, invitation, inducement or
                    recommendation to subscribe for or purchase any interest in the Triton Real Estate Fund (CEIC) Limited
                    (the "Fund"). The DFSA has not approved or endorsed this document or the Fund. Any investment in the
                    Fund may only be made on the basis of the Private Placement Memorandum (PPM), the Fund's constitutional
                    documents and the Subscription Agreement. Investment in the Fund involves significant risks, including
                    the potential loss of capital, illiquidity and leverage. Past performance, targets or projections are
                    not reliable indicators of future results.
                </div>
            </div>
            <div style="background:#f8f8f8;padding:16px 32px;font-size:11px;color:#999;border-top:1px solid #eee;">
                This email and any attachments are confidential and intended solely for the named recipient.
                If you have received this email in error, please notify the sender immediately.
            </div>
        </div>

    </div>

</body>
</html>
