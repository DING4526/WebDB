@echo off
setlocal EnableExtensions EnableDelayedExpansion

REM ============================================================
REM  WebDB One-Click Setup (Stable Distribution Version)
REM  - ASCII-only output (no encoding issues)
REM  - Tries silent install, falls back to GUI install
REM ============================================================

REM Force cmd.exe
if /i not "%ComSpec%"=="%SystemRoot%\System32\cmd.exe" (
  "%SystemRoot%\System32\cmd.exe" /d /c "%~f0" %*
  exit /b
)

REM Elevate admin
net session >nul 2>&1
if %errorlevel% neq 0 (
  powershell -NoProfile -ExecutionPolicy Bypass -Command ^
    "Start-Process -FilePath '%~f0' -Verb RunAs"
  exit /b
)

REM Config
set "XAMPP_EXE=xampp-windows-x64-8.2.12-0-VS16-installer.exe"
set "XAMPP_DIR=D:\XAMPP"

set "REPO_URL=https://github.com/DING4526/WebDB.git"
set "PROJECT_NAME=WebDB"
set "HTDOCS_DIR=%XAMPP_DIR%\htdocs"

set "DB_NAME=yii2advanced"
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_USER=root"
set "DB_PASS="
set "SEED_SQL=seed.sql"

cd /d "%~dp0" || goto :error

echo.
echo =========================================
echo   WebDB One-Click Setup
echo   Target XAMPP: %XAMPP_DIR%
echo =========================================
echo.

REM Check Git
where git >nul 2>&1
if %errorlevel% neq 0 (
  echo [ERROR] Git not found in PATH. Install Git for Windows and retry.
  goto :error
)

REM Install XAMPP if missing
if exist "%XAMPP_DIR%\xampp-control.exe" (
  echo [1/7] XAMPP detected.
) else (
  echo [1/7] XAMPP not found. Installing...

  if not exist "%~dp0%XAMPP_EXE%" (
    echo [ERROR] XAMPP installer not found next to this script:
    echo         %~dp0%XAMPP_EXE%
    goto :error
  )

  REM Try silent install first
  start "" /wait "%~dp0%XAMPP_EXE%" /VERYSILENT /SUPPRESSMSGBOXES /NORESTART /DIR="%XAMPP_DIR%"

  REM If silent install did not place files, fall back to GUI
  if not exist "%XAMPP_DIR%\xampp-control.exe" (
    echo [WARN ] Silent install may not be supported. Opening GUI installer...
    echo        Please install to: %XAMPP_DIR%
    start "" "%~dp0%XAMPP_EXE%"
    echo.
    echo After installation finishes, run this script again.
    goto :error
  )
)

echo [OK] XAMPP ready.
echo.

REM Start services
echo [2/7] Starting Apache/MySQL...
if exist "%XAMPP_DIR%\xampp_start.exe" (
  start "" "%XAMPP_DIR%\xampp_start.exe"
) else (
  start "" "%XAMPP_DIR%\xampp-control.exe"
)
timeout /t 5 >nul

REM Check htdocs
echo [3/7] Checking htdocs...
if not exist "%HTDOCS_DIR%\" (
  echo [ERROR] htdocs not found: %HTDOCS_DIR%
  goto :error
)

REM Clone repo
echo [4/7] Getting source...
cd /d "%HTDOCS_DIR%" || goto :error

if exist "%PROJECT_NAME%\" (
  echo [INFO] Project folder exists. Skip clone.
) else (
  git clone "%REPO_URL%" "%PROJECT_NAME%"
  if errorlevel 1 (
    echo [ERROR] git clone failed. Check network access to GitHub.
    goto :error
  )
)

REM Yii2 init
echo [5/7] Running Yii2 init...
cd /d "%HTDOCS_DIR%\%PROJECT_NAME%" || goto :error
set "PHP_EXE=%XAMPP_DIR%\php\php.exe"
if not exist "%PHP_EXE%" (
  echo [ERROR] PHP not found: %PHP_EXE%
  goto :error
)
"%PHP_EXE%" init --env=Development --overwrite=All
if errorlevel 1 (
  echo [ERROR] Yii2 init failed.
  goto :error
)

REM DB create + import
echo [6/7] Creating database and importing seed.sql...
set "MYSQL_EXE=%XAMPP_DIR%\mysql\bin\mysql.exe"
if not exist "%MYSQL_EXE%" (
  echo [ERROR] MySQL client not found: %MYSQL_EXE%
  goto :error
)

set "SQL_CREATE=CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

if "%DB_PASS%"=="" (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -e "%SQL_CREATE%"
) else (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -p"%DB_PASS%" -e "%SQL_CREATE%"
)
if errorlevel 1 (
  echo [ERROR] DB create failed. Is MySQL running? Check credentials.
  goto :error
)

if not exist "%SEED_SQL%" (
  echo [ERROR] seed.sql not found: %CD%\%SEED_SQL%
  goto :error
)

if "%DB_PASS%"=="" (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" "%DB_NAME%" < "%SEED_SQL%"
) else (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -p"%DB_PASS%" "%DB_NAME%" < "%SEED_SQL%"
)
if errorlevel 1 (
  echo [ERROR] Import failed. Possibly conflicts with existing tables.
  goto :error
)

echo.
echo =========================================
echo   SUCCESS
echo =========================================
echo Frontend: http://localhost/WebDB/frontend/web/
echo Backend : http://localhost/WebDB/backend/web/
echo.
pause
exit /b 0

:error
echo.
echo =========================================
echo   FAILED
echo =========================================
echo.
pause
exit /b 1
