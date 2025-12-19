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

<?php if (empty($members)): ?>
  <div class="alert alert-warning">
    data/personal 目录为空：请按 data/personal/学号(或姓名)/ 放置个人作业文件。
  </div>
<?php else: ?>
  <?php foreach ($members as $m): ?>
    <h4 style="margin-top: 18px;"><?= Html::encode($m['folder']) ?></h4>
    <?php if (empty($m['files'])): ?>
      <div class="text-muted">该目录暂无文件</div>
    <?php else: ?>
      <ul style="line-height: 2.2; font-size: 16px;">
        <?php foreach ($m['files'] as $f): ?>
          <li>
            <a href="<?= Url::to(['download/file', 'type' => 'personal', 'path' => $m['folder'].'/'.$f['name']]) ?>">
              <?= Html::encode($f['name']) ?>
            </a>
            <span class="text-muted" style="margin-left: 8px;">
              (<?= date('Y-m-d H:i', $f['mtime']) ?>)
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>
