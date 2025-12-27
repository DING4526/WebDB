@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

:: ============================================================
::  WebDB 快速部署脚本 (Windows)
::  
::  此脚本可以独立运行，会自动克隆仓库并部署
::  用法: 双击运行或在命令行中运行
:: ============================================================

echo.
echo ============================================================
echo   WebDB 快速部署脚本
echo   适用于 Windows + XAMPP 环境
echo ============================================================
echo.

:: 配置
set "REPO_URL=https://github.com/DING4526/WebDB.git"
set "PROJECT_NAME=WebDB"

:: ============================================================
:: 检查必要工具
:: ============================================================
echo [检查环境]
echo.

:: 检查 Git
where git >nul 2>&1
if %errorlevel% neq 0 (
    echo [错误] 未找到 Git
    echo.
    echo 请先安装 Git:
    echo   1. 访问 https://git-scm.com/download/win
    echo   2. 下载并安装 Git for Windows
    echo   3. 重新运行此脚本
    echo.
    goto :error
)
echo   [✓] Git

:: 检测 XAMPP
set "XAMPP_PATH="
if exist "D:\xampp" set "XAMPP_PATH=D:\xampp"
if exist "C:\xampp" set "XAMPP_PATH=C:\xampp"

if not defined XAMPP_PATH (
    echo [错误] 未找到 XAMPP
    echo.
    echo 请先安装 XAMPP:
    echo   1. 访问 https://www.apachefriends.org/
    echo   2. 下载 XAMPP for Windows
    echo   3. 安装到 D:\xampp 或 C:\xampp
    echo   4. 重新运行此脚本
    echo.
    goto :error
)
echo   [✓] XAMPP ^(%XAMPP_PATH%^)

:: 添加 PHP 到 PATH
set "PATH=%XAMPP_PATH%\php;%PATH%"

:: 检查 PHP
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [错误] PHP 无法运行
    goto :error
)
echo   [✓] PHP

:: 检查 MySQL 是否运行
tasklist /fi "imagename eq mysqld.exe" | find /i "mysqld.exe" >nul
if %errorlevel% neq 0 (
    echo   [!] MySQL 未运行
    echo.
    echo   请启动 XAMPP 控制面板并启动 MySQL 服务
    echo   然后按任意键继续...
    pause >nul
)

echo.

:: ============================================================
:: 克隆项目
:: ============================================================
echo [克隆项目]
echo.

set "INSTALL_DIR=%XAMPP_PATH%\htdocs\%PROJECT_NAME%"

if exist "%INSTALL_DIR%" (
    echo   目录已存在: %INSTALL_DIR%
    set /p "USE_EXISTING=   使用现有目录？(Y/n): "
    if /i "!USE_EXISTING!"=="n" (
        rmdir /s /q "%INSTALL_DIR%"
        goto :do_clone
    )
    cd /d "%INSTALL_DIR%"
    goto :skip_clone
)

:do_clone
echo   正在克隆 %REPO_URL%...
git clone %REPO_URL% "%INSTALL_DIR%"
if %errorlevel% neq 0 (
    echo [错误] 克隆失败
    goto :error
)
cd /d "%INSTALL_DIR%"
echo   [✓] 克隆完成

:skip_clone
echo.

:: ============================================================
:: 安装 Composer（如果需要）
:: ============================================================
echo [检查 Composer]
echo.

where composer >nul 2>&1
if %errorlevel% neq 0 (
    if not exist "%XAMPP_PATH%\php\composer.phar" (
        echo   正在下载 Composer...
        powershell -Command "(New-Object Net.WebClient).DownloadFile('https://getcomposer.org/download/latest-stable/composer.phar', '%XAMPP_PATH%\php\composer.phar')"
        if %errorlevel% neq 0 (
            echo [错误] 下载 Composer 失败
            goto :error
        )
    )
    
    :: 创建 composer.bat
    echo @php "%XAMPP_PATH%\php\composer.phar" %%* > "%XAMPP_PATH%\php\composer.bat"
    set "PATH=%XAMPP_PATH%\php;%PATH%"
)

echo   [✓] Composer

echo.

:: ============================================================
:: 安装依赖
:: ============================================================
echo [安装依赖]
echo.

echo   配置镜像加速...
call composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ 2>nul

echo   安装 PHP 依赖（请耐心等待）...
call composer install --no-interaction --no-progress
if %errorlevel% neq 0 (
    echo [错误] 依赖安装失败
    goto :error
)
echo   [✓] 依赖安装完成

echo.

:: ============================================================
:: 初始化 Yii2
:: ============================================================
echo [初始化环境]
echo.

php init --env=Development --overwrite=All
if %errorlevel% neq 0 (
    echo [错误] 初始化失败
    goto :error
)
echo   [✓] 环境初始化完成

echo.

:: ============================================================
:: 配置数据库
:: ============================================================
echo [配置数据库]
echo.

set "DB_NAME=yii2advanced"
echo   数据库名: %DB_NAME%
echo.
echo   请在 phpMyAdmin (http://localhost/phpmyadmin) 中创建数据库:
echo   - 名称: %DB_NAME%
echo   - 字符集: utf8mb4_general_ci
echo.
set /p "CONTINUE=   已创建数据库？按 Enter 继续..."

echo.

:: ============================================================
:: 执行迁移
:: ============================================================
echo [数据库迁移]
echo.

echo   创建数据表...
php yii migrate --interactive=0
if %errorlevel% neq 0 (
    echo   [!] 迁移失败，请稍后手动执行: php yii migrate
) else (
    echo   [✓] 数据库迁移完成
)

echo.

:: ============================================================
:: 完成
:: ============================================================
echo ============================================================
echo   部署完成!
echo ============================================================
echo.
echo   访问地址:
echo     前台: http://localhost/%PROJECT_NAME%/frontend/web/
echo     后台: http://localhost/%PROJECT_NAME%/backend/web/
echo.
echo   项目目录: %INSTALL_DIR%
echo.
echo ============================================================
echo.

set /p "OPEN_BROWSER=打开浏览器？(Y/n): "
if /i not "%OPEN_BROWSER%"=="n" (
    start http://localhost/%PROJECT_NAME%/frontend/web/
)

goto :end

:error
echo.
echo 部署失败，请检查错误信息
echo.
pause
exit /b 1

:end
pause
exit /b 0
