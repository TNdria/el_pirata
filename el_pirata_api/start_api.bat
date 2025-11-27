@echo off
echo ========================================
echo    EL PIRATA API - SERVEUR DE DEVELOPPEMENT
echo ========================================
echo.
echo Demarrage du serveur Laravel...
echo URL: http://localhost:8000
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.

C:\xampp\php\php.exe artisan serve --host=0.0.0.0 --port=8000

pause
