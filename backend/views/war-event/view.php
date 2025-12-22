<?php

/**
 * Ding 2310724
 * 抗战事件详情
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $personOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */
/* @var $relationMap array */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-event-view">

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该事件？',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a($model->status ? '下线' : '发布', ['toggle-status', 'id' => $model->id], [
            'class' => 'btn ' . ($model->status ? 'btn-warning' : 'btn-success'),
            'data-method' => 'post',
        ]) ?>
    </p>

    <div class="panel panel-default">
        <div class="panel-heading">基本信息</div>
        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'title',
                    'event_date',
                    [
                        'label' => '阶段',
                        'value' => $model->stage->name ?? '',
                    ],
                    'location',
                    'summary:ntext',
                    'content:ntext',
                    [
                        'attribute' => 'status',
                        'value' => $model->status ? '发布' : '草稿',
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

    <?= $this->render('_relations_media_view', [
        'model' => $model,
        'mediaList' => $mediaList,
        'relationMap' => $relationMap,
    ]) ?>
</div>
