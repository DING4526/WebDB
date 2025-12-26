<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '用户登录';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
/* 登录页面样式 - 深色主题 */
.site-login {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 40px 0;
}

.login-container {
    width: 100%;
    max-width: 450px;
    padding: 40px;
    background: var(--card-bg, rgba(30, 25, 20, 0.9));
    border: 1px solid var(--card-border, rgba(201, 162, 39, 0.2));
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.login-header {
    text-align: center;
    margin-bottom: 35px;
}

.login-header h1 {
    color: var(--gold-primary, #C9A227) !important;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.login-header .login-subtitle {
    color: var(--text-light, #F5E6C8);
    opacity: 0.8;
    font-size: 14px;
}

.login-header .login-divider {
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--gold-muted, #A88B2A), var(--gold-primary, #C9A227), var(--gold-muted, #A88B2A));
    margin: 20px auto 0;
    border-radius: 2px;
}

/* 表单样式 */
.login-container .form-group {
    margin-bottom: 22px;
}

.login-container .control-label {
    color: var(--text-light, #F5E6C8) !important;
    font-weight: 500;
    margin-bottom: 8px;
}

.login-container .form-control {
    background: rgba(20, 15, 10, 0.6);
    border: 1px solid var(--card-border, rgba(201, 162, 39, 0.2));
    color: var(--text-light, #F5E6C8);
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.login-container .form-control:focus {
    background: rgba(25, 20, 15, 0.8);
    border-color: var(--gold-primary, #C9A227);
    box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.15);
    outline: none;
}

.login-container .form-control::placeholder {
    color: rgba(245, 230, 200, 0.4);
}

/* 复选框样式 */
.login-container .checkbox {
    color: var(--text-light, #F5E6C8);
    opacity: 0.85;
}

.login-container .checkbox label {
    padding-left: 25px;
    position: relative;
    cursor: pointer;
}

.login-container .checkbox input[type="checkbox"] {
    position: absolute;
    left: 0;
    top: 2px;
    width: 18px;
    height: 18px;
    accent-color: var(--gold-primary, #C9A227);
}

/* 链接样式 */
.login-links {
    color: rgba(245, 230, 200, 0.7);
    font-size: 13px;
    margin: 20px 0;
    padding: 15px;
    background: rgba(201, 162, 39, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(201, 162, 39, 0.1);
}

.login-links a {
    color: var(--gold-primary, #C9A227);
    text-decoration: none;
    transition: color 0.2s ease;
}

.login-links a:hover {
    color: var(--gold-light, #D4AF37);
    text-decoration: underline;
}

/* 登录按钮 */
.login-container .btn-login {
    width: 100%;
    padding: 14px 20px;
    font-size: 16px;
    font-weight: 600;
    background: linear-gradient(135deg, var(--gold-primary, #C9A227), var(--gold-muted, #A88B2A));
    border: none;
    color: var(--text-dark, #1A1A1A);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.login-container .btn-login:hover {
    background: linear-gradient(135deg, var(--gold-light, #D4AF37), var(--gold-primary, #C9A227));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(201, 162, 39, 0.4);
}

.login-container .btn-login:active {
    transform: translateY(0);
}

/* 注册链接 */
.login-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid var(--card-border, rgba(201, 162, 39, 0.2));
}

.login-footer p {
    color: rgba(245, 230, 200, 0.7);
    margin: 0;
    font-size: 14px;
}

.login-footer a {
    color: var(--gold-primary, #C9A227);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.login-footer a:hover {
    color: var(--gold-light, #D4AF37);
}

/* 错误提示样式 */
.login-container .help-block {
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
}

.login-container .has-error .form-control {
    border-color: #e74c3c;
}

.login-container .has-error .control-label {
    color: #e74c3c !important;
}
</style>

<div class="site-login">
    <div class="login-container">
        <div class="login-header">
            <h1><i class="glyphicon glyphicon-user"></i> <?= Html::encode($this->title) ?></h1>
            <p class="login-subtitle">烽火记忆 · 抗战胜利80周年</p>
            <div class="login-divider"></div>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput([
                'autofocus' => true,
                'placeholder' => '请输入用户名'
            ])->label('用户名') ?>

            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => '请输入密码'
            ])->label('密码') ?>

            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => '<div class="checkbox">{input} {label}</div>{error}',
            ])->label('记住我') ?>

            <div class="login-links">
                忘记密码？<?= Html::a('点击重置', ['site/request-password-reset']) ?>
                <br>
                需要验证邮件？<?= Html::a('重新发送', ['site/resend-verification-email']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('登 录', ['class' => 'btn btn-login', 'name' => 'login-button']) ?>
            </div>

        <?php ActiveForm::end(); ?>

        <div class="login-footer">
            <p>还没有账号？<?= Html::a('立即注册', ['site/signup']) ?></p>
        </div>
    </div>
</div>
