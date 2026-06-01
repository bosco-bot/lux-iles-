<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 3px solid #CBAE82;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: bold;
            color: #0A1A2F;
        }
        .logo-gold {
            color: #CBAE82;
            font-style: italic;
        }
        h1 {
            color: #0A1A2F;
            margin-top: 0;
            font-size: 24px;
        }
        .info-section {
            background-color: #F8F8F6;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 12px;
        }
        .info-label {
            font-weight: bold;
            color: #8A96A6;
            display: inline-block;
            width: 120px;
        }
        .info-value {
            color: #0A1A2F;
        }
        .message-box {
            background-color: #F8F8F6;
            padding: 20px;
            border-left: 4px solid #CBAE82;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message-text {
            color: #0A1A2F;
            white-space: pre-wrap;
            line-height: 1.8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            font-size: 12px;
            color: #8A96A6;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #CBAE82;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">LUX<span class="logo-gold">ÎLES</span></div>
            <div style="font-size: 14px; color: #8A96A6; margin-top: 5px;">Villas de Luxe aux Antilles</div>
        </div>

        <h1>Nouveau message de contact</h1>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Nom :</span>
                <span class="info-value"><strong>{{ $firstName }} {{ $lastName }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email :</span>
                <span class="info-value"><a href="mailto:{{ $email }}" style="color: #CBAE82; text-decoration: none;">{{ $email }}</a></span>
            </div>
            @if($phone)
            <div class="info-row">
                <span class="info-label">Téléphone :</span>
                <span class="info-value">{{ $phone }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Sujet :</span>
                <span class="info-value"><strong>{{ $subject }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date :</span>
                <span class="info-value">{{ $submittedAt }}</span>
            </div>
        </div>

        <div class="message-box">
            <div style="font-weight: bold; color: #0A1A2F; margin-bottom: 10px;">Message :</div>
            <div class="message-text">{{ $message }}</div>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <a href="mailto:{{ $email }}?subject=Re: {{ $subject }}" class="button">Répondre par email</a>
        </div>

        <div class="footer">
            <p>Ce message a été envoyé depuis le formulaire de contact du site LUXÎLES.</p>
            <p style="margin-top: 10px; font-size: 11px;">
                @php
                    $companyName = \App\Helpers\SettingsHelper::get('company_name', 'BLUE SECRET');
                    $websiteDomain = parse_url(config('app.url'), PHP_URL_HOST);
                @endphp
                {{ $companyName }} - {{ $websiteDomain }}
            </p>
        </div>
    </div>
</body>
</html>









