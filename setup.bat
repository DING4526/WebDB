@echo off
setlocal EnableExtensions
chcp 65001 >nul

REM =========================================
REM WebDB 一键部署入口
REM 1. git clone
REM 2. cd WebDB
REM 3. 执行 deploy.bat
REM =========================================

set "REPO_URL=https://github.com/DING4526/WebDB.git"
set "PROJECT_DIR=WebDB"

echo.
echo =========================================
echo   WebDB 一键部署（clone + deploy）
echo =========================================
echo.

REM 1) 检查是否已存在目录
if exist "%PROJECT_DIR%" (
    echo [!] 已检测到目录 "%PROJECT_DIR%"
    echo     将直接进入并执行 deploy.bat
) else (
    echo [1/3] 正在克隆仓库...
    git clone "%REPO_URL%"
    if errorlevel 1 goto :error
    echo [?] 仓库克隆完成
)

echo.
echo [2/3] 进入项目目录...
cd /d "%PROJECT_DIR%"
if errorlevel 1 goto :error

REM 3) 检查 deploy.bat 是否存在
if not exist "deploy.bat" (
    echo [错误] 未找到 deploy.bat
    echo        请确认仓库中存在 deploy.bat
    goto :error
)

echo.
echo [3/3] 执行 deploy.bat ...
call deploy.bat
if errorlevel 1 goto :error

echo.
echo =========================================
echo   全流程完成！
echo =========================================
echo.
pause
exit /b 0

:error
echo.
echo =========================================
echo   部署失败，请检查上面的错误信息
echo =========================================
echo.
pause
exit /b 1
