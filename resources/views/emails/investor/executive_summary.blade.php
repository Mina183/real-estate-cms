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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Triton Real Estate Fund</h1>
        </div>
        <div class="content">
            <p>Dear {{ $contact->full_name }},</p>

            <p>Thank you again for your continued interest in the Triton Real Estate Fund. As per your request, please find attached the following materials for your review:</p>

            <ul>
                <li><strong>Executive Summary</strong> – a concise overview of the Fund's investment thesis, strategy, target returns, governance structure and key risk considerations.</li>
                <li><strong>Term Sheet</strong> – a summary of the headline commercial terms including fund size, fees, preferred return, structure and liquidity.</li>
            </ul>

            <p>The Fund targets a net IRR of approximately 29.78% through a focused allocation to UAE residential real estate, leveraging proprietary data infrastructure and vertically integrated sourcing and asset management capabilities. The minimum subscription is US$1,000,000 and the Fund is open only to DFSA-defined Professional Clients.</p>

            <p>These materials are provided for information purposes only and are qualified in their entirety by the Private Placement Memorandum, which will be made available at the appropriate stage.</p>

            <p>I would welcome the opportunity to discuss the Fund in further detail. Please let me know if you would like to schedule a call or meeting, and I will be happy to accommodate your preferred timing.</p>

            <p>Kind regards,<br><br>
            {{ $senderName }}<br>
            {{ $senderTitle }} | Investor Relations<br>
            {{ $senderEmail }} | {{ $senderPhone }}<br>
            Triton Real Estate Fund (CEIC) Limited</p>
        </div>

        <div class="disclaimer">
            <p><strong>IMPORTANT NOTICE</strong></p>
            <p>This communication is confidential and intended solely for persons who qualify as Professional Clients as defined by the Dubai Financial Services Authority (DFSA). It is not directed at, and must not be relied upon by, Retail Clients. This document does not constitute an offer, invitation, inducement or recommendation to subscribe for or purchase any interest in the Triton Real Estate Fund (CEIC) Limited (the "Fund"). The DFSA has not approved or endorsed this document or the Fund. Any investment in the Fund may only be made on the basis of the Private Placement Memorandum (PPM), the Fund's constitutional documents and the Subscription Agreement. Investment in the Fund involves significant risks, including the potential loss of capital, illiquidity and leverage. Past performance, targets or projections are not reliable indicators of future results.</p>
        </div>
    </div>
</body>
</html>