<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = '团队后台主页';
$this->params['breadcrumbs'][] = $this->title;

$roleMatrix = [
    [
        'name' => 'root 管理员',
        'color' => 'danger',
        'abilities' => ['系统配置', '成员审批/禁用', '敏感数据清理'],
    ],
    [
        'name' => '普通管理员（member）',
        'color' => 'primary',
        'abilities' => ['成员维护', '作业/任务内容管理', '公告与导航编辑'],
    ],
    [
        'name' => '普通用户（user）',
        'color' => 'info',
        'abilities' => ['查看作业与任务', '提交作业文件（待开放上传）', '参与任务分工'],
    ],
    [
        'name' => '游客（guest）',
        'color' => 'default',
        'abilities' => ['仅浏览公共信息', '无法操作数据'],
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
?>

<div class="site-index">
  <div class="row">
    <div class="col-md-8">
      <div class="panel panel-info panel-border top">
        <div class="panel-heading">
          <span class="panel-title"><i class="glyphicon glyphicon-dashboard"></i> 后台概览</span>
        </div>
        <div class="panel-body" style="padding:18px 22px;">
          <p class="text-muted" style="margin-bottom: 8px;">
            当前身份：
            <?php if (Yii::$app->user->isGuest): ?>
              游客（仅浏览）
            <?php else: ?>
              <?= Html::encode(Yii::$app->user->identity->username) ?> · 角色：<?= Html::encode(Yii::$app->user->identity->role ?? 'member') ?>
            <?php endif; ?>
          </p>
          <div class="row">
            <div class="col-sm-4">
              <div class="well well-sm text-center">
                <div class="text-muted">作业</div>
                <a href="<?= Url::to(['teamwork/index']) ?>" class="btn btn-primary btn-xs">团队作业</a>
                <a href="<?= Url::to(['personalwork/index']) ?>" class="btn btn-default btn-xs">个人作业</a>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="well well-sm text-center">
                <div class="text-muted">成员</div>
                <a href="<?= Url::to(['team-member/index']) ?>" class="btn btn-warning btn-xs">成员管理</a>
                <a href="<?= Url::to(['team-member-apply/create']) ?>" class="btn btn-info btn-xs">申请成为成员</a>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="well well-sm text-center">
                <div class="text-muted">任务</div>
                <a href="<?= Url::to(['taskboard/index']) ?>" class="btn btn-success btn-xs">任务分工板</a>
              </div>
            </div>
          </div>
          <div class="alert alert-info" style="margin-bottom:0;">
            当前重点：成员申请/审批上线；作业列表仍基于目录扫描，后续引入上传与任务 CRUD。
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="panel panel-success">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-list-alt"></span> 进度速览
        </div>
        <div class="panel-body p15">
          <ul class="list-unstyled mb10">
            <li><span class="label label-success">已完成</span> 角色字段 & 默认 root</li>
            <li><span class="label label-success">已完成</span> 成员申请/审批</li>
            <li><span class="label label-warning">进行中</span> 成员管理/审批界面美化</li>
            <li><span class="label label-warning">进行中</span> 主页概览精简</li>
            <li><span class="label label-default">待办</span> 任务表 CRUD、作业上传表单</li>
          </ul>
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
          <div class="alert alert-warning" style="margin-bottom: 0;">
            当前系统仅基于“是否登录”区分身份，以上为规划基线，后续逐步接入。
          </div>
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
