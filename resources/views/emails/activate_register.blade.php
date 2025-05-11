<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online - Potvrda registracije</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="footer">
            <a href="https://poslovionline.com"><img src="https://poslovionline.com/images/logo.png" alt="Poslovi Online Logo" /></a>
        </div>

        <div class="content">
            <h1>Dobrodošli, {{ $first_name }} {{ $last_name }}!</h1>

            <p class="message">Uspešno ste se registrovali na <strong>Poslovi Online</strong> platformi.</p>

            <p>Molimo vas da potvrdite svoju email adresu klikom na dugme ispod:</p>

            <p style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Potvrdi email adresu</a>
            </p>

            <p>Ako niste vi kreirali ovaj nalog, možete ignorisati ovu poruku.</p>

            <p>Hvala i dobrodošli!<br>Tim <strong>Poslovi Online</strong></p>
        </div>
    </div>
</body>
</html>
