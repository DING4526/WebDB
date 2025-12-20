<?php
/**
 * Ding 2310724
 * 个人作业文件列表视图/下载（改版：左侧成员列表 + 右侧文件表格）
 */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '个人作业';
$this->params['breadcrumbs'][] = $this->title;

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

  <div class="pw-header">
    <div>
      <h2 class="pw-title"><?= Html::encode($this->title) ?></h2>
      <div class="pw-subtitle text-muted">按学号（或姓名）目录归档个人作业，支持下载与权限删除</div>
    </div>
    <div class="pw-actions">
      <?php if (!empty($canUpload)): ?>
        <a class="btn btn-primary" href="#uploadBox">
          <span class="glyphicon glyphicon-upload"></span> 上传个人作业
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- 统计卡 -->
  <div class="row pw-cards">
    <div class="col-sm-4">
      <div class="panel panel-default pw-card">
        <div class="panel-body">
          <div class="pw-kpi"><?= Html::encode($memberCount) ?></div>
          <div class="text-muted">成员目录</div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="panel panel-default pw-card">
        <div class="panel-body">
          <div class="pw-kpi"><?= Html::encode($fileCount) ?></div>
          <div class="text-muted">作业文件</div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="panel panel-default pw-card">
        <div class="panel-body">
          <div class="pw-badges">
            <span class="label label-success">本地存储</span>
            <span class="label label-success">目录扫描</span>
            <span class="label label-success">统一下载</span>
          </div>
          <div class="pw-tip text-muted">路径：<code>data/personal/学号/</code></div>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty($canUpload)): ?>
    <!-- 上传区（更干净） -->
    <div id="uploadBox" class="panel panel-info pw-upload">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-cloud-upload"></span> 上传个人作业
      </div>
      <div class="panel-body">
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
                <!-- <div class="help-block pw-help">建议命名：<code>作业类型_姓名(学号).pdf</code></div> -->
              </div>
              <div class="col-sm-3">
                <label class="control-label" style="visibility:hidden;">提交</label>
                <?= Html::submitButton('上传', ['class' => 'btn btn-primary btn-block']) ?>
              </div>
            </div>
          <?= Html::endForm() ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if (empty($members)): ?>
    <div class="alert alert-warning pw-empty">
      <strong>目录为空：</strong>请按 <code>data/personal/学号(或姓名)/</code> 放置个人作业文件。
    </div>
  <?php else: ?>

    <div class="row">
      <!-- 左栏：成员目录 -->
      <div class="col-md-4">
        <div class="panel panel-default pw-side">
          <div class="panel-heading">
            <span class="glyphicon glyphicon-list"></span> 成员目录
          </div>
          <div class="panel-body">
            <input id="pwSearch" type="text" class="form-control" placeholder="搜索学号/姓名目录…">
            <div class="pw-sidehint text-muted">点击目录查看文件</div>

            <div class="list-group pw-list" id="pwFolderList">
              <?php foreach ($members as $m): ?>
                <?php
                  $active = (!empty($selectedFolder) && $selectedFolder === $m['folder']);
                  $count = is_array($m['files']) ? count($m['files']) : 0;
                ?>
                <a class="list-group-item <?= $active ? 'active' : '' ?>"
                   href="<?= Url::to(['personalwork/index', 'folder' => $m['folder']]) ?>"
                   data-folder="<?= Html::encode($m['folder']) ?>">
                  <span class="glyphicon glyphicon-user"></span>
                  <span class="pw-folder"><?= Html::encode($m['folder']) ?></span>
                  <span class="badge"><?= $count ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- 右栏：文件表格 -->
      <div class="col-md-8">
        <div class="panel panel-default pw-main">
          <div class="panel-heading">
            <span class="glyphicon glyphicon-folder-open"></span>
            <?php if (!empty($selectedFolder)): ?>
              当前目录：<strong><?= Html::encode($selectedFolder) ?></strong>
            <?php else: ?>
              文件列表
            <?php endif; ?>
          </div>

          <div class="panel-body" style="padding:0;">
            <?php if (empty($selectedFolder)): ?>
              <div class="pw-placeholder">
                请从左侧选择一个成员目录查看文件。
              </div>
            <?php elseif (empty($selectedFiles)): ?>
              <div class="pw-placeholder">
                该目录暂无文件。
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover pw-table">
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
                      <td class="pw-filecell">
                        <span class="glyphicon glyphicon-file text-info"></span>
                        <a class="pw-filename"
                           href="<?= Url::to(['download/file', 'type' => 'personal', 'path' => $selectedFolder.'/'.$f['name']]) ?>">
                          <?= Html::encode($f['name']) ?>
                        </a>
                      </td>
                      <td class="text-muted">
                        <span class="glyphicon glyphicon-time"></span>
                        <?= date('Y-m-d H:i', $f['mtime']) ?>
                      </td>
                      <td class="text-right">
                        <a class="btn btn-xs btn-default"
                           href="<?= Url::to(['download/file', 'type' => 'personal', 'path' => $selectedFolder.'/'.$f['name']]) ?>">
                          下载
                        </a>
                        <?php if (!empty($isRoot) || (!empty($currentStudentNo) && $currentStudentNo === $selectedFolder)): ?>
                          <?= Html::a('删除', ['personalwork/delete', 'folder' => $selectedFolder, 'name' => $f['name']], [
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

<style>
/* 页面整体 */
.pw-page { padding: 8px 6px; }
.pw-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 12px; }
.pw-title { margin:0; font-weight:800; }
.pw-subtitle { margin-top:6px; }
.pw-actions { margin-bottom: 4px; }

/* 统计卡 */
.pw-cards { margin-bottom: 12px; }
.pw-card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.05); border: 1px solid #eef1f5; }
.pw-kpi { font-size: 34px; font-weight: 800; line-height: 1; margin-bottom: 8px; }
.pw-badges .label { margin-right: 6px; display:inline-block; margin-bottom: 6px; }
.pw-tip { margin-top: 6px; }

/* 上传 */
.pw-upload { border-radius: 10px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,.04); }
.pw-help { margin-top: 6px; margin-bottom: 0; }

/* 左侧栏 */
.pw-side { border-radius: 10px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,.05); border: 1px solid #eef1f5; }
.pw-list { margin-top: 10px; max-height: 520px; overflow:auto; }
.pw-sidehint { margin-top: 8px; font-size: 12px; }
.pw-folder { margin-left: 6px; }

/* 右侧 */
.pw-main { border-radius: 10px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,.05); border: 1px solid #eef1f5; }
.pw-table thead th { background:#f7f9fb; border-bottom: 1px solid #e9edf3; }
.pw-filecell .glyphicon { margin-right: 8px; }
.pw-filename { font-weight: 600; }
.pw-placeholder { padding: 24px; color:#6b7280; }

/* 空态 */
.pw-empty { border-radius: 10px; }
</style>
