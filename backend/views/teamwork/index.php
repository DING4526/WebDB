<?php

/**
 * Ding 2310724
 * 团队作业文件列表视图/下载
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '团队作业';
$this->params['breadcrumbs'][] = $this->title;
$currentUserId = Yii::$app->user->id;
?>

<h2 style="margin-bottom:18px;"><?= Html::encode($this->title) ?></h2>

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-briefcase"></span> 团队作业总览
      </div>
      <div class="panel-body">
        <div class="row text-center">
          <div class="col-sm-4">
            <div style="font-size:28px;font-weight:bold;"><?= Html::encode($fileCount) ?></div>
            <div class="text-muted">文件数量</div>
          </div>
          <div class="col-sm-4">
            <span class="label label-info" style="font-size:14px;padding:8px 12px;">需求 / 设计 / 实现</span>
          </div>
          <div class="col-sm-4">
            <span class="label label-success" style="font-size:14px;padding:8px 12px;">本地存储 · 快速预览</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if (!empty($canUpload)): ?>
  <div class="col-md-4">
    <div class="panel panel-info">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-upload"></span> 上传团队作业
      </div>
      <div class="panel-body">
        <?= Html::beginForm(['teamwork/upload'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) ?>
          <div class="form-group">
            <label class="control-label col-sm-4">选择文件</label>
            <div class="col-sm-8">
              <input type="file" name="file" required class="form-control">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
              <?= Html::submitButton('上传', ['class' => 'btn btn-primary btn-block']) ?>
            </div>
          </div>
        <?= Html::endForm() ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (empty($files)): ?>
  <div class="alert alert-warning">
    data/team 目录为空：请把需求文档/设计文档/实现文档/用户手册/PPT/数据库文件等放入该目录。
  </div>
<?php else: ?>
  <div class="panel panel-default" style="box-shadow:0 2px 6px rgba(0,0,0,0.05);">
    <div class="panel-heading" style="background:#f7f9fb;">
      <span class="glyphicon glyphicon-folder-open"></span> 文件列表
    </div>
    <div class="panel-body" style="padding:12px 15px;">
      <ul class="list-group" style="margin-bottom:0;">
        <?php foreach ($files as $f): ?>
          <li class="list-group-item" style="padding:10px 12px;">
            <div class="pull-right text-muted">
              <span class="glyphicon glyphicon-time"></span> <?= date('Y-m-d H:i', $f['mtime']) ?>
            </div>
            <div class="ellipsis">
              <a href="<?= Url::to(['download/file', 'type' => 'team', 'path' => $f['name']]) ?>">
                <span class="glyphicon glyphicon-file text-primary"></span>
                <?= Html::encode($f['display'] ?? $f['name']) ?>
              </a>
            </div>
            <?php if (!empty($f['owner_id']) && !empty($ownerMap[$f['owner_id']])): ?>
              <span class="label label-default" style="margin-left:8px;">上传者：<?= Html::encode($ownerMap[$f['owner_id']]) ?></span>
            <?php endif; ?>
            <?php if (!empty($isRoot) || (!empty($f['owner_id']) && (int)$f['owner_id'] === (int)$currentUserId)): ?>
              <?= Html::a('删除', ['teamwork/delete', 'name' => $f['name']], [
                'class' => 'btn btn-xs btn-danger pull-right',
                'style' => 'margin-top:6px;',
                'data-method' => 'post',
                'data-confirm' => '确定删除该文件？',
              ]) ?>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>

<style>
.ellipsis { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
