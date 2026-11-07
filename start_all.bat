@echo off
title APSkincek - Jalankan Semua Layanan
echo ========================================================
echo       MENJALANKAN SEMUA LAYANAN APSKINCEK & CHATBOT
echo ========================================================
echo.

:: 1. Memeriksa XAMPP Apache dan MySQL
echo [1/3] Memeriksa layanan Apache dan MySQL...
netstat -ano | findstr :80 >nul
if %errorlevel% neq 0 (
    echo Mengaktifkan Apache...
    if exist "c:\xampp\apache_start.bat" (
        start "" /b "c:\xampp\apache_start.bat"
    )
) else (
    echo - Apache sudah aktif (Port 80).
)

netstat -ano | findstr :3306 >nul
if %errorlevel% neq 0 (
    echo Mengaktifkan MySQL...
    if exist "c:\xampp\mysql_start.bat" (
        start "" /b "c:\xampp\mysql_start.bat"
    )
) else (
    echo - MySQL sudah aktif (Port 3306).
)

:: 2. Menjalankan Chatbot Server di jendela tersendiri
echo.
echo [2/3] Menjalankan Server Chatbot (Node.js)...
start "APSkincek Chatbot Server (Port 3000)" cmd /k "cd /d %~dp0SkinCheck\nodejs-chatbot && node server.js"

:: 3. Membuka Aplikasi di Browser
echo.
echo [3/3] Membuka aplikasi APSkincek di browser...
timeout /t 2 >nul
start http://localhost/APSkincek/

echo.
echo ========================================================
echo   Semua layanan berhasil dijalankan!
echo   - Web Aplikasi: http://localhost/APSkincek/
echo   - Chatbot API : http://localhost:3000/api/chat
echo ========================================================
timeout /t 5
