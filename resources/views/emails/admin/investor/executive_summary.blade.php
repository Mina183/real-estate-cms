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

            <p>Following our initial discussion, please find attached the Executive Summary and Term Sheet for the Triton Real Estate Fund (CEIC) Limited.</p>

            <p>These documents provide a detailed overview of the Fund's investment strategy, target returns, fee structure, and key commercial terms.</p>

            <p>Please note that these materials are strictly confidential and are provided to you solely for your personal evaluation. They must not be reproduced or distributed without our prior written consent.</p>

            <p>We would welcome the opportunity to discuss these materials with you in more detail. Please feel free to reach out at your convenience.</p>

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