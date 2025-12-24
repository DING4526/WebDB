<?php

/**
 * Ding 2310724
 * 帮助信息页面 - 角色与权限模型、成员注册/管理链路
 */

use yii\helpers\Html;
use yii\helpers\Url;

$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
$isMember = $currentUser && $currentUser->isMember();

$roleMatrix = [
    [
        'name' => 'root 管理员',
        'color' => 'danger',
        'abilities' => ['系统配置', '成员审批/维护'],
    ],
    [
        'name' => '团队成员（member）',
        'color' => 'primary',
        'abilities' => ['查看任务版', '提交作业文件', '参与团队协作'],
    ],
    [
        'name' => '普通用户（user）',
        'color' => 'info',
        'abilities' => ['仅浏览公共信息', '无法操作数据', '可申请成为团队成员'],
    ],
    [
        'name' => '游客（guest）',
        'color' => 'ghost',
        'abilities' => ['仅浏览首页', '无法访问后台'],
    ],
];

$applySteps = [
    '提交成员信息' => '通过"成员管理"录入或发起申请；暂未上线自助申请，可由管理员代填。',
    '管理员审批' => 'root / 管理员确认信息后设为启用状态，必要时分配角色。',
    '通知与初始权限' => '审批通过后即可访问作业与任务模块；高级操作需管理员调整角色。',
];
?>

<div class="help-content" style="padding: 20px;">
    <div class="row">
        <div class="col-md-12">
            <div class="adm-card" style="margin-top: 0;">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">角色与权限模型</h3>
                </div>
                <div class="adm-card-body">
                    <p class="adm-hint" style="margin-bottom:12px;">用角色而非登录态区分能力范围，后续可接入 RBAC。</p>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>角色</th>
                                    <th>可做什么</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roleMatrix as $role): ?>
                                    <tr>
                                        <td>
                                            <span class="adm-badge adm-badge-<?= 
                                                $role['color'] === 'danger' ? 'pending' : 
                                               ($role['color'] === 'primary' ? 'active' : 
                                               ($role['color'] === 'info' ? 'info' : 'inactive')) ?>">
                                                <?= Html::encode($role['name']) ?>
                                            </span>
                                        </td>
                                        <td class="adm-muted"><?= Html::encode(implode(' / ', $role['abilities'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 14px;">
        <div class="col-md-12">
            <div class="adm-card" style="margin-top: 0;">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">成员注册/管理链路</h3>
                </div>
                <div class="adm-card-body">
                    <p class="adm-hint" style="margin-bottom:12px;">优先打通"申请 → 审批 → 角色分配"闭环。</p>
                    <ol style="padding-left: 18px;margin-bottom:16px;">
                        <?php foreach ($applySteps as $step => $desc): ?>
                            <li style="margin-bottom: 8px;">
                                <strong><?= Html::encode($step) ?>：</strong>
                                <span class="adm-muted"><?= Html::encode($desc) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="adm-section" style="margin-bottom:0;">
                        <?php if ($isRoot): ?>
                            管理员入口：
                            <a href="<?= Url::to(['team-member-apply/index']) ?>" target="_blank">成员申请审批</a>
                            <span class="adm-muted">（root 可审批并授予 member）</span>
                        <?php elseif (!$isMember): ?>
                            申请入口：
                            <a href="<?= Url::to(['team-member-apply/create']) ?>" target="_blank">提交成员申请</a>
                            <span class="adm-muted">（申请后等待管理员审批）</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
