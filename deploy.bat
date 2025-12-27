@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

REM =========================================
REM 全自动部署（从 init 开始）
REM init -> 创建数据库 -> migrate -> 导入 seed.sql
REM =========================================

REM ====== 可改配置 ======
set "DB_NAME=yii2advanced"
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_USER=root"
set "DB_PASS="

REM 如果你的命令不是直接可用，请改成完整路径：
set "PHP_EXE=php"
set "MYSQL_EXE=mysql"

REM seed.sql 位置（默认项目根目录）
set "SEED_SQL=seed.sql"
REM ======================

echo.
echo =========================================
echo   WebDB 全自动部署（init->db->migrate->seed）
echo =========================================
echo.

REM 进入脚本所在目录（项目根目录）
cd /d "%~dp0"

REM 1) Yii2 init
echo [1/4] 执行 Yii2 init（Development）...
%PHP_EXE% init --env=Development --overwrite=All
if errorlevel 1 goto :error
echo [✓] init 完成
echo.

REM 2) 创建数据库（自动，无需进 phpMyAdmin）
echo [2/4] 创建数据库：%DB_NAME% ...
if "%DB_PASS%"=="" (
  %MYSQL_EXE% -h%DB_HOST% -P%DB_PORT% -u%DB_USER% -e "CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
) else (
  %MYSQL_EXE% -h%DB_HOST% -P%DB_PORT% -u%DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
)
if errorlevel 1 goto :error
echo [✓] 数据库已就绪
echo.

REM 3) 执行迁移
echo [3/4] 执行迁移：php yii migrate ...
%PHP_EXE% yii migrate --interactive=0
if errorlevel 1 goto :error
echo [✓] migrate 完成
echo.

REM 4) 导入 seed.sql
echo [4/4] 导入种子数据：%SEED_SQL% ...
if not exist "%SEED_SQL%" (
  echo [错误] 未找到 %SEED_SQL%（请确认它在项目根目录或修改 SEED_SQL 路径）
  goto :error
)

if "%DB_PASS%"=="" (
  %MYSQL_EXE% -h%DB_HOST% -P%DB_PORT% -u%DB_USER% %DB_NAME% < "%SEED_SQL%"
) else (
  %MYSQL_EXE% -h%DB_HOST% -P%DB_PORT% -u%DB_USER% -p%DB_PASS% %DB_NAME% < "%SEED_SQL%"
)
if errorlevel 1 goto :error
echo [✓] seed.sql 导入完成
echo.

echo =========================================
echo   全部完成！
echo =========================================
echo.
echo 前台: http://localhost/WebDB/frontend/web/
echo 后台: http://localhost/WebDB/backend/web/
echo.
pause
exit /b 0

:error
echo.
echo =========================================
echo   失败：请看上面哪一步报错
echo =========================================
echo.
pause
exit /b 1
