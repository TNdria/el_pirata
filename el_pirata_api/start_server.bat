@echo off
echo 🏴‍☠️  DÉMARRAGE SERVEUR EL PIRATA API
echo ====================================
echo.

echo 📦 Vérification de l'environnement...
C:\xampp\php\php.exe --version
echo.

echo 🗄️  Vérification de la base de données...
C:\xampp\php\php.exe artisan migrate:status
echo.

echo 🚀 Démarrage du serveur de développement...
echo Serveur disponible sur: http://127.0.0.1:8000
echo.
echo Endpoints disponibles:
echo   • GET  /api/health - Santé de l'API
echo   • GET  /api/metrics - Métriques de performance
echo   • GET  /api/stats - Statistiques générales
echo   • POST /api/login - Connexion utilisateur
echo   • POST /api/register - Inscription utilisateur
echo   • GET  /api/user/tickets - Tickets utilisateur
echo   • POST /api/user/tickets - Créer un ticket
echo   • GET  /api/user/refunds - Remboursements utilisateur
echo   • POST /api/user/refunds - Demander un remboursement
echo   • GET  /api/user/vip-codes - Codes VIP utilisateur
echo   • POST /api/user/vip-codes/validate - Valider un code VIP
echo.
echo Appuyez sur Ctrl+C pour arrêter le serveur
echo.

C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
