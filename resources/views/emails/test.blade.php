<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            line-height: 1.6;
            color: #0A1A2F;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
        }
        .header {
            background: linear-gradient(135deg, #0A1A2F 0%, #1a3a5f 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .success-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Test de Configuration Email</h1>
        </div>
        
        <div class="content">
            <div class="success-box">
                <strong>✅ Email de test envoyé avec succès !</strong>
            </div>
            
            <p>{{ $message ?? 'Ceci est un email de test pour vérifier la configuration SMTP de PHPMailer.' }}</p>
            
            <p>Si vous recevez cet email, cela signifie que la configuration SMTP est correcte.</p>
            
            <p><strong>Date d'envoi :</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
        
        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} LUXÎLES. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>










