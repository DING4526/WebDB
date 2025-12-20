<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = '团队后台主页';
$this->params['breadcrumbs'][] = $this->title;

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
        'color' => 'default',
        'abilities' => ['仅浏览首页', '无法访问后台'],
    ],
];

$applySteps = [
    '提交成员信息' => '通过“成员管理”录入或发起申请；暂未上线自助申请，可由管理员代填。',
    '管理员审批' => 'root / 管理员确认信息后设为启用状态，必要时分配角色。',
    '通知与初始权限' => '审批通过后即可访问作业与任务模块；高级操作需管理员调整角色。',
];

$contentPipelines = [
    [
        'title' => '团队作业',
        'path' => 'data/team',
        'route' => ['teamwork/index'],
        'items' => ['需求/设计/实现文档', '数据库文件、PPT 等'],
    ],
    [
        'title' => '个人作业',
        'path' => 'data/personal/{学号或姓名}/',
        'route' => ['personalwork/index'],
        'items' => ['个人提交的作业文件', '按成员独立目录放置'],
    ],
];

$teamInfo = Yii::$app->teamProvider->getTeam();
?>

<div class="site-index">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-info panel-border top">
        <div class="panel-heading">
          <span class="panel-title"><i class="glyphicon glyphicon-dashboard"></i> 后台概览</span>
        </div>
        <div class="panel-body" style="padding:18px 22px;">
          <div class="row">
            <div class="col-sm-4">
              <p class="text-muted mb5">当前身份：</p>
              <p class="lead" style="margin:0;">
                <?php if (Yii::$app->user->isGuest): ?>
                  游客（仅浏览）
                <?php else: ?>
                  <?= Html::encode(Yii::$app->user->getUser()->username ?? '') ?> · 角色：<?= Html::encode(Yii::$app->user->getUser()->role ?? 'member') ?>
                <?php endif; ?>
              </p>
            </div>
            <div class="col-sm-4">
              <p class="text-muted mb5">团队信息：</p>
              <?php if (!empty($teamInfo)): ?>
                <div><strong><?= Html::encode($teamInfo->name) ?></strong></div>
                <div class="text-muted">主题：<?= Html::encode($teamInfo->topic) ?></div>
              <?php else: ?>
                <div class="text-muted">尚未创建团队</div>
              <?php endif; ?>
            </div>
            <div class="col-sm-4 text-right">
              <?php if ($isRoot): ?>
                <a class="btn btn-warning btn-xs" href="<?= Url::to(['team/index']) ?>">查看团队信息</a>
                <a class="btn btn-info btn-xs" href="<?= Url::to(['team-member-apply/index']) ?>">审批成员申请</a>
                <a class="btn btn-success btn-xs" href="<?= Url::to(['taskboard/index']) ?>">查看任务分工板</a>
              <?php elseif($isMember): ?>
                <a class="btn btn-warning btn-xs" href="<?= Url::to(['team/index']) ?>">查看团队信息</a>
                <a class="btn btn-success btn-xs" href="<?= Url::to(['taskboard/index']) ?>">查看任务分工板</a>
              <?php else: ?>
                <a class="btn btn-warning btn-xs" href="<?= Url::to(['team/index']) ?>">查看团队信息</a>
                <a class="btn btn-info btn-xs" href="<?= Url::to(['team-member-apply/create']) ?>">申请成为团队成员</a>
                <a class="btn btn-success btn-xs" href="<?= Url::to(['taskboard/index']) ?>">查看任务分工板</a>
              <?php endif; ?>                
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-lock"></span>
          角色与权限模型
        </div>
        <div class="panel-body p15">
          <p class="text-muted mb10">用角色而非登录态区分能力范围，后续可接入 RBAC。</p>
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
                      <span class="label label-<?= $role['color'] ?>"><?= Html::encode($role['name']) ?></span>
                    </td>
                    <td><?= Html::encode(implode(' / ', $role['abilities'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <!-- <div class="alert alert-warning" style="margin-bottom: 0;">
            当前系统仅基于“是否登录”区分身份，以上为规划基线，后续逐步接入。
          </div> -->
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-user"></span>
          成员注册/管理链路
        </div>
        <div class="panel-body p15">
          <p class="text-muted mb10">优先打通“申请 → 审批 → 角色分配”闭环。</p>
          <ol class="mb15" style="padding-left: 18px;">
            <?php foreach ($applySteps as $step => $desc): ?>
              <li style="margin-bottom: 8px;">
                <strong><?= Html::encode($step) ?>：</strong>
                <?= Html::encode($desc) ?>
              </li>
            <?php endforeach; ?>
          </ol>
          <div class="well well-sm" style="margin-bottom: 0;">
            管理员入口：
            <a href="<?= Url::to(['team-member-apply/index']) ?>">成员申请审批</a>
            <span class="text-muted">（root 可审批并授予 member）</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
