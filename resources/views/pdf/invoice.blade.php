<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture - {{ $reservation->reservation_number }}</title>
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
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24pt;
            font-weight: bold;
            color: #0A1A2F;
        }
        .logo-gold {
            color: #CBAE82;
            font-style: italic;
        }
        .invoice-title {
            font-size: 20pt;
            font-weight: bold;
            color: #0A1A2F;
            margin: 10px 0;
        }
        .invoice-number {
            font-size: 14pt;
            color: #CBAE82;
            font-weight: bold;
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
            margin-bottom: 5px;
            font-size: 10pt;
        }
        .info-label {
            font-weight: bold;
            color: #8A96A6;
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }
        table th {
            background-color: #F8F8F6;
            font-weight: bold;
            color: #0A1A2F;
            text-align: right;
        }
        table td:first-child {
            text-align: left;
        }
        table td:not(:first-child) {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #F8F8F6;
            font-size: 12pt;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            font-size: 9pt;
            color: #8A96A6;
            text-align: center;
        }
        .payment-info {
            background-color: #F8F8F6;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
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
            <div style="font-size: 9pt; color: #8A96A6; margin-top: 10px; line-height: 1.6;">
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
                <div>TVA intracommunautaire: {{ $companyVat }}</div>
                @endif
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-number">{{ $documentNumber }}</div>
            <div style="font-size: 9pt; color: #8A96A6; margin-top: 10px;">
                Date: {{ now()->format('d/m/Y') }}<br>
                Réservation: #{{ $reservation->reservation_number }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Facturé à</div>
        <div class="info-row">
            <strong>{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</strong>
        </div>
        <div class="info-row">{{ $reservation->guest_email }}</div>
        @if($reservation->guest_phone)
        <div class="info-row">{{ $reservation->guest_phone }}</div>
        @endif
        @if($reservation->guest_address)
        <div class="info-row">{{ $reservation->guest_address }}</div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Détails de la Réservation</div>
        <div class="info-row">
            <span class="info-label">Villa:</span>
            <span>{{ $reservation->villa->name ?? 'N/A' }} - {{ $reservation->villa->island->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Période:</span>
            <span>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Durée:</span>
            <span>{{ $reservation->number_of_nights }} nuit(s)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Voyageurs:</span>
            <span>{{ $reservation->number_of_guests }} personne(s)</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Location villa ({{ $reservation->number_of_nights }} nuit(s))</td>
                <td style="text-align: right;">{{ $reservation->number_of_nights }}</td>
                <td style="text-align: right;">{{ number_format($reservation->base_price / $reservation->number_of_nights, 2, ',', ' ') }} €</td>
                <td style="text-align: right;">{{ number_format($reservation->base_price, 2, ',', ' ') }} €</td>
            </tr>
            @if($reservation->cleaning_fee > 0)
            <tr>
                <td>Frais de ménage</td>
                <td style="text-align: right;">1</td>
                <td style="text-align: right;">{{ number_format($reservation->cleaning_fee, 2, ',', ' ') }} €</td>
                <td style="text-align: right;">{{ number_format($reservation->cleaning_fee, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            @if($reservation->service_fee > 0)
            <tr>
                <td>Frais de service</td>
                <td style="text-align: right;">1</td>
                <td style="text-align: right;">{{ number_format($reservation->service_fee, 2, ',', ' ') }} €</td>
                <td style="text-align: right;">{{ number_format($reservation->service_fee, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            @if($reservation->vat_amount > 0)
            <tr>
                <td>TVA (sur frais de ménage et service)</td>
                <td style="text-align: right;">1</td>
                <td style="text-align: right;">{{ number_format($reservation->vat_amount, 2, ',', ' ') }} €</td>
                <td style="text-align: right;">{{ number_format($reservation->vat_amount, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            @if($reservation->tourist_tax > 0)
            <tr>
                <td>Taxe de séjour ({{ $reservation->number_of_guests }} pers. × {{ $reservation->number_of_nights }} nuit(s))</td>
                <td style="text-align: right;">{{ $reservation->number_of_guests * $reservation->number_of_nights }}</td>
                <td style="text-align: right;">{{ number_format($reservation->tourist_tax / ($reservation->number_of_guests * $reservation->number_of_nights), 2, ',', ' ') }} €</td>
                <td style="text-align: right;">{{ number_format($reservation->tourist_tax, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 20px;">TOTAL TTC</td>
                <td style="text-align: right;">{{ number_format($reservation->total_price, 2, ',', ' ') }} €</td>
            </tr>
        </tbody>
    </table>

    <div class="payment-info">
        <div style="font-weight: bold; margin-bottom: 10px; color: #0A1A2F;">Informations de Paiement</div>
        <div class="info-row">
            <span class="info-label">Acompte:</span>
            <span><strong>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</strong> ({{ $reservation->deposit_percentage }}%)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Solde restant:</span>
            <span><strong>{{ number_format($reservation->balance_amount, 2, ',', ' ') }} €</strong></span>
        </div>
        @if($reservation->deposit_guarantee > 0)
        <div class="info-row">
            <span class="info-label">Dépôt de garantie:</span>
            <span><strong>{{ number_format($reservation->deposit_guarantee, 2, ',', ' ') }} €</strong> (à régulariser avant l'arrivée)</span>
        </div>
        @endif
    </div>

    <div class="footer">
        @php
            $websiteDomain = parse_url(config('app.url'), PHP_URL_HOST);
            $companyName = \App\Helpers\SettingsHelper::get('company_name', 'BLUE SECRET');
        @endphp
        <p>Facture générée le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>{{ $companyName }} - Villas de Luxe aux Antilles | {{ $websiteDomain }}</p>
        <p style="margin-top: 10px; font-size: 8pt;">Cette facture est générée automatiquement et fait foi.</p>
    </div>
</body>
</html>



