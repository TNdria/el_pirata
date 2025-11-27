@echo off
echo ========================================
echo    EL PIRATA - SERVEURS DE DEVELOPPEMENT
echo ========================================
echo.
echo Demarrage des serveurs...
echo API: http://localhost:8000
echo Frontend: http://localhost:3000
echo.
echo Appuyez sur Ctrl+C pour arreter tous les serveurs
echo.

start "El Pirata API" cmd /k "cd /d %~dp0 && C:\xampp\php\php.exe artisan serve --host=0.0.0.0 --port=8000"
timeout /t 3 /nobreak >nul
start "El Pirata Frontend" cmd /k "cd /d %~dp0 && npm run dev"

echo Serveurs demarres!
echo.
pause
