<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de réservation</title>
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
        .reservation-number {
            background-color: #f8f9fa;
            border-left: 4px solid #0A1A2F;
            padding: 15px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: 600;
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
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #0A1A2F;
        }
        .button {
            display: inline-block;
            background-color: #0A1A2F;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .highlight {
            color: #0A1A2F;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmation de Réservation</h1>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
            
            <p>Nous avons le plaisir de vous confirmer votre réservation !</p>
            
            <div class="reservation-number">
                Numéro de réservation : <span class="highlight">{{ $reservation->reservation_number }}</span>
            </div>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Villa :</span>
                    <span class="info-value">{{ $villa->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date d'arrivée :</span>
                    <span class="info-value">{{ $checkIn }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date de départ :</span>
                    <span class="info-value">{{ $checkOut }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nombre de nuits :</span>
                    <span class="info-value">{{ $reservation->number_of_nights }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nombre d'invités :</span>
                    <span class="info-value">{{ $reservation->number_of_guests }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Prix total :</span>
                    <span class="info-value">{{ number_format($reservation->total_price, 2, ',', ' ') }} €</span>
                </div>
            </div>
            
            <p>Votre réservation est confirmée. Vous recevrez prochainement les documents de réservation par email.</p>
            
            <p>Pour toute question, n'hésitez pas à nous contacter.</p>
            
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










