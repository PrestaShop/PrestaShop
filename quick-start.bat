@echo off
REM PrestaShop Docker Compose Quick Start Script
REM This script quickly starts existing containers without rebuilding

echo ================================
echo Quick Starting PrestaShop
echo ================================
echo.

echo Starting Docker containers...
docker compose up --detach
if errorlevel 1 (
    echo Error: Failed to start Docker containers
    echo.
    echo Tip: If containers don't exist, run start.bat first to build them.
    pause
    exit /b 1
)

echo.
echo ================================
echo PrestaShop started successfully!
echo ================================
echo.
echo Access your PrestaShop installation at:
echo   Frontend:  http://localhost:8001
echo   Backend:   http://localhost:8001/admin-dev
echo   MailDev:   http://localhost:1080
echo.
echo Default Admin Credentials:
echo   Email:     demo@prestashop.com
echo   Password:  Correct Horse Battery Staple
echo.
echo To view logs, run: docker compose logs --follow
echo To stop, run: stop.bat
echo.
pause
