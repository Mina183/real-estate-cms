<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1a3a4a; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Triton Real Estate Fund</h1>
        </div>
        <div class="content">
            <p>Dear {{ $contact->full_name }},</p>

            <p>As part of our investor onboarding process, we are required to confirm your eligibility and Professional Client status in accordance with DFSA regulations.</p>

            <p>Please find attached our Investor Eligibility and Professional Client Questionnaire. We kindly ask that you complete and return the signed questionnaire at your earliest convenience.</p>

            <p>This is a mandatory step before we are able to proceed with providing you with the full offering documentation for the Triton Real Estate Fund (CEIC) Limited.</p>

            <p>Should you have any questions regarding the questionnaire or the eligibility requirements, please do not hesitate to contact us.</p>

            <p>Kind regards,<br>
            {{ $senderName }}<br>
            Triton Real Estate Fund</p>
        </div>
        <div class="footer">
            <p>This email and any attachments are confidential and intended solely for the addressee.</p>
        </div>
    </div>
</body>
</html>