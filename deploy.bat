@echo off
setlocal EnableExtensions EnableDelayedExpansion
chcp 65001 >nul

REM ====== 配置 ======
set "DB_NAME=yii2advanced"
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_USER=root"
set "DB_PASS="

set "PHP_EXE=php"
set "MYSQL_EXE=mysql"
set "SEED_SQL=seed.sql"
REM ==================

cd /d "%~dp0"

echo.
echo =========================================
echo   WebDB 一键部署（init + import seed）
echo =========================================
echo.

echo [1/3] Yii2 init（Development）...
"%PHP_EXE%" init --env=Development --overwrite=All
if errorlevel 1 goto :error
echo [✓] init 完成
echo.

echo [2/3] 创建数据库：%DB_NAME% ...
set "SQL_CREATE=CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

if "%DB_PASS%"=="" (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -e "%SQL_CREATE%"
) else (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -p"%DB_PASS%" -e "%SQL_CREATE%"
)
if errorlevel 1 goto :error
echo [✓] 数据库已就绪
echo.

echo [3/3] 导入种子数据：%SEED_SQL% ...
if not exist "%SEED_SQL%" (
  echo [错误] 未找到 %SEED_SQL%
  goto :error
)

if "%DB_PASS%"=="" (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" "%DB_NAME%" < "%SEED_SQL%"
) else (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -p"%DB_PASS%" "%DB_NAME%" < "%SEED_SQL%"
)

if errorlevel 1 goto :error
echo [✓] 数据导入完成
echo.

echo =========================================
echo   完成！
echo =========================================
echo 前台: http://localhost/WebDB/frontend/web/
echo 后台: http://localhost/WebDB/backend/web/
echo.
pause
exit /b 0

:error
echo.
echo =========================================
echo   失败：请检查上面的错误信息
echo =========================================
echo.
pause
exit /b 1
