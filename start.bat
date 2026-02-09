@echo off
REM PrestaShop Docker Compose Startup Script
REM This script builds and starts the PrestaShop development environment

echo ================================
echo Starting PrestaShop Development
echo ================================
echo.

echo Building Docker images...
docker compose build --pull --no-cache
if errorlevel 1 (
    echo Error: Failed to build Docker images
    pause
    exit /b 1
)

echo.
echo Starting Docker containers...
docker compose up --detach --force-recreate --remove-orphans
if errorlevel 1 (
    echo Error: Failed to start Docker containers
    pause
    exit /b 1
)

echo.
echo ================================
echo PrestaShop is starting up!
echo ================================
echo.
echo Please wait a moment for the services to initialize...
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
