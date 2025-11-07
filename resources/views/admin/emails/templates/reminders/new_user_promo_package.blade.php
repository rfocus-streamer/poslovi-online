<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online - Poklon za Vas!</title>
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
                <p>Hvala Vam što ste se registrovali na platformi <strong>Poslovi Online</strong> i dobrodošli u našu zajednicu!</p>
                <p>Imamo sjajnu vest za Vas — dobili ste <strong>mesec dana besplatnog korišćenja</strong> svih funkcionalnosti naše platforme.</p>
            </div>

            <p class="message">Da biste odmah iskoristili svoj poklon, podsećamo Vas da postavite svoju ponudu. Na taj način ćete povećati vidljivost i doći do potencijalnih saradnika i klijenata.</p>

            <p class="message">👉 <a href="{{ route('packages.index') }}" class="button">Kliknite OVDE</a> i aktivirajte svoj mesec dana besplatnog korišćenja!</p>

            <p>Ne propustite priliku da iskoristite sve pogodnosti platforme i unapredite svoje poslovanje.</p>

            <p>Srdačan pozdrav, <br> Vaš <strong>Poslovi Online tim</strong></p>
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
