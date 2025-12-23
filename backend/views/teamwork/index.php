<?php

/**
 * Ding 2310724
 * 团队作业文件列表视图/下载
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '团队作业';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
$currentUserId = Yii::$app->user->id;
?>

<div class="tw-page">
  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">集中管理需求 / 设计 / 实现 / 用户手册 / 展示材料等</div>
      </div>
      <div class="adm-actions">
        <?php if (!empty($canUpload)): ?>
          <a class="btn btn-primary" href="#uploadBox">
            上传文件
          </a>
        <?php endif; ?>
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <!-- 顶部统计卡 -->
  <div class="adm-stats" style="margin-top: 14px;">
    <div class="adm-stat-card">
      <div class="adm-stat-value"><?= Html::encode($fileCount) ?></div>
      <div class="adm-stat-label">当前文件数量</div>
    </div>
    <div class="adm-stat-card">
      <div class="adm-stat-value">
        <span class="adm-badge adm-badge-active">本地存储</span>
      </div>
      <div class="adm-stat-label">路径：<code>data/team/</code></div>
    </div>
  </div>

  <?php if (!empty($canUpload)): ?>
    <!-- 上传区 -->
    <div id="uploadBox" class="adm-card">
      <div class="adm-card-head">
        <h3 class="adm-card-title">上传团队作业</h3>
      </div>
      <div class="adm-card-body adm-form">
        <?= Html::beginForm(['teamwork/upload'], 'post', ['enctype' => 'multipart/form-data', 'id' => 'twUploadForm']) ?>
          <div class="tw-upload-modern">
            <div class="tw-upload-hint">
              <span class="tw-upload-icon">📎</span>
              <div>
                <div class="tw-upload-hint-title">上传团队作业文件</div>
                <div class="tw-upload-hint-desc">支持 pdf / docx / ppt / zip 等格式</div>
              </div>
            </div>
            
            <div class="tw-upload-action">
              <input type="file" name="file" required id="twFileInput" style="display:none;">
              <button type="button" class="btn btn-primary btn-tw-trigger" id="twFileTrigger">
                <span class="glyphicon glyphicon-cloud-upload"></span>
                <span id="twFileName">选择文件并上传</span>
              </button>
            </div>
          </div>
        <?= Html::endForm() ?>
      </div>
    </div>
  <?php endif; ?>

<style>
.tw-upload-modern {
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 20px;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 16px;
  border: 2px dashed rgba(0,0,0,0.12);
}

.tw-upload-hint {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.tw-upload-icon {
  font-size: 32px;
}

.tw-upload-hint-title {
  font-weight: 900;
  font-size: 16px;
  color: #0f172a;
  margin-bottom: 4px;
}

.tw-upload-hint-desc {
  font-size: 13px;
  color: #64748b;
  font-weight: 700;
}

.tw-upload-action {
  flex-shrink: 0;
}

.btn-tw-trigger {
  border-radius: 12px;
  padding: 14px 28px;
  font-weight: 900;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border: 0;
  box-shadow: 0 4px 12px rgba(16,185,129,0.25);
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-tw-trigger:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16,185,129,0.35);
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.btn-tw-trigger:active {
  transform: translateY(0);
}

.btn-tw-trigger .glyphicon {
  font-size: 18px;
}

@media (max-width: 768px) {
  .tw-upload-modern {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }
  
  .btn-tw-trigger {
    width: 100%;
    justify-content: center;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var twFileInput = document.getElementById('twFileInput');
  var twFileTrigger = document.getElementById('twFileTrigger');
  var twFileName = document.getElementById('twFileName');
  var twUploadForm = document.getElementById('twUploadForm');
  
  if (twFileTrigger && twFileInput) {
    twFileTrigger.addEventListener('click', function() {
      twFileInput.click();
    });
    
    twFileInput.addEventListener('change', function() {
      if (twFileInput.files && twFileInput.files.length > 0) {
        var file = twFileInput.files[0];
        twFileName.textContent = file.name;
        // Auto submit
        twUploadForm.submit();
      }
    });
  }
});
</script>

  <?php if (empty($files)): ?>
    <div class="alert alert-warning" style="border-radius:18px; margin-top:14px;">
      <strong>目录为空：</strong>请把需求文档 / 设计文档 / 实现文档 / 用户手册 / PPT / 数据库文件等放入 <code>data/team</code>。
    </div>
  <?php else: ?>
    <!-- 文件列表 -->
    <div class="adm-card">
      <div class="adm-card-head">
        <h3 class="adm-card-title">文件列表</h3>
        <span class="adm-pill"><span class="adm-dot"></span> 文件总数：<?= count($files) ?></span>
      </div>
      <div class="adm-grid" style="padding:0;">
        <div class="table-responsive">
          <table class="table table-hover">
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
                <td style="font-weight:700;">
                  <span class="glyphicon glyphicon-file text-primary" style="margin-right:8px;"></span>
                  <a href="<?= Url::to(['download/file', 'type' => 'team', 'path' => $f['name']]) ?>">
                    <?= Html::encode($f['display'] ?? $f['name']) ?>
                  </a>
                  <div class="adm-hint" style="font-size:12px; margin-top:4px;">
                    <?= Html::encode($f['name']) ?>
                  </div>
                </td>
                <td>
                  <?php if (!empty($f['owner_id']) && !empty($ownerMap[$f['owner_id']])): ?>
                    <span class="adm-badge adm-badge-info"><?= Html::encode($ownerMap[$f['owner_id']]) ?></span>
                  <?php else: ?>
                    <span class="adm-muted">—</span>
                  <?php endif; ?>
                </td>
                <td class="adm-muted">
                  <span class="glyphicon glyphicon-time"></span>
                  <?= date('Y-m-d H:i', $f['mtime']) ?>
                </td>
                <td class="text-right adm-actions-col">
                  <a class="btn btn-xs btn-ghost" href="<?= Url::to(['download/file', 'type' => 'team', 'path' => $f['name']]) ?>">
                    下载
                  </a>
                  <?php if (!empty($isRoot) || (!empty($f['owner_id']) && (int)$f['owner_id'] === (int)$currentUserId)): ?>
                    <?= Html::a('删除', ['teamwork/delete', 'name' => $f['name']], [
                      'class' => 'btn btn-xs btn-soft-danger',
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
    </div>
  <?php endif; ?>
</div>
