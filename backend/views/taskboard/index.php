<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * 任务分工板占位视图
 * 展示待办/进行中/已完成的静态示例，便于后续接 CRUD。
 */

/* @var $this yii\web\View */
/* @var $lanes array */

$this->title = '任务分工板';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="taskboard-index">
  <div class="panel panel-success">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-check"></span>
      团队任务分工
      <span class="label label-success pull-right">New</span>
    </div>
    <div class="panel-body">
      <p class="text-muted" style="margin-bottom: 12px;">
        用于同步后台建设与课程作业分工。后续可以接入任务 CRUD、标签、截止时间、负责人字段等。
      </p>
      <div class="alert alert-info" style="margin-bottom: 16px;">
        当前为静态示例：可先用于沟通优先级，逐步替换为数据库驱动的任务表。
      </div>
      <div class="row">
        <?php foreach ($lanes as $lane): ?>
          <div class="col-md-4">
            <div class="panel panel-<?= Html::encode($lane['color']) ?>">
              <div class="panel-heading">
                <?= Html::encode($lane['title']) ?>
              </div>
              <ul class="list-group">
                <?php foreach ($lane['items'] as $item): ?>
                  <li class="list-group-item">
                    <strong><?= Html::encode($item['name']) ?></strong>
                    <span class="label label-default pull-right" style="margin-left: 6px;"><?= Html::encode($item['owner']) ?></span>
                    <div class="text-muted" style="margin-top: 6px; font-size: 12px;">
                      <?= Html::encode($item['note']) ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-road"></span>
      后续演进建议
    </div>
    <div class="panel-body">
      <ol style="padding-left: 18px; margin-bottom: 0;">
        <li>在数据库中创建 tasks 表，字段包含标题/描述/状态/负责人/截止时间/标签。</li>
        <li>为 root/admin 开启 CRUD 权限，member 可以领取/更新状态，user 仅可查看。</li>
        <li>与作业上传/审批流程联动，支持“需要我处理”的首页提醒。</li>
      </ol>
    </div>
  </div>

  <a class="btn btn-default" href="<?= Url::to(['site/index']) ?>">
    返回后台主页
  </a>
</div>
