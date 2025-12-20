<?php

/**
 * Ding 2310724
 * 个人作业文件列表视图/下载
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '个人作业';
$this->params['breadcrumbs'][] = $this->title;
?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-education"></span> 个人作业总览
      </div>
      <div class="panel-body">
        <div class="row text-center">
          <div class="col-sm-4">
            <div style="font-size:28px;font-weight:bold;"><?= Html::encode($memberCount) ?></div>
            <div class="text-muted">成员目录</div>
          </div>
          <div class="col-sm-4">
            <div style="font-size:28px;font-weight:bold;"><?= Html::encode($fileCount) ?></div>
            <div class="text-muted">作业文件</div>
          </div>
          <div class="col-sm-4">
            <span class="label label-success" style="font-size:14px;padding:8px 12px;">本地存储 · 扫描展示</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if (!empty($canUpload)): ?>
  <div class="col-md-4">
    <div class="panel panel-info">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-upload"></span> 上传个人作业
      </div>
      <div class="panel-body">
        <?php if (empty($currentStudentNo) && empty($isRoot)): ?>
          <div class="alert alert-warning" style="margin-bottom:0;">请先在首页补充学号后再上传。</div>
        <?php else: ?>
          <?= Html::beginForm(['personalwork/upload'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) ?>
            <?php if (!empty($isRoot)): ?>
              <div class="form-group">
                <label class="control-label col-sm-4">学号目录</label>
                <div class="col-sm-8">
                  <?= Html::textInput('student_no', $currentStudentNo, ['class' => 'form-control', 'placeholder' => '学号目录']) ?>
                </div>
              </div>
            <?php else: ?>
              <?= Html::hiddenInput('student_no', $currentStudentNo) ?>
              <div class="form-group">
                <label class="control-label col-sm-4">学号目录</label>
                <div class="col-sm-8">
                  <p class="form-control-static"><?= Html::encode($currentStudentNo) ?></p>
                </div>
              </div>
            <?php endif; ?>
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
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (empty($members)): ?>
  <div class="alert alert-warning">
    data/personal 目录为空：请按 data/personal/学号(或姓名)/ 放置个人作业文件。
  </div>
<?php else: ?>
  <div class="row">
    <?php foreach ($members as $m): ?>
      <div class="col-md-6">
        <div class="panel panel-default" style="box-shadow:0 2px 6px rgba(0,0,0,0.05);">
          <div class="panel-heading" style="background:#f7f9fb;">
            <span class="glyphicon glyphicon-user"></span>
            <?= Html::encode($m['folder']) ?>
            <span class="badge pull-right"><?= count($m['files']) ?></span>
          </div>
          <div class="panel-body" style="padding:12px 15px;">
            <?php if (empty($m['files'])): ?>
              <div class="text-muted">该目录暂无文件</div>
            <?php else: ?>
              <ul class="list-group" style="margin-bottom:0;">
                <?php foreach ($m['files'] as $f): ?>
                  <li class="list-group-item" style="padding:10px 12px;">
                    <div class="pull-right text-muted">
                      <span class="glyphicon glyphicon-time"></span> <?= date('Y-m-d H:i', $f['mtime']) ?>
                    </div>
                    <div class="ellipsis">
                      <a href="<?= Url::to(['download/file', 'type' => 'personal', 'path' => $m['folder'].'/'.$f['name']]) ?>">
                        <span class="glyphicon glyphicon-file text-info"></span>
                        <?= Html::encode($f['name']) ?>
                      </a>
                    </div>
                    <?php if (!empty($isRoot) || (!empty($currentStudentNo) && $currentStudentNo === $m['folder'])): ?>
                      <?= Html::a('删除', ['personalwork/delete', 'folder' => $m['folder'], 'name' => $f['name']], [
                        'class' => 'btn btn-xs btn-danger pull-right',
                        'style' => 'margin-top:6px;',
                        'data-method' => 'post',
                        'data-confirm' => '确定删除该文件？',
                      ]) ?>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<style>
.ellipsis { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
