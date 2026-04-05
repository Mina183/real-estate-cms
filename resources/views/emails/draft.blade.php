<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 640px; margin: 0 auto; background: white; }
        .header { background: #1B3A5C; padding: 24px 32px; }
        .header img { height: 40px; }
        .content { padding: 32px; }
        .on-behalf { font-size: 13px; color: #666; margin-bottom: 24px; border-bottom: 1px solid #eee; padding-bottom: 16px; }
        .body-text { font-size: 15px; color: #333; white-space: pre-wrap; }
        .signature { margin-top: 32px; padding-top: 24px; border-top: 1px solid #eee; font-size: 13px; color: #555; }
        .disclaimer { margin-top: 32px; padding: 16px; background: #f8f9fa; border: 1px solid #e2e4e8; border-radius: 4px; font-size: 11px; color: #666; line-height: 1.5; }
        .disclaimer strong { display: block; margin-bottom: 6px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #444; }
        .footer { background: #f8f8f8; padding: 16px 32px; font-size: 11px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <span style="color: white; font-size: 18px; font-weight: bold;">Triton Real Estate Fund</span>
        </div>
        <div class="content">
            @if($onBehalf)
            <div class="on-behalf">
                This email is sent on behalf of <strong>{{ $onBehalf->name }}{{ $onBehalf->title ? ', ' . $onBehalf->title : '' }}</strong>
            </div>
            @endif

            <div class="body-text">{!! $body !!}</div>

            @if($signature)
            <div class="signature">
                {!! $signature->signature_html !!}
            </div>
            @endif

            <div class="disclaimer">
                <strong>Important Notice</strong>
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
        <div class="footer">
            This email and any attachments are confidential and intended solely for the named recipient.
            If you have received this email in error, please notify the sender immediately.
        </div>
    </div>
</body>
</html>