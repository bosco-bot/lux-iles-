<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrat de Location - {{ $reservation->reservation_number }}</title>
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
        .document-title {
            font-size: 18pt;
            font-weight: bold;
            color: #0A1A2F;
            margin: 20px 0;
            text-align: center;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #CBAE82;
            border-bottom: 1px solid #CBAE82;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            color: #8A96A6;
        }
        .info-value {
            display: table-cell;
            color: #0A1A2F;
        }
        .signature-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #E5E7EB;
        }
        .signature-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
            margin: 0 2%;
        }
        .signature-line {
            border-top: 1px solid #0A1A2F;
            margin-top: 60px;
            padding-top: 5px;
            text-align: center;
            font-size: 9pt;
            color: #8A96A6;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            font-size: 9pt;
            color: #8A96A6;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }
        table th {
            background-color: #F8F8F6;
            font-weight: bold;
            color: #0A1A2F;
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

    <div class="document-title">CONTRAT DE LOCATION</div>

    <div class="section">
        <div class="section-title">Informations de la Réservation</div>
        <div class="info-row">
            <div class="info-label">Numéro de réservation :</div>
            <div class="info-value"><strong>#{{ $reservation->reservation_number }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Date du contrat :</div>
            <div class="info-value">{{ now()->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Numéro de document :</div>
            <div class="info-value"><strong>{{ $documentNumber }}</strong></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Villa Louée</div>
        <div class="info-row">
            <div class="info-label">Nom de la villa :</div>
            <div class="info-value"><strong>{{ $reservation->villa->name ?? 'N/A' }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Localisation :</div>
            <div class="info-value">{{ $reservation->villa->island->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Adresse :</div>
            <div class="info-value">{{ $reservation->villa->address ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Locataire</div>
        <div class="info-row">
            <div class="info-label">Nom complet :</div>
            <div class="info-value"><strong>{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Email :</div>
            <div class="info-value">{{ $reservation->guest_email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Téléphone :</div>
            <div class="info-value">{{ $reservation->guest_phone ?? 'N/A' }}</div>
        </div>
        @if($reservation->guest_address)
        <div class="info-row">
            <div class="info-label">Adresse :</div>
            <div class="info-value">{{ $reservation->guest_address }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Période de Location</div>
        <div class="info-row">
            <div class="info-label">Date d'arrivée :</div>
            <div class="info-value"><strong>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</strong> à {{ $reservation->villa->check_in_time ?? '16:00' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de départ :</div>
            <div class="info-value"><strong>{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</strong> avant {{ $reservation->villa->check_out_time ?? '10:00' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Durée du séjour :</div>
            <div class="info-value"><strong>{{ $reservation->number_of_nights }} nuit(s)</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Nombre de voyageurs :</div>
            <div class="info-value"><strong>{{ $reservation->number_of_guests }} personne(s)</strong></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Conditions Financières</div>
        <table>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Montant</th>
            </tr>
            <tr>
                <td>Prix de base ({{ $reservation->number_of_nights }} nuit(s))</td>
                <td style="text-align: right;">{{ number_format($reservation->base_price, 2, ',', ' ') }} €</td>
            </tr>
            @if($reservation->cleaning_fee > 0)
            <tr>
                <td>Frais de ménage</td>
                <td style="text-align: right;">{{ number_format($reservation->cleaning_fee, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            @if($reservation->service_fee > 0)
            <tr>
                <td>Frais de service</td>
                <td style="text-align: right;">{{ number_format($reservation->service_fee, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            @if($reservation->tourist_tax > 0)
            <tr>
                <td>Taxe de séjour</td>
                <td style="text-align: right;">{{ number_format($reservation->tourist_tax, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            <tr style="font-weight: bold; background-color: #F8F8F6;">
                <td>TOTAL</td>
                <td style="text-align: right;">{{ number_format($reservation->total_price, 2, ',', ' ') }} €</td>
            </tr>
        </table>
        <div style="margin-top: 15px;">
            <div class="info-row">
                <div class="info-label">Acompte :</div>
                <div class="info-value"><strong>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</strong> ({{ $reservation->deposit_percentage }}%)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Solde à régler :</div>
                <div class="info-value"><strong>{{ number_format($reservation->balance_amount, 2, ',', ' ') }} €</strong></div>
            </div>
            @if($reservation->deposit_guarantee > 0)
            <div class="info-row">
                <div class="info-label">Dépôt de garantie :</div>
                <div class="info-value"><strong>{{ number_format($reservation->deposit_guarantee, 2, ',', ' ') }} €</strong> (restituable après séjour)</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Conditions Générales</div>
        <p style="text-align: justify; margin-bottom: 10px;">
            Le présent contrat de location est soumis aux conditions générales de location de LUXÎLES. 
            Le locataire s'engage à respecter les règles de la villa et à la restituer dans l'état où il l'a trouvée.
        </p>
        <p style="text-align: justify; margin-bottom: 10px;">
            En cas d'annulation, les conditions de remboursement sont définies par la politique d'annulation applicable à la réservation.
        </p>
        <p style="text-align: justify;">
            Le locataire reconnaît avoir pris connaissance des informations relatives à la villa et accepte les conditions de location.
        </p>
    </div>

    @if($reservation->special_requests)
    <div class="section">
        <div class="section-title">Demandes Spéciales</div>
        <p style="text-align: justify;">{{ $reservation->special_requests }}</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div style="font-weight: bold; margin-bottom: 10px;">Le Locataire</div>
            <div class="signature-line">{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</div>
        </div>
        <div class="signature-box">
            <div style="font-weight: bold; margin-bottom: 10px;">LUXÎLES</div>
            <div class="signature-line">Représentant autorisé</div>
        </div>
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        @php
            $websiteDomain = parse_url(config('app.url'), PHP_URL_HOST);
            $companyName = \App\Helpers\SettingsHelper::get('company_name', 'BLUE SECRET');
        @endphp
        <p>{{ $companyName }} - Villas de Luxe aux Antilles | {{ $websiteDomain }}</p>
    </div>
</body>
</html>



