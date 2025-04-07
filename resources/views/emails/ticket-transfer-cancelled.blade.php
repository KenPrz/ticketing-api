<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Transfer Cancelled</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #F8F9FA;
            margin: 0;
            padding: 0;
            color: #1C1C1E;
            line-height: 1.6;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #8B2EFF, #B15EFF);
            color: #FFFFFF;
            padding: 32px 24px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .content {
            padding: 32px 24px;
        }
        
        .greeting {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 24px;
            color: #1C1C1E;
        }
        
        .text-line {
            margin-bottom: 16px;
            font-size: 16px;
        }
        
        .icon {
            text-align: center;
            margin: 24px 0;
        }
        
        .icon-circle {
            width: 64px;
            height: 64px;
            background-color: #FF9500;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        
        .ticket-info {
            background-color: #F0E6FF;
            border-left: 4px solid #8B2EFF;
            padding: 16px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .ticket-info p {
            margin: 8px 0;
            font-size: 16px;
        }
        
        .ticket-info .event-name {
            font-weight: 600;
            font-size: 18px;
            color: #8B2EFF;
        }
        
        .info-message {
            background-color: #FFF9E6;
            border-left: 4px solid #FF9500;
            padding: 16px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .button {
            display: inline-block;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            background-color: #8B2EFF;
            color: #FFFFFF;
            margin-top: 24px;
        }
        
        .footer {
            padding: 24px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
            background-color: #F8F9FA;
        }
        
        /* Responsive styles */
        @media screen and (max-width: 480px) {
            .header {
                padding: 24px 16px;
            }
            .content {
                padding: 24px 16px;
            }
            .button {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ticket Transfer Cancelled</h1>
        </div>
        
        <div class="content">
            <div class="greeting">Hello!</div>
            
            <div class="text-line">{{ $fromUser->name }} has cancelled the ticket transfer request for:</div>
            
            <div class="ticket-info">
                <p class="event-name">{{ $ticket->event->name }}</p>
                <p><strong>Ticket:</strong> {{ $ticket->ticket_name }}</p>
            </div>
            
            <div class="icon">
                <div class="icon-circle">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5.99L19.53 19H4.47L12 5.99M12 2L1 21h22L12 2zm1 14h-2v2h2v-2zm0-6h-2v4h2v-4z" fill="white"/>
                    </svg>
                </div>
            </div>
            
            <div class="info-message">
                <p><strong>Transfer Request Cancelled</strong></p>
                <p>The ticket transfer request has been cancelled by the sender. No action is required from you.</p>
            </div>
            
            <div class="text-line">
                You can still explore and purchase tickets for various events on QPhoria.
            </div>

        </div>
        
        <div class="footer">
            <p>If you have any questions, please contact <a href="mailto:support@qphoria.com" style="color: #8B2EFF; text-decoration: none;">support@qphoria.com</a></p>
            <p style="margin-top: 16px; font-size: 12px; color: #888;">© 2025 QPhoria. All rights reserved.</p>
            <p style="margin-top: 8px; font-size: 12px; color: #888;">The ultimate platform for event tickets and experiences.</p>
        </div>
    </div>
</body>
</html>