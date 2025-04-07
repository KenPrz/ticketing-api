<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Transfer Request</title>
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
        
        .ticket-info {
            background-color: #F8F9FA;
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
        
        .actions {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin: 32px 0;
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
        }
        
        .accept-button {
            background-color: #8B2EFF;
            color: #FFFFFF;
        }
        
        .reject-button {
            background-color: #E0E0E0;
            color: #1C1C1E;
            border: none;
        }
        
        .expiry-notice {
            background-color: #FFF9E6;
            border-left: 4px solid #FF9500;
            padding: 12px 16px;
            margin: 24px 0;
            font-size: 14px;
            border-radius: 0 8px 8px 0;
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
            <h1>Ticket Transfer Request</h1>
        </div>
        
        <div class="content">
            <div class="greeting">Hello!</div>
            
            <div class="text-line">{{ $fromUser->name }} has sent you a ticket transfer request for the event:</div>
            
            <div class="ticket-info">
                <p class="event-name">{{ $ticket->event->name }}</p>
                <p><strong>Ticket:</strong> {{ $ticket->ticket_name }}</p>
            </div>
            
            <div class="actions">
                <a href="{{ $acceptUrl }}" class="button accept-button">Accept Transfer</a>
                <a href="{{ $rejectUrl }}" class="button reject-button">Reject Transfer</a>
            </div>
            
            <div class="expiry-notice">
                This transfer request will expire in 7 days.
            </div>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact <a href="mailto:transfer@qphoria.online" style="color: #8B2EFF; text-decoration: none;">support@qphoria.com</a></p>
            <p style="margin-top: 16px; font-size: 12px; color: #888;">© 2025 QPhoria. All rights reserved.</p>
            <p style="margin-top: 8px; font-size: 12px; color: #888;">The ultimate platform for event tickets and experiences.</p>
        </div>
    </div>
</body>
</html>