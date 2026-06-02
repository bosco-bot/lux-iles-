<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Privilege Club</title>
</head>
<body style="margin:0; padding:0; background:#f6f6f4; font-family:Arial, sans-serif; color:#0a1a2f;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f6f4; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #ece8df;">
                <tr>
                    <td style="background:#0a1a2f; color:#ffffff; padding:20px 24px;">
                        <h1 style="margin:0; font-size:20px; font-weight:600;">LUXILES - Alerte Privilege Club</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px;">
                        <p style="margin:0 0 14px 0; font-size:15px; line-height:1.6;">
                            Un client a change de palier Privilege Club.
                        </p>
                        <p style="margin:0 0 8px 0; font-size:14px;">
                            <strong>Client :</strong> {{ $client->first_name }} {{ $client->last_name }}<br>
                            <strong>Email :</strong> {{ $client->email }}<br>
                            <strong>Telephone :</strong> {{ $client->phone ?: 'Non renseigne' }}
                        </p>
                        <p style="margin:0 0 18px 0; font-size:14px;">
                            <strong>Changement :</strong>
                            {{ $oldTierLabel }} -> {{ $newTierLabel }}
                            ({{ $isUpgrade ? 'montee' : 'retrogradation' }})
                        </p>

                        <p style="margin:0 0 14px 0; font-size:14px; line-height:1.6;">
                            Merci de traiter la notification WhatsApp depuis la fiche client.
                        </p>

                        <p style="margin:0;">
                            <a href="{{ $adminClientUrl }}" style="display:inline-block; background:#cbae82; color:#0a1a2f; text-decoration:none; padding:10px 16px; border-radius:6px; font-weight:600; font-size:14px;">
                                Ouvrir la fiche client
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 24px; background:#faf9f5; color:#666; font-size:12px;">
                        Message automatique - LUXILES Back-office
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
