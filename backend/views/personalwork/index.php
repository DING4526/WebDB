<?php
/**
 * Ding 2310724
 * 个人作业文件列表视图/下载（改版：左侧成员列表 + 右侧文件表格）
 */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '个人作业';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');

// 选中目录：root 可通过 ?folder=xxx 切换；普通用户默认自己的 $currentStudentNo
$selectedFolder = Yii::$app->request->get('folder');
if (empty($selectedFolder) && !empty($currentStudentNo)) $selectedFolder = $currentStudentNo;

// 构造 folder => files 映射，便于右侧展示
$memberMap = [];
if (!empty($members)) {
  foreach ($members as $m) $memberMap[$m['folder']] = $m['files'];
}
$selectedFiles = (!empty($selectedFolder) && isset($memberMap[$selectedFolder])) ? $memberMap[$selectedFolder] : [];
?>

<div class="pw-page">

  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">按学号（或姓名）目录归档个人作业，支持下载与权限删除</div>
      </div>
      <div class="adm-actions">
        <?php if (!empty($canUpload)): ?>
          <a class="btn btn-primary" href="#uploadBox">
            上传个人作业
          </a>
        <?php endif; ?>
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <!-- 统计卡 -->
  <div class="adm-stats" style="margin-top: 14px;">
    <div class="adm-stat-card">
      <div class="adm-stat-value"><?= Html::encode($memberCount) ?></div>
      <div class="adm-stat-label">成员目录</div>
    </div>
    <div class="adm-stat-card">
      <div class="adm-stat-value"><?= Html::encode($fileCount) ?></div>
      <div class="adm-stat-label">作业文件</div>
    </div>
    <div class="adm-stat-card">
      <div class="adm-stat-value">
        <span class="adm-badge adm-badge-active">本地存储</span>
        <span class="adm-badge adm-badge-active">目录扫描</span>
      </div>
      <div class="adm-stat-label">路径：<code>data/personal/学号/</code></div>
    </div>
  </div>

  <?php if (!empty($canUpload)): ?>
    <!-- 上传区 -->
    <div id="uploadBox" class="adm-card">
      <div class="adm-card-head">
        <h3 class="adm-card-title">上传个人作业</h3>
      </div>
      <div class="adm-card-body adm-form">
        <?php if (empty($currentStudentNo) && empty($isRoot)): ?>
          <div class="alert alert-warning" style="margin:0;">
            请先在首页补充学号后再上传。
          </div>
        <?php else: ?>
          <?= Html::beginForm(['personalwork/upload'], 'post', ['enctype' => 'multipart/form-data']) ?>
            <div class="row">
              <div class="col-sm-4">
                <label class="control-label">学号目录</label>
                <?php if (!empty($isRoot)): ?>
                  <?= Html::textInput('student_no', $selectedFolder ?: $currentStudentNo, [
                    'class' => 'form-control',
                    'placeholder' => '如：2310xxxx',
                  ]) ?>
                <?php else: ?>
                  <?= Html::hiddenInput('student_no', $currentStudentNo) ?>
                  <div class="form-control" style="background:#f9fafb;"><?= Html::encode($currentStudentNo) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-sm-5">
                <label class="control-label">选择文件</label>
                <input type="file" name="file" required class="form-control">
              </div>
              <div class="col-sm-3">
                <label class="control-label" style="visibility:hidden;">提交</label>
                <?= Html::submitButton('上传', ['class' => 'btn btn-success btn-block']) ?>
              </div>
            </div>
          <?= Html::endForm() ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if (empty($members)): ?>
    <div class="alert alert-warning" style="border-radius:18px; margin-top:14px;">
      <strong>目录为空：</strong>请按 <code>data/personal/学号(或姓名)/</code> 放置个人作业文件。
    </div>
  <?php else: ?>

    <div class="row">
      <!-- 左栏：成员目录 -->
      <div class="col-md-4">
        <div class="adm-card">
          <div class="adm-card-head">
            <h3 class="adm-card-title">成员目录</h3>
          </div>
          <div class="adm-card-body">
            <input id="pwSearch" type="text" class="form-control" placeholder="搜索学号/姓名目录…" style="margin-bottom:12px;">
            <div class="adm-hint" style="margin-bottom:10px;">点击目录查看文件</div>

            <div class="list-group pw-list" id="pwFolderList" style="max-height:520px; overflow:auto;">
              <?php foreach ($members as $m): ?>
                <?php
                  $active = (!empty($selectedFolder) && $selectedFolder === $m['folder']);
                  $count = is_array($m['files']) ? count($m['files']) : 0;
                ?>
                <a class="list-group-item <?= $active ? 'active' : '' ?>"
                   href="<?= Url::to(['personalwork/index', 'folder' => $m['folder']]) ?>"
                   data-folder="<?= Html::encode($m['folder']) ?>"
                   style="border-radius:12px; margin-bottom:6px; border:1px solid rgba(0,0,0,.08);">
                  <span class="glyphicon glyphicon-user"></span>
                  <span style="margin-left:6px; font-weight:700;"><?= Html::encode($m['folder']) ?></span>
                  <span class="badge"><?= $count ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- 右栏：文件表格 -->
      <div class="col-md-8">
        <div class="adm-card">
          <div class="adm-card-head">
            <h3 class="adm-card-title">
              <?php if (!empty($selectedFolder)): ?>
                当前目录：<strong><?= Html::encode($selectedFolder) ?></strong>
              <?php else: ?>
                文件列表
              <?php endif; ?>
            </h3>
          </div>

          <div class="adm-grid" style="padding:0;">
            <?php if (empty($selectedFolder)): ?>
              <div style="padding:24px; color:#6b7280;">
                请从左侧选择一个成员目录查看文件。
              </div>
            <?php elseif (empty($selectedFiles)): ?>
              <div style="padding:24px; color:#6b7280;">
                该目录暂无文件。
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th style="width:60%;">文件名</th>
                      <th style="width:25%;">更新时间</th>
                      <th style="width:15%;" class="text-right">操作</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($selectedFiles as $f): ?>
                    <tr>
                      <td style="font-weight:700;">
                        <span class="glyphicon glyphicon-file text-info" style="margin-right:8px;"></span>
                        <a href="<?= Url::to(['download/file', 'type' => 'personal', 'path' => $selectedFolder.'/'.$f['name']]) ?>">
                          <?= Html::encode($f['name']) ?>
                        </a>
                      </td>
                      <td class="adm-muted">
                        <span class="glyphicon glyphicon-time"></span>
                        <?= date('Y-m-d H:i', $f['mtime']) ?>
                      </td>
                      <td class="text-right adm-actions-col">
                        <a class="btn btn-xs btn-ghost"
                           href="<?= Url::to(['download/file', 'type' => 'personal', 'path' => $selectedFolder.'/'.$f['name']]) ?>">
                          下载
                        </a>
                        <?php if (!empty($isRoot) || (!empty($currentStudentNo) && $currentStudentNo === $selectedFolder)): ?>
                          <?= Html::a('删除', ['personalwork/delete', 'folder' => $selectedFolder, 'name' => $f['name']], [
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
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

<script>
// 左侧搜索过滤（纯前端，不增加后端难度）
(function(){
  var input = document.getElementById('pwSearch');
  if(!input) return;
  input.addEventListener('input', function(){
    var q = (this.value || '').toLowerCase();
    var list = document.getElementById('pwFolderList');
    if(!list) return;
    var items = list.querySelectorAll('.list-group-item');
    for (var i=0;i<items.length;i++){
      var folder = (items[i].getAttribute('data-folder') || '').toLowerCase();
      items[i].style.display = folder.indexOf(q) >= 0 ? '' : 'none';
    }
  });
})();
</script>
