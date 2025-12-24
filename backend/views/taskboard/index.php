<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '任务分工板';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');

// 你可以先用静态数据，后面接 tasks 表时只改这里的数据来源
$milestones = [
  ['name' => '迁移建表', 'done' => true],
  ['name' => 'Seed演示数据', 'done' => true],
  ['name' => '后台CRUD', 'done' => false],
  ['name' => '前台三线', 'done' => false],
  ['name' => '联调&修复', 'done' => false],
  ['name' => '提交材料', 'done' => false],
];

$owners = [
  [
    'name' => '组长',
    'scope' => '后台（事件/人物/留言审核） + 整合发布',
    'todo' => ['Gii生成CRUD并精简表单', '留言审核页(通过/拒绝)', '补一批演示数据'],
    'blocker' => '无/或写具体阻塞',
  ],
  [
    'name' => '组员A',
    'scope' => '前台事件线：时间轴列表 + 事件详情',
    'todo' => ['列表页展示(日期/标题/摘要)', '详情页(内容+关联人物)', '阶段筛选(可选)'],
    'blocker' => '等待：事件-人物关联查询接口/模型关系',
  ],
  [
    'name' => '组员B',
    'scope' => '前台人物线：人物卡片墙 + 人物详情',
    'todo' => ['人物列表卡片', '人物详情(生平+参与事件)', '角色类型筛选(可选)'],
    'blocker' => '无/或写具体阻塞',
  ],
  [
    'name' => '组员C',
    'scope' => '前台留言线：留言墙 + 提交留言',
    'todo' => ['留言列表(仅status=1)', '提交留言(默认status=0)', '提交成功提示'],
    'blocker' => '留言 target_type/target_id 规范需统一',
  ],
];

$p0 = [
  '后台：事件/人物/留言审核最简可用（能新增、能改、能发布）',
  '前台：三条线均可访问且能读到数据库数据',
  '留言：提交后进入待审核，后台审核后前台可见',
];
$p1 = [
  '事件-人物关联管理页（可选独立菜单）',
  '标签显示（事件详情展示标签即可）',
  '简单统计（事件数量按年份）',
];
$p2 = [
  '前台样式统一（卡片/间距/空态提示）',
  '搜索/筛选（阶段/年份/人物身份）',
];
?>

<div class="taskboard-index">

  <!-- 顶部操作 -->
  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">让每个人清楚"我现在做什么、交付到哪一步、卡在哪里"</div>
      </div>
      <div class="adm-actions">
        <?= Html::a('返回后台主页', ['site/index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <!-- 里程碑 -->
  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">项目里程碑</h3>
    </div>
    <div class="adm-card-body">
      <?php foreach ($milestones as $m): ?>
        <span class="adm-badge <?= $m['done'] ? 'adm-badge-active' : 'adm-badge-inactive' ?>" style="margin-right:8px;margin-bottom:6px;">
          <?= $m['done'] ? '✓ ' : '· ' ?><?= Html::encode($m['name']) ?>
        </span>
      <?php endforeach; ?>
      <div class="adm-hint" style="margin-top:12px;font-size:12px;">
        建议每次组会只更新这里的"完成状态"，不必长篇汇报。
      </div>
    </div>
  </div>

  <!-- 负责人卡片 -->
  <div class="row" style="margin-top:14px;">
    <?php foreach ($owners as $o): ?>
      <div class="col-md-6">
        <div class="adm-card" style="margin-bottom:12px;">
          <div class="adm-card-head">
            <h3 class="adm-card-title">
              <strong><?= Html::encode($o['name']) ?></strong>
              <span class="adm-muted">· <?= Html::encode($o['scope']) ?></span>
            </h3>
          </div>
          <div class="adm-card-body">
            <div style="margin-bottom:8px;font-weight:900;">本周交付</div>
            <ul style="padding-left:18px;margin-bottom:10px;">
              <?php foreach ($o['todo'] as $t): ?>
                <li><?= Html::encode($t) ?></li>
              <?php endforeach; ?>
            </ul>
            <div class="adm-hint" style="font-size:12px;">
              <strong>阻塞：</strong><?= Html::encode($o['blocker']) ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- P0/P1/P2 -->
  <div class="row">
    <div class="col-md-4">
      <div class="adm-card" style="border-left:4px solid #ef4444;">
        <div class="adm-card-head" style="background:rgba(239,68,68,0.04);">
          <h3 class="adm-card-title" style="color:#dc2626;">P0 必须完成（能演示）</h3>
        </div>
        <div class="adm-card-body">
          <ul style="padding-left:18px;margin:0;">
            <?php foreach ($p0 as $x): ?><li><?= Html::encode($x) ?></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="adm-card" style="border-left:4px solid #f59e0b;">
        <div class="adm-card-head" style="background:rgba(245,158,11,0.04);">
          <h3 class="adm-card-title" style="color:#d97706;">P1 加分项</h3>
        </div>
        <div class="adm-card-body">
          <ul style="padding-left:18px;margin:0;">
            <?php foreach ($p1 as $x): ?><li><?= Html::encode($x) ?></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="adm-card" style="border-left:4px solid #3b82f6;">
        <div class="adm-card-head" style="background:rgba(59,130,246,0.04);">
          <h3 class="adm-card-title" style="color:#2563eb;">P2 体验优化</h3>
        </div>
        <div class="adm-card-body">
          <ul style="padding-left:18px;margin:0;">
            <?php foreach ($p2 as $x): ?><li><?= Html::encode($x) ?></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-info" style="margin-top:14px;border-radius:18px;">
    <strong>用法建议：</strong>每次开会只更新两处：①里程碑状态 ②每个人的"本周交付/阻塞"。其余不改。
  </div>

</div>
