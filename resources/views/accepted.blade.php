<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Transfer Accepted | QPhoria</title>
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
        
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #4CAF50;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 24px;
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
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
        }
        
        .info-card {
            background-color: #F0E6FF;
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
            <h1>Ticket Transfer Accepted</h1>
        </div>
        
        <div class="content">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
            </div>
            
            <div class="title">Ticket Successfully Transferred!</div>
            
            <div class="message">
                You've successfully accepted the ticket transfer. The ticket is now available in your QPhoria account.
            </div>
            
            <div class="info-card">
                <h3>What's Next?</h3>
                <p>Open your QPhoria app to view all details about your new ticket, including:</p>
                <ul>
                    <li>Event date and location</li>
                    <li>Seating information</li>
                    <li>Digital ticket QR code for entry</li>
                    <li>Event updates and notifications</li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3>Don't have the app yet?</h3>
                <p>Download the QPhoria app to access your tickets anytime, anywhere.</p>
            </div>
            
            <a href="#" class="app-button">Open QPhoria App</a>
        </div>
        
        <div class="footer">
            <p>If you have any questions, please contact <a href="mailto:support@qphoria.com">support@qphoria.com</a></p>
            <p>© 2025 QPhoria. All rights reserved.</p>
        </div>
    </div>
</body>
</html>