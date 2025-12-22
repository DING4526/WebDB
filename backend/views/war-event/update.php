<?php

/**
 * Ding 2310724
 * 编辑抗战事件
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $stageList array */
/* @var $personOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */

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

    <hr>

    <h3 class="mt20">关联与媒资</h3>
    <p class="text-muted">在同一页面完成人物绑定、媒资上传与维护。</p>
    <?= $this->render('_relations_media', [
        'model' => $model,
        'personOptions' => $personOptions,
        'relationForm' => $relationForm,
        'mediaForm' => $mediaForm,
        'mediaList' => $mediaList,
    ]) ?>
</div>
