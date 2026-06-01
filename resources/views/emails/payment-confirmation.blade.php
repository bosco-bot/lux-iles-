<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de paiement</title>
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
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
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
            color: #28a745;
            margin: 10px 0;
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
            <h1>Confirmation de Paiement</h1>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
            
            <div class="success-box">
                <strong>✅ Votre paiement a été reçu avec succès !</strong>
            </div>
            
            <div class="amount-box">
                <p style="margin: 0; color: #666;">Montant payé :</p>
                <div class="amount">{{ number_format($payment->amount, 2, ',', ' ') }} €</div>
            </div>
            
            <div class="info-box">
                <div class="info-row">
                    <span>Numéro de paiement :</span>
                    <strong>{{ $payment->payment_number }}</strong>
                </div>
                <div class="info-row">
                    <span>Réservation :</span>
                    <strong>{{ $reservation->reservation_number }}</strong>
                </div>
                <div class="info-row">
                    <span>Type de paiement :</span>
                    <strong>{{ $payment->type === 'deposit' ? 'Acompte' : ($payment->type === 'balance' ? 'Solde' : ($payment->type === 'deposit_guarantee' ? 'Garantie (Caution)' : 'Paiement')) }}</strong>
                </div>
                <div class="info-row">
                    <span>Date de paiement :</span>
                    <strong>{{ \Carbon\Carbon::parse($payment->paid_at ?? now())->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="info-row">
                    <span>Méthode :</span>
                    <strong>{{ ucfirst($payment->payment_method ?? 'Stripe') }}</strong>
                </div>
            </div>
            
            <p>Villa : <strong>{{ $villa->name }}</strong></p>
            
            <p>Merci pour votre confiance. Votre réservation est confirmée.</p>
            
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










