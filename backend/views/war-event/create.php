<?php

/**
 * Ding 2310724
 * 创建抗战事件
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $stageList array */

$this->title = '新增事件';
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-event-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'stageList' => $stageList,
    ]) ?>

</div>
