<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1a3a4a; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .disclaimer { padding: 15px; background: #f0f0f0; border-top: 2px solid #1a3a4a; font-size: 11px; color: #555; margin-top: 20px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .header img { max-height: 60px; }
    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <img src="{{ url('/images/logo.png') }}" alt="Triton Real Estate Fund" style="max-height: 60px;">
    </div>
        <div class="content">
            <p>Dear {{ $contact->full_name }},</p>

            <p>Thank you for your time and for the opportunity to connect. As per your request, please find attached a brief overview of the Triton Real Estate Fund (CEIC) Limited.</p>

            <p>Triton is a DIFC-domiciled Qualified Investor Fund focused on UAE residential real estate, primarily Dubai, targeting attractive risk-adjusted returns through a disciplined, data-led investment approach. The Fund is managed by Axys Capital Ltd, a DFSA-regulated firm.</p>

            <p>The attached teaser provides a high-level introduction to the Fund's thesis, target returns, key terms and structure. It is intended as an initial overview only and does not constitute an offer or recommendation.</p>

            <p>Should you wish to explore the opportunity in more detail, I would be happy to arrange a call or meeting at your convenience and share further materials including our Executive Summary, Term Sheet and Investor Presentation, subject to eligibility confirmation.</p>

            <p>Please do not hesitate to reach out with any questions.</p>

            @if($acknowledgementUrl)
                <p style="margin-top: 30px;">
                    <a href="{{ $acknowledgementUrl }}" style="display: inline-block; padding: 12px 24px; background-color: #1a7a4a; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
                        ✓ Confirm Receipt of Materials
                    </a>
                </p>
                <p style="font-size: 12px; color: #999;">By clicking the button above, you confirm that you have received and reviewed the attached materials.</p>
            @endif

            <p>Kind regards,<br><br>
            {{ $senderName }}<br>
            {{ $senderTitle ?? '' }} | Investor Relations<br>
            {{ $senderEmail ?? '' }} | {{ $senderPhone ?? '' }}<br>
            Triton Real Estate Fund (CEIC) Limited</p>
        </div>

        <div class="disclaimer">
            <p><strong>IMPORTANT NOTICE</strong></p>
            <p>This communication is confidential and intended solely for persons who qualify as Professional Clients as defined by the Dubai Financial Services Authority (DFSA). It is not directed at, and must not be relied upon by, Retail Clients. This document does not constitute an offer, invitation, inducement or recommendation to subscribe for or purchase any interest in the Triton Real Estate Fund (CEIC) Limited (the "Fund"). The DFSA has not approved or endorsed this document or the Fund. Any investment in the Fund may only be made on the basis of the Private Placement Memorandum (PPM), the Fund's constitutional documents and the Subscription Agreement. Investment in the Fund involves significant risks, including the potential loss of capital, illiquidity and leverage. Past performance, targets or projections are not reliable indicators of future results.</p>
        </div>
    </div>
</body>
</html>