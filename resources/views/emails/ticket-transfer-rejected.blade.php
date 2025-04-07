<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Transfer Rejected</title>
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
            background-color: #E0E0E0;
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
            background-color: #F5F5F5;
            border-left: 4px solid #555555;
            padding: 16px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .options-box {
            background-color: #F0E6FF;
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
        }
        
        .options-box h3 {
            margin-top: 0;
            color: #8B2EFF;
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
            <h1>Ticket Transfer Rejected</h1>
        </div>
        
        <div class="content">
            <div class="greeting">Hello!</div>
            
            <div class="text-line">{{ $toUser->name }} has declined your ticket transfer request for:</div>
            
            <div class="ticket-info">
                <p class="event-name">{{ $ticket->event->name }}</p>
                <p><strong>Ticket:</strong> {{ $ticket->ticket_name }}</p>
            </div>
            
            <div class="icon">
                <div class="icon-circle">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" fill="#555555"/>
                    </svg>
                </div>
            </div>
            
            <div class="info-message">
                <p><strong>Your ticket is still yours</strong></p>
                <p>The ticket remains in your possession and is still available in your QPhoria account.</p>
            </div>
            
            <div class="options-box">
                <h3>What would you like to do next?</h3>
                <ul>
                    <li>Keep the ticket for yourself</li>
                    <li>Transfer the ticket to someone else</li>
                    <li>List the ticket for resale on QPhoria Marketplace</li>
                </ul>
            </div>
            
            <div class="text-line">
                You can manage your tickets and view all available options in the QPhoria app.
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