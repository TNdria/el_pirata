<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Lien de restauration de mot de passe</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #111111;
            color: #ffffff;
            margin: 0;
            padding: 20px;
            text-align: center;
        }


        .container {
            width: 100%;
            max-width: 100%;
            background-color: #111111;
            border: 1px solid #2b2b2b;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: auto;
        }

        .title {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .message {
            font-size: 14px;
            color: #9ca3af;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .btn-container {
            margin: 20px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            transition: background 0.3s ease;
            background-color: #373E4D;
            border: none;
        }

        .btn:hover {
            background-color: #EB0000;
        }

        .warning {
            font-size: 14px;
            color: #9ca3af;
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #9ca3af;
        }

        /* Adaptation pour mobile */
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            .btn {
                font-size: 14px;
                padding: 10px 20px;
            }

            .logo img {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="logo">
            <img src="{{ $app_url }}/images/logo1.jpeg" alt="El Pirata Logo" width="200"
                style="max-width: 100%; height: auto;">
        </div>


        <h1 class="title">Lien de restauration de mot de passe</h1>
        <p class="message">
            Ahoy moussaillon ! Il semble que vous ayez perdu la clé de votre coffre.
            Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe et reprendre la mer avec El Pirata.
        </p>

        <!-- ✅ Correction : Bouton cliquable sur iPhone -->
        <div class="btn-container">
            <a href="{{ $urlRecoveryPassword }}" class="btn" style="color: white" target="_blank"
                rel="noopener noreferrer">
                créer un nouveau mot de passe
            </a>
        </div>


        <div class="footer">
            <p>Cordialement,<br>
                🏴‍☠️ Le Capitaine et l'équipage d'El Pirata</p>
        </div>
    </div>
</body>

</html>
