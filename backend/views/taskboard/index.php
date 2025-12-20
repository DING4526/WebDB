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
      当前任务摘要
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
          <div class="well well-sm">
            <strong>已完成</strong>
            <ul class="list-unstyled mb0">
              <li>角色字段 & 默认 root</li>
              <li>成员申请/审批闭环</li>
              <li>角色访问控制（root/member/user）</li>
            </ul>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="well well-sm">
            <strong>进行中</strong>
            <ul class="list-unstyled mb0">
              <li>成员管理/审批界面美化</li>
              <li>主页概览精简</li>
            </ul>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="well well-sm">
            <strong>待办</strong>
            <ul class="list-unstyled mb0">
              <li>任务表 CRUD（数据库持久化）</li>
              <li>作业上传改为表单存储</li>
            </ul>
          </div>
        </div>
      </div>
      <p class="text-muted" style="margin-bottom:0;">当前页面为简版摘要，后续将接入 tasks 表实现可编辑看板。</p>
    </div>
  </div>

  <a class="btn btn-default" href="<?= Url::to(['site/index']) ?>">
    返回后台主页
  </a>
</div>
