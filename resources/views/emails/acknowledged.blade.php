<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; background: #f9f9f9; }
        .container { max-width: 500px; margin: 80px auto; padding: 40px; background: white; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .icon { font-size: 48px; margin-bottom: 20px; }
        h1 { color: #1a3a4a; }
        p { color: #666; }
    </style>
</head>
<body>
    <div class="container">
        @if($alreadyAcknowledged)
            <div class="icon">✓</div>
            <h1>Already Acknowledged</h1>
            <p>You have already confirmed receipt of these materials.</p>
        @else
            <div class="icon">✅</div>
            <h1>Thank You</h1>
            <p>Your receipt of the materials has been confirmed and recorded.</p>
            <p style="font-size: 12px; color: #999; margin-top: 30px;">Triton Real Estate Fund (CEIC) Limited</p>
        @endif
    </div>
</body>
</html>