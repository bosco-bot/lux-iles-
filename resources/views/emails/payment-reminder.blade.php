<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de paiement</title>
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
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .amount-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .amount {
            font-size: 32px;
            font-weight: 700;
            color: #0A1A2F;
            margin: 10px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Rappel de Paiement</h1>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
            
            <p>Nous vous rappelons qu'un paiement est en attente pour votre réservation <strong>{{ $reservation->reservation_number }}</strong>.</p>
            
            <div class="alert-box">
                @if($dueDate)
                    <strong>Date d'échéance : {{ $dueDate }}</strong>
                @endif
            </div>
            
            <div class="amount-box">
                <p style="margin: 0; color: #666;">Montant à régler :</p>
                <div class="amount">{{ number_format($payment->amount, 2, ',', ' ') }} €</div>
                <p style="margin: 0; color: #666; font-size: 14px;">
                    Type : {{ $payment->type === 'deposit' ? 'Acompte' : ($payment->type === 'balance' ? 'Solde' : 'Paiement') }}
                </p>
            </div>
            
            <p>Villa : <strong>{{ $villa->name }}</strong></p>
            <p>Dates : {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</p>
            
            <p>Merci de procéder au règlement dans les plus brefs délais pour confirmer votre réservation.</p>
            
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










