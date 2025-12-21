<?php

/**
 * Ding 2310724
 * 编辑抗战事件
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $stageList array */

$this->title = '编辑事件: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="war-event-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'stageList' => $stageList,
    ]) ?>

</div>
