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

<div class="tw-page">
  <div class="tw-header">
    <div>
      <h2 class="tw-title"><?= Html::encode($this->title) ?></h2>
      <div class="tw-subtitle text-muted">集中管理需求 / 设计 / 实现 / 用户手册 / 展示材料等</div>
    </div>
    <?php if (!empty($canUpload)): ?>
      <a class="btn btn-primary tw-btn" href="#uploadBox">
        <span class="glyphicon glyphicon-upload"></span> 上传文件
      </a>
    <?php endif; ?>
  </div>

  <!-- 顶部统计卡 -->
  <div class="row tw-cards">
    <div class="col-sm-4">
      <div class="panel panel-default tw-card">
        <div class="panel-body">
          <div class="tw-kpi"><?= Html::encode($fileCount) ?></div>
          <div class="text-muted">当前文件数量</div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
    </div>
  </div>

  <?php if (!empty($canUpload)): ?>
    <!-- 上传区 -->
    <div id="uploadBox" class="panel panel-info tw-upload">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-cloud-upload"></span> 上传团队作业
      </div>
      <div class="panel-body">
        <?= Html::beginForm(['teamwork/upload'], 'post', ['enctype' => 'multipart/form-data']) ?>
          <div class="row">
            <div class="col-sm-8">
              <input type="file" name="file" required class="form-control">
              <div class="help-block tw-help">支持 pdf / docx / ppt / zip 等。</div>
            </div>
            <div class="col-sm-4">
              <?= Html::submitButton('上传', ['class' => 'btn btn-primary btn-block']) ?>
            </div>
          </div>
        <?= Html::endForm() ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if (empty($files)): ?>
    <div class="alert alert-warning tw-empty">
      <strong>目录为空：</strong>请把需求文档 / 设计文档 / 实现文档 / 用户手册 / PPT / 数据库文件等放入 <code>data/team</code>。
    </div>
  <?php else: ?>
    <!-- 文件列表：用 table 立刻变高级 -->
    <div class="panel panel-default tw-tablewrap">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-folder-open"></span> 文件列表
      </div>
      <div class="table-responsive">
        <table class="table table-hover tw-table">
          <thead>
            <tr>
              <th style="width:55%;">文件</th>
              <th style="width:15%;">上传者</th>
              <th style="width:20%;">更新时间</th>
              <th style="width:10%;" class="text-right">操作</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($files as $f): ?>
            <tr>
              <td class="tw-filecell">
                <span class="glyphicon glyphicon-file text-primary"></span>
                <a class="tw-filename" href="<?= Url::to(['download/file', 'type' => 'team', 'path' => $f['name']]) ?>">
                  <?= Html::encode($f['display'] ?? $f['name']) ?>
                </a>
                <div class="tw-meta text-muted">
                  <?= Html::encode($f['name']) ?>
                </div>
              </td>
              <td>
                <?php if (!empty($f['owner_id']) && !empty($ownerMap[$f['owner_id']])): ?>
                  <span class="label label-default"><?= Html::encode($ownerMap[$f['owner_id']]) ?></span>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
              <td class="text-muted">
                <span class="glyphicon glyphicon-time"></span>
                <?= date('Y-m-d H:i', $f['mtime']) ?>
              </td>
              <td class="text-right">
                <a class="btn btn-xs btn-default" href="<?= Url::to(['download/file', 'type' => 'team', 'path' => $f['name']]) ?>">
                  下载
                </a>
                <?php if (!empty($isRoot) || (!empty($f['owner_id']) && (int)$f['owner_id'] === (int)$currentUserId)): ?>
                  <?= Html::a('删除', ['teamwork/delete', 'name' => $f['name']], [
                    'class' => 'btn btn-xs btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => '确定删除该文件？',
                  ]) ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
/* 页面整体 */
.tw-page { padding: 8px 6px; }
.tw-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 12px; }
.tw-title { margin:0; font-weight:700; }
.tw-subtitle { margin-top:6px; }
.tw-btn { margin-bottom: 4px; }

/* 卡片 */
.tw-cards { margin-bottom: 12px; }
.tw-card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.05); border: 1px solid #eef1f5; }
.tw-kpi { font-size: 34px; font-weight: 800; line-height: 1; margin-bottom: 8px; }
.tw-badges .label { margin-right: 6px; display:inline-block; margin-bottom: 6px; }
.tw-tip { margin-top: 6px; }

/* 上传 */
.tw-upload { border-radius: 10px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,.04); }
.tw-help { margin-top: 6px; margin-bottom: 0; }

/* 表格 */
.tw-tablewrap { border-radius: 10px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,.05); border: 1px solid #eef1f5; }
.tw-table thead th { background: #f7f9fb; border-bottom: 1px solid #e9edf3; }
.tw-filecell .glyphicon { margin-right: 8px; }
.tw-filename { font-weight: 600; }
.tw-meta { font-size: 12px; margin-top: 4px; }

/* 空态 */
.tw-empty { border-radius: 10px; }
</style>
