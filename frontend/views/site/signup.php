<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '用户注册';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
/* 注册页面样式 - 深色主题 */
.site-signup {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 40px 0;
}

.signup-container {
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

.signup-header {
    text-align: center;
    margin-bottom: 35px;
}

.signup-header h1 {
    color: var(--gold-primary, #C9A227) !important;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.signup-header .signup-subtitle {
    color: var(--text-light, #F5E6C8);
    opacity: 0.8;
    font-size: 14px;
}

.signup-header .signup-divider {
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--gold-muted, #A88B2A), var(--gold-primary, #C9A227), var(--gold-muted, #A88B2A));
    margin: 20px auto 0;
    border-radius: 2px;
}

/* 表单样式 */
.signup-container .form-group {
    margin-bottom: 22px;
}

.signup-container .control-label {
    color: var(--text-light, #F5E6C8) !important;
    font-weight: 500;
    margin-bottom: 8px;
}

.signup-container .form-control {
    background: rgba(20, 15, 10, 0.6);
    border: 1px solid var(--card-border, rgba(201, 162, 39, 0.2));
    color: var(--text-light, #F5E6C8);
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.signup-container .form-control:focus {
    background: rgba(25, 20, 15, 0.8);
    border-color: var(--gold-primary, #C9A227);
    box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.15);
    outline: none;
}

.signup-container .form-control::placeholder {
    color: rgba(245, 230, 200, 0.4);
}

/* 注册按钮 */
.signup-container .btn-signup {
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

.signup-container .btn-signup:hover {
    background: linear-gradient(135deg, var(--gold-light, #D4AF37), var(--gold-primary, #C9A227));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(201, 162, 39, 0.4);
}

.signup-container .btn-signup:active {
    transform: translateY(0);
}

/* 登录链接 */
.signup-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid var(--card-border, rgba(201, 162, 39, 0.2));
}

.signup-footer p {
    color: rgba(245, 230, 200, 0.7);
    margin: 0;
    font-size: 14px;
}

.signup-footer a {
    color: var(--gold-primary, #C9A227);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.signup-footer a:hover {
    color: var(--gold-light, #D4AF37);
}

/* 错误提示样式 */
.signup-container .help-block {
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
}

.signup-container .has-error .form-control {
    border-color: #e74c3c;
}

.signup-container .has-error .control-label {
    color: #e74c3c !important;
}

/* 提示信息 */
.signup-tips {
    color: rgba(245, 230, 200, 0.6);
    font-size: 12px;
    margin-top: 15px;
    padding: 12px;
    background: rgba(201, 162, 39, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(201, 162, 39, 0.1);
}

.signup-tips i {
    color: var(--gold-primary, #C9A227);
    margin-right: 5px;
}
</style>

<div class="site-signup">
    <div class="signup-container">
        <div class="signup-header">
            <h1><i class="glyphicon glyphicon-edit"></i> <?= Html::encode($this->title) ?></h1>
            <p class="signup-subtitle">加入我们，铭记历史</p>
            <div class="signup-divider"></div>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <?= $form->field($model, 'username')->textInput([
                'autofocus' => true,
                'placeholder' => '请输入用户名'
            ])->label('用户名') ?>

            <?= $form->field($model, 'email')->textInput([
                'placeholder' => '请输入邮箱地址'
            ])->label('邮箱') ?>

            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => '请设置密码（至少6位）'
            ])->label('密码') ?>

            <div class="signup-tips">
                <i class="glyphicon glyphicon-info-sign"></i>
                注册后即可参与历史事件讨论和留言互动
            </div>

            <div class="form-group" style="margin-top: 25px;">
                <?= Html::submitButton('立即注册', ['class' => 'btn btn-signup', 'name' => 'signup-button']) ?>
            </div>

        <?php ActiveForm::end(); ?>

        <div class="signup-footer">
            <p>已有账号？<?= Html::a('立即登录', ['site/login']) ?></p>
        </div>
    </div>
</div>
