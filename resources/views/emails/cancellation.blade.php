<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annulation de réservation</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
        .info-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
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
            <h1>Annulation de Réservation</h1>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
            
            <p>Nous vous confirmons l'annulation de votre réservation <strong>{{ $reservation->reservation_number }}</strong>.</p>
            
            <div class="info-box">
                <div class="info-row">
                    <span>Réservation :</span>
                    <strong>{{ $reservation->reservation_number }}</strong>
                </div>
                <div class="info-row">
                    <span>Villa :</span>
                    <strong>{{ $villa->name }}</strong>
                </div>
                <div class="info-row">
                    <span>Dates :</span>
                    <strong>{{ $checkIn }} - {{ $checkOut }}</strong>
                </div>
                @if($reservation->cancellation_reason)
                <div class="info-row">
                    <span>Raison :</span>
                    <strong>{{ $reservation->cancellation_reason }}</strong>
                </div>
                @endif
            </div>
            
            <p>Nous sommes désolés de ne pas pouvoir vous accueillir cette fois-ci.</p>
            
            <p>Si vous avez des questions concernant cette annulation ou souhaitez effectuer une nouvelle réservation, n'hésitez pas à nous contacter.</p>
            
            <p>Cordialement,<br>
            <strong>L'équipe LUXÎLES</strong></p>
        </div>
        
        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} LUXÎLES. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>










