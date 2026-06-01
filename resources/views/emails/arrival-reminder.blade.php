<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel avant arrivée</title>
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
            <h1>Rappel - Votre Séjour Approche</h1>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
            
            <p>Votre séjour dans notre villa commence dans <strong>{{ $daysBefore }} jour(s)</strong> !</p>
            
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
                    <span>Date d'arrivée :</span>
                    <strong>{{ $checkIn }}</strong>
                </div>
                <div class="info-row">
                    <span>Date de départ :</span>
                    <strong>{{ $checkOut }}</strong>
                </div>
                <div class="info-row">
                    <span>Nombre de nuits :</span>
                    <strong>{{ $reservation->number_of_nights }}</strong>
                </div>
            </div>
            
            <p>Nous avons hâte de vous accueillir !</p>
            
            <p>Pour toute question ou demande particulière, n'hésitez pas à nous contacter.</p>
            
            <p>À très bientôt,<br>
            <strong>L'équipe LUXÎLES</strong></p>
        </div>
        
        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} LUXÎLES. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>










