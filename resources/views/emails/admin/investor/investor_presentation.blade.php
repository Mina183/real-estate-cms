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

            <p>Please find attached our Investor Presentation for the Triton Real Estate Fund (CEIC) Limited.</p>

            <p>This presentation provides a comprehensive overview of the Fund's investment thesis, market opportunity, portfolio strategy, and projected returns.</p>

            <p>These materials are strictly confidential and are intended solely for your personal review as part of your due diligence process. They must not be reproduced, distributed, or shared with any third party without our prior written consent.</p>

            <p>We look forward to discussing this opportunity with you further. Please do not hesitate to contact us should you have any questions.</p>

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