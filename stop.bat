@echo off
REM PrestaShop Docker Compose Stop Script
REM This script stops the PrestaShop development environment

echo ================================
echo Stopping PrestaShop Development
echo ================================
echo.

echo Stopping Docker containers...
docker compose down --remove-orphans
if errorlevel 1 (
    echo Error: Failed to stop Docker containers
    pause
    exit /b 1
)

echo.
echo ================================
echo PrestaShop stopped successfully!
echo ================================
echo.
echo To start again, run: start.bat
echo.
pause
