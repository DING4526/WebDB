<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">WebDB - Yii 2 Advanced Project</h1>
    <br>
</p>

基于 Yii 2 Advanced Template 的 Web 数据库项目。

## 🚀 一键部署 (Windows)

### 方式一：快速部署（推荐新手）

1. **安装 XAMPP**
   - 下载 [XAMPP](https://www.apachefriends.org/) 并安装到 `D:\xampp` 或 `C:\xampp`
   - 启动 XAMPP 控制面板，开启 Apache 和 MySQL

2. **安装 Git**
   - 下载 [Git for Windows](https://git-scm.com/download/win) 并安装

3. **运行部署脚本**
   ```powershell
   # 下载并运行快速部署脚本
   Invoke-WebRequest -Uri "https://raw.githubusercontent.com/DING4526/WebDB/master/quick-deploy.bat" -OutFile "quick-deploy.bat"; .\quick-deploy.bat
   ```
   
   或者手动下载 `quick-deploy.bat` 并双击运行。

### 方式二：手动克隆后部署

```bash
# 1. 克隆仓库到 XAMPP htdocs 目录
cd D:\xampp\htdocs
git clone https://github.com/DING4526/WebDB.git
cd WebDB

# 2. 双击运行 deploy.bat
```

### 部署完成后访问

- 前台: http://localhost/WebDB/frontend/web/
- 后台: http://localhost/WebDB/backend/web/

## 📋 手动部署步骤

如果一键部署失败，可以参考以下手动步骤：

```bash
# 1. 克隆仓库
git clone https://github.com/DING4526/WebDB.git
cd WebDB

# 2. 安装 Composer 依赖
composer install

# 3. 初始化 Yii2 环境
php init --env=Development --overwrite=All

# 4. 创建数据库 yii2advanced（在 phpMyAdmin 中）

# 5. 配置数据库（编辑 common/config/main-local.php）

# 6. 执行数据库迁移
php yii migrate
```

详细部署说明请参考 [迁移部署手册.md](迁移部署手册.md) 和 [前置工作.md](前置工作.md)。

## 📁 项目结构

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
