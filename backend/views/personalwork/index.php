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

<?php if (!empty($canUpload)): ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-upload"></span> 上传个人作业
    </div>
    <div class="panel-body">
      <?php if (empty($currentStudentNo) && empty($isRoot)): ?>
        <div class="alert alert-warning" style="margin-bottom:0;">请先在首页补充学号后再上传。</div>
      <?php else: ?>
        <?= Html::beginForm(['personalwork/upload'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'form-inline']) ?>
          <?php if (!empty($isRoot)): ?>
            <div class="form-group mb10" style="margin-right:8px;">
              <?= Html::textInput('student_no', $currentStudentNo, ['class' => 'form-control', 'placeholder' => '学号目录']) ?>
            </div>
          <?php else: ?>
            <?= Html::hiddenInput('student_no', $currentStudentNo) ?>
            <div class="form-group mb10" style="margin-right:8px;">
              <div class="form-control" disabled><?= Html::encode($currentStudentNo) ?></div>
            </div>
          <?php endif; ?>
          <div class="form-group mb10" style="margin-right:8px;">
            <input type="file" name="file" required class="form-control">
          </div>
          <?= Html::submitButton('上传', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm() ?>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php if (empty($members)): ?>
  <div class="alert alert-warning">
    data/personal 目录为空：请按 data/personal/学号(或姓名)/ 放置个人作业文件。
  </div>
<?php else: ?>
  <div class="row">
    <?php foreach ($members as $m): ?>
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <span class="glyphicon glyphicon-user"></span>
            <?= Html::encode($m['folder']) ?>
          </div>
          <div class="panel-body" style="padding:12px 15px;">
            <?php if (empty($m['files'])): ?>
              <div class="text-muted">该目录暂无文件</div>
            <?php else: ?>
              <ul class="list-group" style="margin-bottom:0;">
                <?php foreach ($m['files'] as $f): ?>
                  <li class="list-group-item" style="padding:10px 12px;">
                    <div class="pull-right text-muted"><?= date('Y-m-d H:i', $f['mtime']) ?></div>
                    <a href="<?= Url::to(['download/file', 'type' => 'personal', 'path' => $m['folder'].'/'.$f['name']]) ?>">
                      <?= Html::encode($f['name']) ?>
                    </a>
                    <?php if (!empty($isRoot) || (!empty($currentStudentNo) && $currentStudentNo === $m['folder'])): ?>
                      <?= Html::a('删除', ['personalwork/delete', 'folder' => $m['folder'], 'name' => $f['name']], [
                        'class' => 'btn btn-xs btn-danger',
                        'style' => 'margin-left:10px;',
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
