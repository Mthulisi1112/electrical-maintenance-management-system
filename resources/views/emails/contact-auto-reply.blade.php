<!DOCTYPE html>
<html>
<head>
    <title>Thank You for Contacting EMMS</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .message-box { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Contacting Us!</h1>
        </div>
        <div class="content">
            <p>Dear {{ $data['name'] }},</p>
            
            <p>Thank you for reaching out to the Electrical Maintenance Management System (EMMS) team. We have received your message and will get back to you as soon as possible.</p>
            
            <p><strong>Your message:</strong></p>
            <div class="message-box">
                <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
                <p><strong>Message:</strong></p>
                <p>{{ $data['message'] }}</p>
            </div>
            
            <p>If you have any urgent concerns, please contact us directly at support@emms.com or call us at +1 (555) 123-4567.</p>
            
            <p>Best regards,<br>
            The EMMS Team</p>
        </div>
    </div>
</body>
</html>