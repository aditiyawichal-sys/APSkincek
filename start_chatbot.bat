@echo off
title APSkincek Chatbot Server
echo ========================================================
echo         Menjalankan Server Chatbot APSkincek
echo ========================================================
echo.
cd /d "%~dp0SkinCheck\nodejs-chatbot"
if not exist "node_modules" (
    echo Menginstall dependencies...
    call npm install
)
echo Menjalankan chatbot server pada port 3000...
node server.js
pause
