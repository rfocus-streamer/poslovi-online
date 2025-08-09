<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online - Vratite se u igru!</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        h1 {
            font-size: 26px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            text-align: center;
            text-decoration: none;
            border-radius: 30px;
            display: inline-block;
            font-size: 16px;
            margin: 20px 0;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .button:hover {
            background-color: #3e8e41;
            transform: translateY(-2px);
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        .footer img {
            width: 180px;
            height: auto;
            margin-bottom: 15px;
        }
        .content {
            padding: 15px 0;
            line-height: 1.6;
        }
        .benefits {
            background-color: #f0f7ff;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .benefit-icon {
            color: #4CAF50;
            margin-right: 12px;
            font-size: 20px;
        }
        .offer {
            font-style: italic;
            color: #4CAF50;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="footer">
            <a href="https://poslovionline.com"><img src="https://poslovionline.com/images/logo.png" alt="Poslovi Online Logo" /></a>
        </div>

        <div class="content">
            <h1>Poštovani {{ $first_name }},</h1>

            <p>Primetili smo da ste neko vreme bili odsutni na platformi. U međuvremenu, mnogo se toga desilo, i sada je pravo vreme da se ponovo uključite!</p>

            <div class="benefits">
                <h3 style="margin-top: 0; color: #2c3e50;">Šta ste propustili dok ste bili odsutni:</h3>

                <div class="benefit-item">
                    <span class="benefit-icon">✓</span>
                    <span>Nova ponuda proizvoda i usluga koja može biti tačno ono što vam treba</span>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">✓</span>
                    <span>Novih kupaca i prodavaca koji su aktivni i ostvaruju poslovne uspehe</span>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">✓</span>
                    <span>Unapređena platforma sa novim funkcijama koje poboljšavaju iskustvo za sve korisnike</span>
                </div>
            </div>

            <p class="offer">Vratite se i iskoristite sve mogućnosti koje čekaju na vas!</p>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="button">Prijavite se sada</a>
            </div>

            @if(!empty($message))
                <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee;">
                    <p><strong>Dodatna poruka:</strong></p>
                    <p>{{ $message }}</p>
                </div>
            @endif

            <p>Ukoliko vam je potrebna pomoć ili imate bilo kakva pitanja, naš tim vam je uvek na raspolaganju.</p>
            <p style="text-align: center; ">
                    <a style="display: inline-block; padding: 5px 10px; font-size: 12px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;" href="{{ route('tickets.create') }}">Otvori tiket</a>
            </p>

            <p>Radujemo se vašem povratku,<br>
            <strong>Vaš Poslovi Online tim</strong></p>
        </div>


        <div class="footer">
            <p>© {{ date('Y') }} Poslovi Online. Sva prava zadržana.</p>
            <p>
                <a href="https://poslovionline.com/privacy-policy" style="color: #777; text-decoration: none;">Politika privatnosti</a> |
                <a href="https://poslovionline.com/terms" style="color: #777; text-decoration: none;">Uslovi korišćenja</a>
            </p>
        </div>
    </div>
</body>
</html>
