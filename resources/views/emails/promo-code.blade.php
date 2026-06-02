<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre code promo LUXÎLES</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;color:#0A1A2F;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#0A1A2F;color:#ffffff;padding:24px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:600;">Votre code promotionnel LUXÎLES</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 16px;">Bonjour <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
                            <p style="margin:0 0 20px;">Voici le code promo que notre équipe vous a réservé :</p>
                            <p style="margin:0 0 20px;text-align:center;">
                                <span style="display:inline-block;padding:12px 20px;border:1px dashed #CBAE82;border-radius:8px;font-size:24px;letter-spacing:2px;font-weight:700;color:#0A1A2F;">
                                    {{ $promoCode->code }}
                                </span>
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8f9fa;border-radius:8px;padding:16px;">
                                <tr>
                                    <td style="padding:4px 0;">Type :</td>
                                    <td style="padding:4px 0;text-align:right;font-weight:600;">
                                        {{ $promoCode->type === 'percent' ? 'Pourcentage' : 'Montant fixe' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0;">Valeur :</td>
                                    <td style="padding:4px 0;text-align:right;font-weight:600;">
                                        @if($promoCode->type === 'percent')
                                            {{ rtrim(rtrim(number_format($promoCode->value, 2, ',', ''), '0'), ',') }} %
                                        @else
                                            {{ number_format($promoCode->value, 2, ',', ' ') }} €
                                        @endif
                                    </td>
                                </tr>
                                @if($validFrom || $validUntil)
                                    <tr>
                                        <td style="padding:4px 0;">Validité :</td>
                                        <td style="padding:4px 0;text-align:right;font-weight:600;">
                                            @if($validFrom) Du {{ $validFrom }} @endif
                                            @if($validUntil) au {{ $validUntil }} @endif
                                        </td>
                                    </tr>
                                @endif
                            </table>
                            <p style="margin:20px 0 12px;">Pour l'utiliser :</p>
                            <ol style="margin:0 0 20px;padding-left:20px;">
                                <li>Connectez-vous à votre espace client.</li>
                                <li>Commencez une réservation sur LUXÎLES.</li>
                                <li>Saisissez ce code dans le champ « Code promotionnel ».</li>
                            </ol>
                            <p style="margin:0 0 20px;text-align:center;">
                                <a href="{{ $bookingUrl }}" style="display:inline-block;background:#0A1A2F;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:6px;">Réserver maintenant</a>
                            </p>
                            <p style="margin:0;">L'équipe LUXÎLES vous souhaite un excellent séjour.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8f9fa;padding:16px;text-align:center;font-size:12px;color:#6c757d;">
                            Cet email est envoyé automatiquement par LUXÎLES.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
