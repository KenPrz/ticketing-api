<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Transfer Rejected | QPhoria</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #F8F9FA;
            margin: 0;
            padding: 0;
            color: #1C1C1E;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            max-width: 600px;
            width: 90%;
            background-color: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .header {
            background: linear-gradient(135deg, #8B2EFF, #B15EFF);
            color: #FFFFFF;
            padding: 32px 24px;
        }
        
        .header img {
            margin-bottom: 16px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .content {
            padding: 40px 32px;
        }
        
        .rejected-icon {
            width: 80px;
            height: 80px;
            background-color: #E0E0E0;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 24px;
        }
        
        .rejected-icon svg {
            width: 40px;
            height: 40px;
            fill: #555;
        }
        
        .title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #1C1C1E;
        }
        
        .message {
            font-size: 16px;
            color: #555;
            margin-bottom: 32px;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        
        .info-card {
            background-color: #F0F0F0;
            border-left: 4px solid #8B2EFF;
            padding: 16px;
            margin: 24px auto;
            border-radius: 8px;
            max-width: 80%;
            text-align: left;
        }
        
        .info-card h3 {
            margin-top: 0;
            color: #8B2EFF;
            font-size: 18px;
        }
        
        .info-card p {
            margin-bottom: 0;
        }
        
        .app-button {
            display: inline-block;
            background-color: #8B2EFF;
            color: #FFFFFF;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 16px;
            transition: background-color 0.2s;
            border: none;
            cursor: pointer;
        }
        
        .app-button:hover {
            background-color: #7928DA;
        }
        
        .footer {
            padding: 24px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
            background-color: #F8F9FA;
        }
        
        .footer a {
            color: #8B2EFF;
            text-decoration: none;
        }
        
        @media screen and (max-width: 480px) {
            .container {
                width: 95%;
            }
            .content {
                padding: 32px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="/api/placeholder/150/50" alt="QPhoria Logo" />
            <h1>Ticket Transfer Rejected</h1>
        </div>
        
        <div class="content">
            <div class="rejected-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                </svg>
            </div>
            
            <div class="title">Ticket Transfer Declined</div>
            
            <div class="message">
                You've declined this ticket transfer. The sender has been notified that you chose not to accept the ticket.
            </div>
            
            <div class="info-card">
                <h3>Change your mind?</h3>
                <p>If you declined by mistake or have changed your mind, please contact the sender and ask them to initiate a new transfer request.</p>
            </div>
            
            <div class="info-card">
                <h3>Looking for tickets?</h3>
                <p>Check out the QPhoria app to discover exciting events and purchase tickets directly.</p>
            </div>
            
            <a href="#" class="app-button">Explore QPhoria Events</a>
        </div>
        
        <div class="footer">
            <p>If you have any questions, please contact <a href="mailto:support@qphoria.com">support@qphoria.com</a></p>
            <p>© 2025 QPhoria. All rights reserved.</p>
        </div>
    </div>
</body>
</html>