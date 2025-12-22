<?php

/**
 * Ding 2310724
 * 抗战人物列表
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WarPersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抗战人物管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-person-index">
    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('新增人物', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'role_type',
            'birth_year',
            'death_year',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status ? '展示' : '隐藏';
                },
                'filter' => [1 => '展示', 0 => '隐藏'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '快捷操作',
                'template' => '{update} {toggle-status} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary']);
                    },
                    'toggle-status' => function ($url, $model) {
                        $isPublished = (bool)$model->status;
                        return Html::a($isPublished ? '下线' : '发布', ['toggle-status', 'id' => $model->id], [
                            'class' => 'btn btn-xs ' . ($isPublished ? 'btn-warning' : 'btn-success'),
                            'data-method' => 'post',
                            'data-pjax' => 0,
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('删除', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-danger',
                            'data' => [
                                'confirm' => '确认删除该人物？',
                                'method' => 'post',
                            ],
                            'data-pjax' => 0,
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'min-width:220px;'],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
