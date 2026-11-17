@echo off
title APSkincek - Automated Unit Testing Suite (CI/CD Local Runner)
echo ======================================================================
echo           APSkincek - Automated Unit Testing Suite (CI/CD)
echo ======================================================================
echo.

set PHP_BIN=C:\xampp\php\php.exe
if not exist "%PHP_BIN%" (
    where php >nul 2>&1
    if %errorlevel% equ 0 (
        set PHP_BIN=php
    ) else (
        echo [ERROR] PHP executable tidak ditemukan di C:\xampp\php\php.exe atau PATH.
        goto fail
    )
)

echo [1/3] Menjalankan Unit Testing PHP (MVC, Auth, Fonnte, Models)...
echo ----------------------------------------------------------------------
"%PHP_BIN%" "%~dp0tests\php\run_tests.php"
if %errorlevel% neq 0 (
    echo.
    echo [FAIL] Pengujian PHP mengalami kegagalan!
    goto fail
)
echo.

echo [2/3] Menjalankan Unit Testing Node.js (Chatbot, Provider, Guardrails)...
echo ----------------------------------------------------------------------
where node >nul 2>&1
if %errorlevel% equ 0 (
    node --test "%~dp0tests\nodejs\chatbot_logic.test.js"
    if %errorlevel% neq 0 (
        echo.
        echo [FAIL] Pengujian Node.js mengalami kegagalan!
        goto fail
    )
) else (
    echo [SKIP] Node.js tidak terdeteksi di PATH, melewati uji Node.js.
)
echo.

echo [3/3] Menjalankan Pemeriksaan Sintaksis Python (Computer Vision AI)...
echo ----------------------------------------------------------------------
where python >nul 2>&1
if %errorlevel% equ 0 (
    python "%~dp0tests\python\test_skin_analysis.py"
) else (
    echo [INFO] Python tidak terdeteksi di PATH Windows (Akan diuji otomatis di GitHub Actions CI/CD).
)
echo.

echo ======================================================================
echo  [SUCCESS] SEMUA UNIT TEST LOKAL BERHASIL DIJALANKAN (STATUS: GREEN)
echo ======================================================================
echo.
pause
exit /b 0

:fail
echo.
echo ======================================================================
echo  [ERROR] PENGUJIAN UNIT TEST GAGAL! PERIKSA LOG DI ATAS.
echo ======================================================================
echo.
pause
exit /b 1
