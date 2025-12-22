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
/* @var $relationMap array */

$this->title = '编辑事件: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="war-event-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <div class="panel panel-default">
        <div class="panel-heading">一体化编辑</div>
        <div class="panel-body">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab-basic" aria-controls="tab-basic" role="tab" data-toggle="tab">基本信息</a></li>
                <li role="presentation"><a href="#tab-relations" aria-controls="tab-relations" role="tab" data-toggle="tab">关联 & 媒资</a></li>
            </ul>
            <div class="tab-content" style="padding-top:15px;">
                <div role="tabpanel" class="tab-pane active" id="tab-basic">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'stageList' => $stageList,
                    ]) ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab-relations">
                    <p class="text-muted">在同一处完成人物绑定、媒资上传与维护。</p>
                    <?= $this->render('_relations_media', [
                        'model' => $model,
                        'personOptions' => $personOptions,
                        'relationForm' => $relationForm,
                        'mediaForm' => $mediaForm,
                        'mediaList' => $mediaList,
                        'relationMap' => $relationMap,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
