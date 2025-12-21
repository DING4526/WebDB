<?php

/**
 * 抗战留言审核列表
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\WarMessage;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WarMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '留言审核';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="war-message-index">
    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('一键通过全部待审', ['approve-all'], [
            'class' => 'btn btn-success',
            'data-method' => 'post',
            'data-confirm' => '确认通过所有待审留言？',
        ]) ?>
        <?= Html::a('一键拒绝全部待审', ['reject-all'], [
            'class' => 'btn btn-warning',
            'data-method' => 'post',
            'data-confirm' => '确认拒绝所有待审留言？',
        ]) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nickname',
            'content:ntext',
            [
                'attribute' => 'target_type',
                'filter' => ['event' => '事件', 'person' => '人物'],
                'value' => function ($model) {
                    return $model->target_type === 'person' ? '人物' : '事件';
                },
            ],
            'target_id',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    $labels = WarMessage::statusLabels();
                    return $labels[$model->status] ?? '未知';
                },
                'filter' => WarMessage::statusLabels(),
            ],
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{approve} {reject}',
                'buttons' => [
                    'approve' => function ($url, $model) {
                        return Html::a('通过', ['approve', 'id' => $model->id], [
                            'data-method' => 'post',
                            'class' => 'btn btn-xs btn-success',
                            'title' => '通过',
                        ]);
                    },
                    'reject' => function ($url, $model) {
                        return Html::a('拒绝', ['reject', 'id' => $model->id], [
                            'data-method' => 'post',
                            'class' => 'btn btn-xs btn-danger',
                            'title' => '拒绝',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
