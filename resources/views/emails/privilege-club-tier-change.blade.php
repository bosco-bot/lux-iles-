<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privilege Club</title>
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #0A1A2F; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #0A1A2F 0%, #1a3a5f 100%); color: #ffffff; padding: 30px 20px; text-align: center; }
        .content { padding: 30px 20px; }
        .button { display: inline-block; background-color: #CBAE82; color: #0A1A2F; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600; margin-top: 20px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #8A96A6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 22px;">LUXÎLES PRIVILEGE CLUB</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $user->first_name }},</p>

            @if($newTier)
                <p>
                    @if($isUpgrade ?? false)
                        Nous avons le plaisir de vous annoncer votre accession au niveau <strong>{{ $newTierLabel }}</strong> du LUXÎLES PRIVILEGE CLUB.
                    @else
                        Votre statut Privilege Club est désormais : <strong>{{ $newTierLabel ?? 'Non membre' }}</strong>.
                    @endif
                </p>
            @else
                <p>Votre statut Privilege Club a été mis à jour.</p>
            @endif

            @if($tierDefinition ?? null)
                <p><strong>Vos avantages :</strong></p>
                <ul>
                    @foreach($tierDefinition['benefits'] as $benefit)
                        <li>{{ $benefit }}</li>
                    @endforeach
                </ul>
            @endif

            <p>Notre équipe vous contactera également sur WhatsApp pour vous féliciter personnellement.</p>

            <p style="text-align: center;">
                <a href="{{ $clubUrl }}" class="button">Voir mon espace Privilege Club</a>
            </p>
        </div>
        <div class="footer">
            <p>LUXÎLES — Villas de prestige aux Caraïbes</p>
        </div>
    </div>
</body>
</html>
