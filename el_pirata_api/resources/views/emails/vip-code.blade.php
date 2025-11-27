<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code VIP El Pirata</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 2em;
            font-weight: bold;
            color: #8B4513;
            margin-bottom: 10px;
        }
        .vip-badge {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #8B4513;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            margin: 20px 0;
        }
        .code-container {
            background-color: #f8f9fa;
            border: 2px dashed #8B4513;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 10px;
        }
        .vip-code {
            font-size: 2em;
            font-weight: bold;
            color: #8B4513;
            letter-spacing: 3px;
            margin: 10px 0;
        }
        .discount {
            font-size: 1.2em;
            color: #28a745;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #8B4513;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏴‍☠️ El Pirata</div>
            <h1>Félicitations {{ $user->name }} !</h1>
        </div>

        <p>Excellente nouvelle ! Vous avez terminé à la <strong>{{ $rank }}ème place</strong> de la chasse au trésor <strong>"{{ $hunting->title }}"</strong> !</p>

        <div class="vip-badge">
            🏆 CODE VIP GAGNÉ
        </div>

        <p>En tant que l'un des 9 meilleurs joueurs, vous recevez un <strong>code VIP exclusif</strong> qui vous donne droit à une réduction sur votre prochaine aventure !</p>

        <div class="code-container">
            <h3>Votre Code VIP :</h3>
            <div class="vip-code">{{ $vipCode->code }}</div>
            <div class="discount">{{ $vipCode->percent_off }}% de réduction</div>
            <p><small>Valide jusqu'au {{ $vipCode->valid_until->format('d/m/Y') }}</small></p>
        </div>

        <p>Utilisez ce code lors de votre prochaine inscription à une chasse au trésor El Pirata pour bénéficier de votre réduction !</p>

        <div style="text-align: center;">
            <a href="{{ $app_url }}/chasses-tresors" class="btn">Découvrir les prochaines chasses</a>
        </div>

        <p>Merci d'avoir participé à cette aventure avec nous. Nous espérons vous revoir bientôt pour de nouvelles énigmes palpitantes !</p>

        <div class="footer">
            <p><strong>L'équipe El Pirata</strong></p>
            <p>À votre service, moussaillon ! 🏴‍☠️</p>
            <p><small>Ce code est personnel et non transférable. Une seule utilisation par code.</small></p>
        </div>
    </div>
</body>
</html>

