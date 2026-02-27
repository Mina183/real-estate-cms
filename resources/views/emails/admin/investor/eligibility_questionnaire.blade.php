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

            <p>Thank you for your time and for your continued interest in the Triton Real Estate Fund (CEIC) Limited. As per your request, please find attached our Investor Eligibility Questionnaire & Professional Client Acknowledgement for your completion.</p>

            <p>As the Fund is structured as a DIFC Qualified Investor Fund (QIF) under the DFSA Collective Investment Rules, participation is restricted by regulation to persons classified as Professional Clients under the DFSA Conduct of Business Module. We are therefore required to confirm your eligibility before we are able to share further offering materials, including the Executive Summary, Investor Presentation and Term Sheet.</p>

            <p>The questionnaire covers the following areas:</p>
            <ul>
                <li><strong>Investor Details</strong> – your legal name, investor type, jurisdiction and proposed subscription amount.</li>
                <li><strong>Professional Client Classification</strong> – confirmation of whether you qualify as a Deemed Professional Client or Assessed Professional Client under DFSA COB Rules 2.3.4, 2.3.7 or 2.3.8.</li>
                <li><strong>Investment Experience & Risk Profile</strong> – a brief summary of your relevant experience and acknowledgement of key investment risks associated with the Fund.</li>
                <li><strong>Declarations & Signature</strong> – formal representations regarding your eligibility, minimum subscription commitment and non-U.S. Person status.</li>
            </ul>

            <p>Please complete and return the signed questionnaire at your earliest convenience. Once our compliance team has reviewed and confirmed your Professional Client status, we will be pleased to share the Executive Summary, Term Sheet and full Investor Presentation for your consideration.</p>

            <p>If you are unsure whether you qualify as a Professional Client under DFSA rules, we would recommend seeking independent legal or financial advice. We are also happy to assist with any questions you may have regarding the form itself.</p>

            <p>Please do not hesitate to reach out should you require any clarification or assistance.</p>

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