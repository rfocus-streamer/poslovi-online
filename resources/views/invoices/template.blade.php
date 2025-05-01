<!DOCTYPE html>
<html>
<head>
    <title>Račun #{{ $invoice->number }}</title>
    <style>
         @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/DejaVuSans.ttf') }}) format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            position: absolute; /* Postavlja footer na dno */
            bottom: 0; /* Poravnava footer na dnu */
            width: 100%; /* Puni širinu stranice */
            text-align: center;
            margin-top: 30px;
        }

        .footer hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .footer p {
            font-size: 14px;
            margin: 10px 0;
        }

        .footer a {
            text-decoration: none;
            color: #007BFF;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer i {
            margin-right: 5px;
        }


       .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
        }

        .payment-method {
            display: flex;
            justify-content: space-between; /* Raspoređuje "Način plaćenja" i "Total" sa prostora između */
            align-items: center; /* Poravnava vertikalno na sredinu */
            margin-top: 2px;
        }

        .payment-method .left {
            text-align: left;
            flex: 1; /* Da bi zauzimao sav prostor levo */
        }

        .payment-method .right {
            text-align: right;
            font-weight: bold;
            flex-shrink: 0; /* Da ne bi smanjivao veličinu ako je potrebno */
        }

    </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<div>
    <img src="{{ public_path('images/logo.png') }}" alt="Poslovi Online Logo" width="160" style="display: block;">
</div>

<table style="width: 100%;">
  <tr>
    <!-- Levi deo -->
    <td style="width: 50%; vertical-align: top;">
      <span style="font-weight: bold; display: inline-block; margin-bottom: 5px;">POSLOVI ONLINE LTD</span><br>
      <span>
        71-75 Shelton Street<br>
        Convent Garden London<br>
        WC2H 9JQ<br>
        UNITED KINGDOM
      </span>
    </td>

    <!-- Desni deo, poravnat levo -->
    <td style="width: 30%; vertical-align: top; text-align: left;">
      <span style="font-weight: bold; display: inline-block; margin-bottom: 5px;">Platilac:</span><br>
      <span>
        {{ $invoice->client_info['name'] }}<br>
        {{ $invoice->client_info['address'] }}<br>
        {{ $invoice->client_info['city'] }}<br>
        {{ $invoice->client_info['country'] }}
      </span>
    </td>
  </tr>
</table>

    <div class="invoice-details">
        <p>Račun #{{ $invoice->number }} | {{ $invoice->issue_date->format('d.m.Y') }} | {{ ucfirst($invoice->status) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Opis</th>
                <th>Obračunski period</th>
                <th style="text-align: center;">Količina</th>
                <th style="text-align: center;">Iznos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['billing_period'] }}</td>
                    <td style="text-align: center;">{{ $item['quantity'] ?? '' }}</td>
                    <td>{{ $item['amount'] ? '€'.number_format($item['amount'], 2, ',', '.') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="payment-method">
        <div class="right">
            <p class="total-row">Ukupno: €{{ number_format($invoice->total, 2, ',', '.') }}</p>
        </div>
        <div class="left">
            <p>Način plaćenja: {{ $invoice->payment_method }}</p>
        </div>
    </div>

    <div class="footer">
        <hr> <!-- Linija -->
        <p>Kontaktirajte nas: <a href="https://poslovionline.com/kontakt" target="_blank">
            <i class="fa fa-envelope"></i> https://poslovionline.com/kontakt</a>
        </p>
    </div>

</body>
</html>
