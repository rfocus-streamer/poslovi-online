<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online - Nova platforma za digitalne usluge!</title>
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
        .emoji {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="footer">
            <a href="https://poslovionline.com"><img src="https://poslovionline.com/images/logo.png" alt="Poslovi Online Logo" /></a>
        </div>

        <div class="content">
            <h1>Ćao drugari <span class="emoji">👋</span></h1>

            <p>Želim da vam predstavim novu platformu koja povezuje kupce i prodavce digitalnih usluga, odnosno onih usluga koje ne zahtevaju fizički kontakt.</p>

            <div class="benefits">
                <h3 style="margin-top: 0; color: #2c3e50;">Za koga je platforma?</h3>

                <div class="benefit-item">
                    <span class="benefit-icon">✓</span>
                    <span>Ako pišete, dizajnirate, izrađujete planove ishrane ili treninga</span>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">✓</span>
                    <span>Ako programirate, prevodite, izrađujete natalne karte</span>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">✓</span>
                    <span>Ako se bavite knjigovodstvom, marketingom, držite kurseve ili nudite bilo kakve digitalne usluge</span>
                </div>
            </div>

            <p>Osim novih klijenata, čeka vas i <strong>affiliate zarada</strong>. Svaki put kad preporučite novog prodavca, dobijate <strong>70% od njegove prve članarine</strong>. Osim što možete zarađivati od svojih usluga, možete kreirati i dodatni pasivan prihod.</p>

            <p class="offer">Ovde možete naći nove klijente i zaraditi!</p>

            <div style="text-align: center;">
                <a href="https://poslovionline.com/register?affiliateCode=pS9pHBkvHX" class="button">REGISTRUJ SE</a>
            </div>

            <p>Želim vam puno uspešnih saradnji.</p>

            <p>Srdačan pozdrav,<br>
            <strong>Ivan Mirović</strong></p>
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
