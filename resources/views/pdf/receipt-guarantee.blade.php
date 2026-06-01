<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Caution - {{ $reservation->reservation_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #0A1A2F;
        }
        .header {
            border-bottom: 3px solid #CBAE82;
            padding-bottom: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24pt;
            font-weight: bold;
            color: #0A1A2F;
        }
        .receipt-title {
            font-size: 20pt;
            font-weight: bold;
            color: #0A1A2F;
            margin: 20px 0;
            text-align: center;
        }
        .receipt-number {
            font-size: 14pt;
            color: #CBAE82;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .amount-box {
            background-color: #F8F8F6;
            border: 2px solid #CBAE82;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .amount-label {
            font-size: 11pt;
            color: #8A96A6;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 32pt;
            font-weight: bold;
            color: #0A1A2F;
            font-family: 'Playfair Display', serif;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #CBAE82;
            margin-bottom: 10px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #8A96A6;
            display: inline-block;
            width: 150px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            font-size: 9pt;
            color: #8A96A6;
            text-align: center;
        }
        .guarantee-note {
            background-color: #fffaf0;
            border: 1px solid #CBAE82;
            padding: 15px;
            margin-top: 20px;
            font-size: 10pt;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $companyName = \App\Helpers\SettingsHelper::get('company_name', 'BLUE SECRET');
            $companyAddress = \App\Helpers\SettingsHelper::get('company_address', '4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE');
            $companyPhone = \App\Helpers\SettingsHelper::get('company_phone', '+33 7 66 33 41 98');
            $companyEmail = \App\Helpers\SettingsHelper::get('company_email', 'contact.luxiles@gmail.com');
            $companySiret = \App\Helpers\SettingsHelper::get('company_siret', '85262415400013');
            $companyVat = \App\Helpers\SettingsHelper::get('company_vat', 'FR31852624154');
        @endphp
        <div class="logo">{{ $companyName }}</div>
        <div style="font-size: 9pt; color: #8A96A6; margin-top: 5px;">Villas de Luxe aux Antilles</div>
        <div style="font-size: 8pt; color: #8A96A6; margin-top: 10px; line-height: 1.5;">
            @if($companyAddress)
            <div>{{ $companyAddress }}</div>
            @endif
            @if($companyPhone)
            <div>Téléphone: {{ $companyPhone }}</div>
            @endif
            <div>Email: {{ $companyEmail }}</div>
            @if($companySiret)
            <div>SIRET: {{ $companySiret }}</div>
            @endif
            @if($companyVat)
            <div>TVA: {{ $companyVat }}</div>
            @endif
        </div>
    </div>

    <div class="receipt-title">REÇU DE CAUTION</div>
    <div class="receipt-number">{{ $documentNumber }}</div>

    <div class="amount-box">
        <div class="amount-label">Montant de la caution reçu</div>
        <div class="amount-value">{{ number_format($payment->amount, 2, ',', ' ') }} €</div>
    </div>

    <div class="section">
        <div class="section-title">Informations de la Réservation</div>
        <div class="info-row">
            <span class="info-label">Numéro de réservation:</span>
            <span><strong>#{{ $reservation->reservation_number }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Client:</span>
            <span><strong>{{ $reservation->user->first_name }} {{ $reservation->user->last_name }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Villa:</span>
            <span>{{ $reservation->villa->name ?? 'N/A' }} - {{ $reservation->villa->island->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Période:</span>
            <span>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</span>
        </div>
    </div>

    @if($payment)
    <div class="section">
        <div class="section-title">Informations de Paiement</div>
        <div class="info-row">
            <span class="info-label">Numéro de transaction:</span>
            <span>#{{ $payment->payment_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Méthode:</span>
            <span>{{ $payment->paymentMethodLabel }}</span>
        </div>
        @if($payment->paid_at)
        <div class="info-row">
            <span class="info-label">Date de règlement:</span>
            <span>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y à H:i') }}</span>
        </div>
        @endif
    </div>
    @endif

    <div class="guarantee-note">
        <strong>Note d'information :</strong><br>
        Ce montant constitue un dépôt de garantie (caution) pour votre séjour. Il n'est pas encaissé comme un revenu mais conservé à titre de garantie. Sauf dégradation constatée lors de l'état des lieux de sortie, ce montant vous sera restitué ou l'empreinte sera libérée dans les délais prévus aux conditions générales de location.
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        @php
            $websiteDomain = parse_url(config('app.url'), PHP_URL_HOST);
            $companyName = \App\Helpers\SettingsHelper::get('company_name', 'BLUE SECRET');
        @endphp
        <p>{{ $companyName }} - Villas de Luxe aux Antilles | {{ $websiteDomain }}</p>
        <p style="margin-top: 10px; font-size: 8pt;">Ce document atteste de la réception du dépôt de garantie pour la réservation #{{ $reservation->reservation_number }}</p>
    </div>
</body>
</html>
