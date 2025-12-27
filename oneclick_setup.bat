@echo off
setlocal EnableExtensions EnableDelayedExpansion
chcp 65001 >nul

REM ============================================================
REM  WebDB 一键部署（含 XAMPP 安装）
REM  目标：全自动安装到 D:\XAMPP，然后启动 Apache/MySQL，
REM       进入 htdocs 执行：clone -> init -> 建库(不覆盖) -> 导入 seed.sql
REM
REM  使用方式：
REM  1) 把本 bat 与 xampp 安装包放同一目录
REM  2) 双击运行本 bat
REM
REM  约定/前提：
REM  - 需要能访问 GitHub（用于 git clone）
REM  - 需要系统已安装 Git（命令 git 可用）
REM  - 本脚本不做交互选择，不提示确认
REM ============================================================

REM ============== 固定配置（按需改） ==============
set "XAMPP_EXE=xampp-windows-x64-8.2.12-0-VS16-installer.exe"
set "XAMPP_DIR=D:\XAMPP"

set "REPO_URL=https://github.com/DING4526/WebDB.git"
set "PROJECT_NAME=WebDB"
set "HTDOCS_DIR=%XAMPP_DIR%\htdocs"

REM 数据库配置（最基础方案：不覆盖）
set "DB_NAME=yii2advanced"
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_USER=root"
set "DB_PASS="

REM seed.sql 位于项目根目录
set "SEED_SQL=seed.sql"
REM ===============================================

REM ------------------------------
REM 0) 自动提升管理员权限（安装 XAMPP 常需要）
REM ------------------------------
net session >nul 2>&1
if %errorlevel% neq 0 (
  powershell -NoProfile -ExecutionPolicy Bypass -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
  exit /b
)

cd /d "%~dp0"

echo.
echo =========================================
echo   WebDB 一键部署（含 XAMPP 安装）
echo   目标路径：%XAMPP_DIR%
echo =========================================
echo.

REM ------------------------------
REM 1) 安装 XAMPP 到 D:\XAMPP（无选择）
REM ------------------------------
if exist "%XAMPP_DIR%\xampp-control.exe" (
  echo [1/6] 已检测到 XAMPP 已安装：%XAMPP_DIR%
) else (
  echo [1/6] 开始安装 XAMPP 到：%XAMPP_DIR%
  if not exist "%XAMPP_EXE%" (
    echo [错误] 未找到 XAMPP 安装包：%XAMPP_EXE%
    echo        请把安装包与本脚本放在同一目录，或修改 XAMPP_EXE 变量
    goto :error
  )

  REM 尝试使用 Inno Setup 常见静默参数安装到指定目录
  REM 若你的安装器不支持这些参数，会安装失败并在后续步骤提示
  start "" /wait "%XAMPP_EXE%" /VERYSILENT /SUPPRESSMSGBOXES /NORESTART /DIR="%XAMPP_DIR%"
)

if not exist "%XAMPP_DIR%\xampp-control.exe" (
  echo [错误] XAMPP 安装未成功或未安装到 %XAMPP_DIR%
  echo        请手动安装到 %XAMPP_DIR% 后重新运行本脚本
  goto :error
)

echo [✓] XAMPP 就绪
echo.

REM ------------------------------
REM 2) 启动 Apache/MySQL（无选择）
REM ------------------------------
echo [2/6] 启动 XAMPP 服务（Apache/MySQL）...

REM 优先使用 xampp_start.exe（通常会启动 Apache/MySQL）
if exist "%XAMPP_DIR%\xampp_start.exe" (
  start "" "%XAMPP_DIR%\xampp_start.exe"
) else (
  REM 兜底：尝试直接打开控制面板（仍然不做交互，但能让对方看到状态）
  start "" "%XAMPP_DIR%\xampp-control.exe"
)

REM 等待几秒给服务启动时间（无交互）
timeout /t 5 >nul
echo [✓] 已发起启动（若端口冲突需手动处理）
echo.

REM ------------------------------
REM 3) 进入 htdocs
REM ------------------------------
echo [3/6] 进入 htdocs：%HTDOCS_DIR%
if not exist "%HTDOCS_DIR%\" (
  echo [错误] 未找到 htdocs：%HTDOCS_DIR%
  goto :error
)
cd /d "%HTDOCS_DIR%"
echo [✓] 已进入 htdocs
echo.

REM ------------------------------
REM 4) git clone 项目到 htdocs\WebDB（已存在则跳过）
REM ------------------------------
echo [4/6] 获取项目代码（git clone）...
if exist "%PROJECT_NAME%\" (
  echo [i] 已存在目录：%HTDOCS_DIR%\%PROJECT_NAME% ，跳过 clone
) else (
  git clone "%REPO_URL%" "%PROJECT_NAME%"
  if errorlevel 1 goto :error
  echo [✓] clone 完成
)
echo.

REM ------------------------------
REM 5) php init（使用 XAMPP 自带 php）
REM ------------------------------
echo [5/6] 执行 Yii2 init（Development）...
cd /d "%HTDOCS_DIR%\%PROJECT_NAME%"
if errorlevel 1 goto :error

set "PHP_EXE=%XAMPP_DIR%\php\php.exe"
if not exist "%PHP_EXE%" (
  echo [错误] 未找到 XAMPP PHP：%PHP_EXE%
  goto :error
)

"%PHP_EXE%" init --env=Development --overwrite=All
if errorlevel 1 goto :error
echo [✓] init 完成
echo.

REM ------------------------------
REM 6) 建库（不覆盖）+ 导入 seed.sql（使用 XAMPP 自带 mysql）
REM ------------------------------
echo [6/6] 创建数据库并导入 seed.sql（不覆盖模式）...

set "MYSQL_EXE=%XAMPP_DIR%\mysql\bin\mysql.exe"
if not exist "%MYSQL_EXE%" (
  echo [错误] 未找到 XAMPP MySQL：%MYSQL_EXE%
  goto :error
)

set "SQL_CREATE=CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

if "%DB_PASS%"=="" (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -e "%SQL_CREATE%"
) else (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -p"%DB_PASS%" -e "%SQL_CREATE%"
)
if errorlevel 1 goto :error

if not exist "%SEED_SQL%" (
  echo [错误] 未找到 %SEED_SQL%（应位于 %HTDOCS_DIR%\%PROJECT_NAME%）
  goto :error
)

if "%DB_PASS%"=="" (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" "%DB_NAME%" < "%SEED_SQL%"
) else (
  "%MYSQL_EXE%" -h "%DB_HOST%" -P "%DB_PORT%" -u "%DB_USER%" -p"%DB_PASS%" "%DB_NAME%" < "%SEED_SQL%"
)
if errorlevel 1 goto :error

echo.
echo =========================================
echo   全部完成！
echo =========================================
echo 前台: http://localhost/WebDB/frontend/web/
echo 后台: http://localhost/WebDB/backend/web/
echo.
pause
exit /b 0

:error
echo.
echo =========================================
echo   部署失败：请检查上面的错误信息
echo =========================================
echo.
echo 可能原因提示：
echo 1) XAMPP 安装器不支持静默参数 -> 请手动安装到 %XAMPP_DIR% 后重跑
echo 2) Apache(80/443) 或 MySQL(3306) 端口被占用 -> 需先解决端口冲突
echo 3) 未安装 Git 或无法访问 GitHub -> 请先安装 Git 并确保网络可用
echo 4) 目标机已有同名数据库/表 -> 不覆盖模式下导入可能冲突（属预期行为）
echo.
pause
exit /b 1
