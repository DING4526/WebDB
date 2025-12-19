<?php

/**
 * Ding 2310724
 * 团队作业文件列表视图/下载
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '团队作业';
$this->params['breadcrumbs'][] = $this->title;
?>

<h2><?= Html::encode($this->title) ?></h2>

<?php if (empty($files)): ?>
  <div class="alert alert-warning">
    data/team 目录为空：请把需求文档/设计文档/实现文档/用户手册/PPT/数据库文件等放入该目录。
  </div>
<?php else: ?>
  <ul style="line-height: 2.2; font-size: 16px;">
    <?php foreach ($files as $f): ?>
      <li>
        <a href="<?= Url::to(['download/file', 'type' => 'team', 'path' => $f['name']]) ?>">
          <?= Html::encode($f['name']) ?>
        </a>
        <span class="text-muted" style="margin-left: 8px;">
          (<?= date('Y-m-d H:i', $f['mtime']) ?>)
        </span>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
