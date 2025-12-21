<?php

/**
 * Ding 2310724
 * 前台抗战专题快捷入口
 */

use yii\helpers\Url;
?>
<div class="panel panel-info">
  <div class="panel-heading">抗战专题快速入口</div>
  <div class="panel-body">
    <a class="btn btn-primary btn-sm" href="<?= Url::to(['/timeline/index']) ?>">时间轴</a>
    <a class="btn btn-default btn-sm" href="<?= Url::to(['/person/index']) ?>">人物库</a>
  </div>
</div>
