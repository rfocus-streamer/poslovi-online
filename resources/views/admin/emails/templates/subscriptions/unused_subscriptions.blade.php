<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online - Podsetnik za pretplatu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-size: 14px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: left;
        }
        .footer img {
            width: 150px;
            height: auto;
        }
        .content {
            padding-top: 20px;
        }
        .message {
            margin-bottom: 20px;
        }
        .highlight {
            background-color: #fffde7;
            padding: 15px;
            border-left: 4px solid #ffd600;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="footer">
            <a href="https://poslovionline.com"><img src="https://poslovionline.com/images/logo.png" alt="Poslovi Online Logo" /></a>
        </div>

        <div class="content">
            <h1>Poštovani, {{ $first_name }} {{ $last_name }}!</h1>

            <div class="highlight">
                <p>Primetili smo da još uvek <strong>niste iskoristili svoju pretplatu</strong> za publikovanje gig-a (ponude) na našoj platformi.</p>
            </div>

            <p class="message">Vaša pretplata vam omogućava da:</p>
            <ul>
                <li>Postavljate ograničen broj ponuda</li>
                <li>Istaknete svoje ponude među prvima</li>
                <li>Povećate vidljivost vaših usluga</li>
                <li>Ostvarite veći broj poslova</li>
            </ul>

            <p>Kliknite na dugme ispod kako biste počeli da koristite svoju pretplatu i postavili prvu ponudu:</p>

            <p style="text-align: center;">
                <a href="{{ route('services.create') }}" class="button">Postavi ponudu</a>
            </p>

            <p>Ako već imate postavljene ponude, možete ih ažurirati i unaprediti:</p>

            <p style="text-align: center; ">
                <a style="display: inline-block; padding: 5px 10px; font-size: 12px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;" href="{{ route('services.index') }}">Tvoje ponude</a>
            </p>


            @if(!empty($message))
                <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee;">
                    <p><strong>Dodatna poruka:</strong></p>
                    <p>{{ $message }}</p>
                </div>
            @endif

            <p>Ukoliko vam je potrebna pomoć ili imate pitanja o korišćenju platforme, naš tim za podršku vam stoji na raspolaganju kroz tiket sistem.</p>
            <p style="text-align: center; ">
                    <a style="display: inline-block; padding: 5px 10px; font-size: 12px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;" href="{{ route('tickets.create') }}">Otvori tiket</a>
            </p>

            <p>Pozdrav, <br> Tim <strong>Poslovi Online</strong></p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Poslovi Online. Sva prava zadržana.</p>
            <p>
                <a href="https://poslovionline.com/privacy-policy">Politika privatnosti</a> |
                <a href="https://poslovionline.com/terms">Uslovi korišćenja</a>
            </p>
        </div>
    </div>
</body>
</html>
