<?php

/**
 * Ding 2310724
 * 编辑抗战人物
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarPerson */
/* @var $eventOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */

$this->title = '编辑人物: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="war-person-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <hr>

    <h3 class="mt20">关联与媒资</h3>
    <p class="text-muted">在同一页面完成事件绑定、媒资上传与维护。</p>
    <?= $this->render('_relations_media', [
        'model' => $model,
        'eventOptions' => $eventOptions,
        'relationForm' => $relationForm,
        'mediaForm' => $mediaForm,
        'mediaList' => $mediaList,
    ]) ?>
</div>
