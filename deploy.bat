@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

:: ============================================================
::  WebDB 一键部署脚本 (Windows)
::  用法: 双击运行 deploy.bat 或在命令行中运行
:: ============================================================

echo.
echo ============================================================
echo   WebDB 一键部署脚本
echo   适用于 Windows + XAMPP 环境
echo ============================================================
echo.

:: 检查是否以管理员权限运行
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo [警告] 建议以管理员身份运行此脚本以避免权限问题
    echo.
)

:: ============================================================
:: 步骤 0: 配置变量
:: ============================================================
set "REPO_URL=https://github.com/DING4526/WebDB.git"
set "PROJECT_NAME=WebDB"
set "DB_NAME=yii2advanced"
set "DB_USER=root"
set "DB_PASS="

:: 检测 XAMPP 路径
set "XAMPP_PATH="
if exist "D:\xampp\php\php.exe" set "XAMPP_PATH=D:\xampp"
if exist "C:\xampp\php\php.exe" set "XAMPP_PATH=C:\xampp"

:: ============================================================
:: 步骤 1: 检查必要工具
:: ============================================================
echo [步骤 1/7] 检查必要工具...
echo.

:: 检查 Git
where git >nul 2>&1
if %errorlevel% neq 0 (
    echo [错误] 未找到 Git，请先安装 Git
    echo        下载地址: https://git-scm.com/download/win
    goto :error
)
echo   [✓] Git 已安装

:: 检查 PHP
where php >nul 2>&1
if %errorlevel% neq 0 (
    if defined XAMPP_PATH (
        echo   [!] PHP 未在 PATH 中，尝试使用 XAMPP 的 PHP...
        set "PATH=!XAMPP_PATH!\php;!PATH!"
    ) else (
        echo [错误] 未找到 PHP，请先安装 XAMPP 并将 PHP 添加到环境变量
        echo        XAMPP 下载地址: https://www.apachefriends.org/
        echo        安装后请将 D:\xampp\php 添加到系统 PATH 环境变量
        goto :error
    )
)
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [错误] PHP 无法正常运行
    goto :error
)
echo   [✓] PHP 已安装

:: 检查 Composer
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo   [!] Composer 未安装，正在下载...
    
    :: 下载 Composer
    if not exist "%TEMP%\composer-setup.php" (
        echo       正在下载 Composer 安装程序...
        powershell -Command "(New-Object Net.WebClient).DownloadFile('https://getcomposer.org/installer', '%TEMP%\composer-setup.php')"
        if %errorlevel% neq 0 (
            echo [错误] 下载 Composer 失败
            goto :error
        )
    )
    
    :: 安装 Composer
    php "%TEMP%\composer-setup.php" --install-dir="%USERPROFILE%" --filename=composer
    if %errorlevel% neq 0 (
        echo [错误] 安装 Composer 失败
        goto :error
    )
    
    set "PATH=%USERPROFILE%;%PATH%"
    echo   [✓] Composer 已安装到 %USERPROFILE%\composer
) else (
    echo   [✓] Composer 已安装
)

echo.

:: ============================================================
:: 步骤 2: 选择安装目录
:: ============================================================
echo [步骤 2/7] 选择安装目录...
echo.

:: 获取当前脚本所在目录
set "SCRIPT_DIR=%~dp0"

:: 检查是否已经在项目目录中（脚本在项目根目录运行）
if exist "%SCRIPT_DIR%composer.json" (
    echo   检测到脚本在项目目录中运行
    set "INSTALL_DIR=%SCRIPT_DIR%"
    set "SKIP_CLONE=1"
    cd /d "%INSTALL_DIR%"
    goto :skip_clone
)

:: 默认安装到 XAMPP htdocs
if defined XAMPP_PATH (
    set "DEFAULT_DIR=!XAMPP_PATH!\htdocs"
) else (
    set "DEFAULT_DIR=%CD%"
)

echo   默认安装目录: %DEFAULT_DIR%\%PROJECT_NAME%
echo.
set /p "CUSTOM_DIR=   请输入安装目录 (直接回车使用默认): "

if "%CUSTOM_DIR%"=="" (
    set "INSTALL_DIR=%DEFAULT_DIR%\%PROJECT_NAME%"
) else (
    set "INSTALL_DIR=%CUSTOM_DIR%\%PROJECT_NAME%"
)

echo.
echo   安装目录: %INSTALL_DIR%

:: 检查目录是否已存在
if exist "%INSTALL_DIR%" (
    echo.
    echo   [警告] 目录已存在: %INSTALL_DIR%
    set /p "OVERWRITE=   是否删除并重新安装？(y/N): "
    if /i "!OVERWRITE!"=="y" (
        echo   正在删除旧目录...
        rmdir /s /q "%INSTALL_DIR%"
    ) else (
        echo   [!] 跳过克隆，使用现有目录
        cd /d "%INSTALL_DIR%"
        set "SKIP_CLONE=1"
        goto :skip_clone
    )
)

echo.

:: ============================================================
:: 步骤 3: 克隆仓库
:: ============================================================
echo [步骤 3/7] 克隆仓库...
echo.

echo   正在从 %REPO_URL% 克隆...
git clone %REPO_URL% "%INSTALL_DIR%"
if %errorlevel% neq 0 (
    echo [错误] 克隆仓库失败
    goto :error
)

cd /d "%INSTALL_DIR%"
echo   [✓] 仓库克隆完成

:skip_clone
echo.

:: ============================================================
:: 步骤 4: 安装 Composer 依赖
:: ============================================================
echo [步骤 4/7] 安装 Composer 依赖...
echo.

:: 配置国内镜像加速（可选，针对中国大陆用户）
set /p "USE_MIRROR=   是否使用国内镜像加速？(适合中国大陆) (y/N): "
if /i "%USE_MIRROR%"=="y" (
    echo   配置 Composer 镜像加速...
    call composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ 2>nul
)

echo   正在安装依赖（可能需要几分钟）...
call composer install --no-interaction
if %errorlevel% neq 0 (
    echo [错误] Composer 安装依赖失败
    echo        请检查网络连接或尝试手动运行: composer install
    goto :error
)
echo   [✓] 依赖安装完成

echo.

:: ============================================================
:: 步骤 5: 初始化 Yii2 环境
:: ============================================================
echo [步骤 5/7] 初始化 Yii2 环境...
echo.

echo   正在初始化开发环境...
php init --env=Development --overwrite=All
if %errorlevel% neq 0 (
    echo [错误] Yii2 环境初始化失败
    goto :error
)
echo   [✓] 环境初始化完成

echo.

:: ============================================================
:: 步骤 6: 配置数据库
:: ============================================================
echo [步骤 6/7] 配置数据库...
echo.

:: 询问数据库配置
echo   数据库配置信息（默认值适用于 XAMPP）:
echo.
set /p "DB_NAME=   数据库名 [%DB_NAME%]: " || set "DB_NAME=yii2advanced"
set /p "DB_USER=   数据库用户名 [%DB_USER%]: " || set "DB_USER=root"
set /p "DB_PASS=   数据库密码 [空]: "
set /p "DB_HOST=   数据库主机 [localhost]: " || set "DB_HOST=localhost"
set /p "DB_PORT=   数据库端口 [3306]: " || set "DB_PORT=3306"

:: 设置默认值
if "%DB_HOST%"=="" set "DB_HOST=localhost"
if "%DB_PORT%"=="" set "DB_PORT=3306"

:: 更新数据库配置文件
set "DB_CONFIG_FILE=%INSTALL_DIR%\common\config\main-local.php"
if not exist "%DB_CONFIG_FILE%" (
    set "DB_CONFIG_FILE=common\config\main-local.php"
)

echo.
echo   正在更新数据库配置...

:: 使用 PowerShell 更新配置文件（转义特殊字符）
powershell -Command ^
    "$dbName = '%DB_NAME%' -replace '([\\[\\]\\^\\$\\.\\|\\?\\*\\+\\(\\)])', '\\$1'; ^
    $dbUser = '%DB_USER%' -replace '([\\[\\]\\^\\$\\.\\|\\?\\*\\+\\(\\)])', '\\$1'; ^
    $dbPass = '%DB_PASS%' -replace \"'\", \"''\"; ^
    $dbHost = '%DB_HOST%'; ^
    $dbPort = '%DB_PORT%'; ^
    $content = Get-Content '%DB_CONFIG_FILE%' -Raw; ^
    $content = $content -replace \"'dsn' => 'mysql:host=[^;]+;port=[^;]+;dbname=[^']+'\", \"'dsn' => 'mysql:host=$dbHost;port=$dbPort;dbname=$dbName'\"; ^
    $content = $content -replace \"'username' => '[^']*'\", \"'username' => '$dbUser'\"; ^
    $content = $content -replace \"'password' => '[^']*'\", \"'password' => '$dbPass'\"; ^
    Set-Content '%DB_CONFIG_FILE%' $content"

echo   [✓] 数据库配置已更新

echo.
echo   [重要] 请确保在 phpMyAdmin 中创建数据库: %DB_NAME%
echo          字符集: utf8mb4_general_ci
echo.
set /p "DB_READY=   数据库是否已创建？(Y/n): "
if /i "%DB_READY%"=="n" (
    echo.
    echo   请先创建数据库，然后重新运行此脚本
    echo   或手动执行: php yii migrate
    goto :manual_migrate
)

echo.

:: ============================================================
:: 步骤 7: 执行数据库迁移
:: ============================================================
echo [步骤 7/7] 执行数据库迁移...
echo.

echo   正在创建数据库表...
php yii migrate --interactive=0
if %errorlevel% neq 0 (
    echo [警告] 数据库迁移失败
    echo        可能原因：数据库未创建或连接配置错误
    echo        请手动执行: php yii migrate
    goto :manual_migrate
)
echo   [✓] 数据库迁移完成

:manual_migrate
echo.

:: ============================================================
:: 完成
:: ============================================================
echo ============================================================
echo   部署完成！
echo ============================================================
echo.
echo   项目目录: %INSTALL_DIR%
echo.
echo   访问地址（使用 XAMPP Apache）:
echo   - 前台: http://localhost/%PROJECT_NAME%/frontend/web/
echo   - 后台: http://localhost/%PROJECT_NAME%/backend/web/
echo.
echo   或者使用 PHP 内置服务器:
echo   - 前台: cd frontend/web ^&^& php -S localhost:8080
echo   - 后台: cd backend/web ^&^& php -S localhost:8081
echo.
echo   其他有用命令:
echo   - 数据库迁移: php yii migrate
echo   - 查看帮助:   php yii help
echo.
echo ============================================================
echo.

set /p "START_SERVER=是否启动 PHP 内置服务器？(y/N): "
if /i "%START_SERVER%"=="y" (
    echo.
    echo 正在启动前台服务器 (端口 8080)...
    start "Frontend Server" cmd /c "cd /d "%INSTALL_DIR%\frontend\web" && php -S localhost:8080"
    echo 正在启动后台服务器 (端口 8081)...
    start "Backend Server" cmd /c "cd /d "%INSTALL_DIR%\backend\web" && php -S localhost:8081"
    echo.
    echo 服务器已启动:
    echo   - 前台: http://localhost:8080
    echo   - 后台: http://localhost:8081
    echo.
    echo 正在打开浏览器...
    timeout /t 2 >nul
    start http://localhost:8080
)

goto :end

:error
echo.
echo ============================================================
echo   部署失败，请检查上述错误信息
echo ============================================================
echo.
pause
exit /b 1

:end
echo.
echo 按任意键退出...
pause >nul
exit /b 0
