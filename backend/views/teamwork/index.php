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

<h2><?= Html::encode($this->title) ?></h2>

<?php if (!empty($canUpload)): ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-upload"></span> 上传团队作业
    </div>
    <div class="panel-body">
      <?= Html::beginForm(['teamwork/upload'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'form-inline']) ?>
        <div class="form-group mb10" style="margin-right:8px;">
          <input type="file" name="file" required class="form-control">
        </div>
        <?= Html::submitButton('上传', ['class' => 'btn btn-primary']) ?>
      <?= Html::endForm() ?>
    </div>
  </div>
<?php endif; ?>

<?php if (empty($files)): ?>
  <div class="alert alert-warning">
    data/team 目录为空：请把需求文档/设计文档/实现文档/用户手册/PPT/数据库文件等放入该目录。
  </div>
<?php else: ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-folder-open"></span> 文件列表
    </div>
    <div class="panel-body" style="padding:12px 15px;">
      <ul class="list-group" style="margin-bottom:0;">
        <?php foreach ($files as $f): ?>
          <li class="list-group-item" style="padding:10px 12px;">
            <div class="pull-right text-muted"><?= date('Y-m-d H:i', $f['mtime']) ?></div>
            <a href="<?= Url::to(['download/file', 'type' => 'team', 'path' => $f['name']]) ?>">
              <?= Html::encode($f['display'] ?? $f['name']) ?>
            </a>
            <?php if (!empty($f['owner_id']) && !empty($ownerMap[$f['owner_id']])): ?>
              <span class="label label-default" style="margin-left:8px;">上传者：<?= Html::encode($ownerMap[$f['owner_id']]) ?></span>
            <?php endif; ?>
            <?php if (!empty($isRoot) || (!empty($f['owner_id']) && (int)$f['owner_id'] === (int)$currentUserId)): ?>
              <?= Html::a('删除', ['teamwork/delete', 'name' => $f['name']], [
                'class' => 'btn btn-xs btn-danger',
                'style' => 'margin-left:10px;',
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
