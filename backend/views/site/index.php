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
  <div class="panel panel-info panel-border top">
    <div class="panel-heading">
      <span class="panel-title">
        <i class="glyphicon glyphicon-dashboard"></i>
        基于 Absolute 模板的后台导航与治理说明
      </span>
    </div>
    <div class="panel-body" style="padding: 18px 22px;">
      <h3 style="margin-top: 0; font-weight: 600;">团队后台信息架构</h3>
      <p class="text-muted" style="margin-bottom: 10px;">
        参考 Absolute 后台模板的分栏与卡片样式，明确角色边界、成员管理与内容链路，并为作业/任务提供统一入口。
      </p>
      <p class="text-muted" style="margin-bottom: 0;">
        当前身份：
        <?php if (Yii::$app->user->isGuest): ?>
          游客（仅浏览）
        <?php else: ?>
          <?= Html::encode(Yii::$app->user->identity->username) ?> · 角色：<?= Html::encode(Yii::$app->user->identity->role ?? 'member') ?>
        <?php endif; ?>
      </p>
      <div style="margin-top: 14px;">
        <a class="btn btn-primary" href="<?= Url::to(['teamwork/index']) ?>">查看团队作业</a>
        <a class="btn btn-default" href="<?= Url::to(['personalwork/index']) ?>">查看个人作业</a>
        <a class="btn btn-warning" href="<?= Url::to(['team-member/index']) ?>">成员管理面板</a>
        <a class="btn btn-success" href="<?= Url::to(['taskboard/index']) ?>">任务分工板</a>
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
            <a href="<?= Url::to(['team-member/index']) ?>">成员管理</a>
            <span class="text-muted">（列表/编辑/启用禁用，后续可加审批状态）</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <?php foreach ($contentPipelines as $block): ?>
      <div class="col-md-6">
        <div class="panel panel-primary">
          <div class="panel-heading">
            <span class="glyphicon glyphicon-folder-open"></span>
            <?= Html::encode($block['title']) ?> 上传与管理
            <span class="label label-default pull-right">当前：目录扫描</span>
          </div>
          <div class="panel-body p15">
            <p class="text-muted mb10">后台展示来源：<?= Html::encode($block['path']) ?></p>
            <ul class="mb10" style="padding-left: 18px;">
              <?php foreach ($block['items'] as $item): ?>
                <li><?= Html::encode($item) ?></li>
              <?php endforeach; ?>
            </ul>
            <div class="alert alert-info" style="margin-bottom: 8px;">
              待办：增加后台上传表单与存储策略（数据库/对象存储），当前先使用目录方式。
            </div>
            <a class="btn btn-sm btn-default" href="<?= Url::to($block['route']) ?>">查看列表</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-home"></span>
          首页信息架构
        </div>
        <div class="panel-body p15">
          <ul class="list-unstyled mb10">
            <li><strong>公告/待办：</strong>首屏展示团队公告、审批提醒、最新作业变更。</li>
            <li><strong>导航与模块：</strong>左侧菜单分组：成员/作业/任务/项目。</li>
            <li><strong>统计：</strong>近期提交数、任务完成度（可接入 dashboard）。</li>
          </ul>
          <div class="alert alert-success" style="margin-bottom: 0;">
            目标：让“当前身份能做什么”“需要我处理什么”在首页即清晰可见。
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="panel panel-success">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-check"></span>
          团队任务分工（新增）
          <span class="label label-success pull-right">New</span>
        </div>
        <div class="panel-body p15">
          <p class="text-muted mb10">
            用于记录后台建设/课程作业的分工与进度，支持按待办/进行中/已完成拆分。
          </p>
          <ul class="mb10" style="padding-left: 18px;">
            <li>典型场景：作业提交规则调整、文件上传重构、审批功能开发。</li>
            <li>入口：左侧“任务分工板”，先以内置列表形式呈现，可逐步演进为 CRUD。</li>
          </ul>
          <a class="btn btn-sm btn-success" href="<?= Url::to(['taskboard/index']) ?>">进入任务分工板</a>
        </div>
      </div>
    </div>
  </div>
</div>
