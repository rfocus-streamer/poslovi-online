<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online - Obaveštenje</title>
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
            width: 150px; /* Manja veličina loga */
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
            <h1>Poštovani {{ $first_name }} {{ $last_name }},</h1>

            <div class="message-box">
                <p>Imate novih nepročitanih poruka na našoj platformi, molimo vas da proverite svoj inbox za nove poruke.</p>
            </div>

            <p>Kliknite na dugme ispod da biste pregledali poruke:</p>

            <p style="text-align: center;">
                <a href="{{ route('messages.index') }}" class="button">Pregledaj poruke</a>
            </p>

            @if(!empty($message))
                <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee;">
                    <p><strong>Dodatna poruka:</strong></p>
                    <p>{{ $message }}</p>
                </div>
            @endif

            <div class="contact-info">
                <p>Ako imate bilo kakvih pitanja, slobodno nas kontaktirajte kroz tiket sistem</p>
                <p style="text-align: center; ">
                    <a style="display: inline-block; padding: 5px 10px; font-size: 12px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;" href="{{ route('tickets.create') }}">Otvori tiket</a>
                </p>
            </div>
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
