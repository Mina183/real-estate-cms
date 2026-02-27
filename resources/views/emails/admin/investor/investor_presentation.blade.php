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

            <p>Thank you for your time and for the engaging discussion regarding the Triton Real Estate Fund. As per your request, please find attached the full Investor Presentation for your review.</p>

            <p>The presentation provides a comprehensive overview of the Fund's strategy, data-led investment methodology, sourcing and asset management platform, governance framework, team, and illustrative case studies. It is designed to support your evaluation of the opportunity and any internal investment committee processes you may wish to undertake.</p>

            <p>To summarise the key highlights:</p>
            <ul>
                <li>DIFC Qualified Investor Fund (CEIC) managed by Axys Capital Ltd (DFSA-regulated)</li>
                <li>Focused on UAE residential real estate, primarily Dubai, with up to 15% GCC allocation</li>
                <li>Target net IRR of 29.78% with a 10% preferred return hurdle and full clawback protections</li>
                <li>Target fund size approximately US$100 million; 5-year term with two 1-year extensions</li>
                <li>Minimum subscription US$1,000,000; DFSA-defined Professional Clients only</li>
            </ul>

            <p>As a next step, I would be happy to arrange a more detailed discussion with our investment team or provide access to additional due diligence materials, should you wish to proceed.</p>

            <p>Please do not hesitate to reach out with any questions or requests for further information.</p>

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