<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '任务分工板';
$this->params['breadcrumbs'][] = $this->title;

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
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:12px;">
    <div>
      <h2 style="margin:0;"><?= Html::encode($this->title) ?></h2>
      <div class="text-muted" style="margin-top:6px;">
        目标：让每个人清楚“我现在做什么、交付到哪一步、卡在哪里”。
      </div>
    </div>
    <div>
      <a class="btn btn-default" href="<?= Url::to(['site/index']) ?>">返回后台主页</a>
    </div>
  </div>

  <!-- 里程碑 -->
  <div class="panel panel-primary" style="box-shadow:0 2px 10px rgba(0,0,0,.05);">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-flag"></span> 项目里程碑
    </div>
    <div class="panel-body">
      <?php foreach ($milestones as $m): ?>
        <span class="label <?= $m['done'] ? 'label-success' : 'label-default' ?>" style="margin-right:8px;display:inline-block;margin-bottom:6px;">
          <?= $m['done'] ? '✓ ' : '· ' ?><?= Html::encode($m['name']) ?>
        </span>
      <?php endforeach; ?>
      <div class="text-muted" style="margin-top:8px;font-size:12px;">
        建议每次组会只更新这里的“完成状态”，不必长篇汇报。
      </div>
    </div>
  </div>

  <!-- 负责人卡片 -->
  <div class="row">
    <?php foreach ($owners as $o): ?>
      <div class="col-md-6">
        <div class="panel panel-default" style="box-shadow:0 2px 10px rgba(0,0,0,.05);">
          <div class="panel-heading" style="background:#f7f9fb;">
            <span class="glyphicon glyphicon-user"></span>
            <strong><?= Html::encode($o['name']) ?></strong>
            <span class="text-muted">· <?= Html::encode($o['scope']) ?></span>
          </div>
          <div class="panel-body">
            <div style="margin-bottom:8px;"><strong>本周交付</strong></div>
            <ul style="padding-left:18px;margin-bottom:10px;">
              <?php foreach ($o['todo'] as $t): ?>
                <li><?= Html::encode($t) ?></li>
              <?php endforeach; ?>
            </ul>
            <div class="text-muted" style="font-size:12px;">
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
      <div class="panel panel-danger" style="box-shadow:0 2px 10px rgba(0,0,0,.05);">
        <div class="panel-heading"><span class="glyphicon glyphicon-fire"></span> P0 必须完成（能演示）</div>
        <div class="panel-body">
          <ul style="padding-left:18px;margin:0;">
            <?php foreach ($p0 as $x): ?><li><?= Html::encode($x) ?></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-warning" style="box-shadow:0 2px 10px rgba(0,0,0,.05);">
        <div class="panel-heading"><span class="glyphicon glyphicon-star"></span> P1 加分项</div>
        <div class="panel-body">
          <ul style="padding-left:18px;margin:0;">
            <?php foreach ($p1 as $x): ?><li><?= Html::encode($x) ?></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-info" style="box-shadow:0 2px 10px rgba(0,0,0,.05);">
        <div class="panel-heading"><span class="glyphicon glyphicon-leaf"></span> P2 体验优化</div>
        <div class="panel-body">
          <ul style="padding-left:18px;margin:0;">
            <?php foreach ($p2 as $x): ?><li><?= Html::encode($x) ?></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-info" style="margin-top:10px;">
    <strong>用法建议：</strong>每次开会只更新两处：①里程碑状态 ②每个人的“本周交付/阻塞”。其余不改。
  </div>

</div>
