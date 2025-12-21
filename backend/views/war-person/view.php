<?php

/**
 * Ding 2310724
 * 抗战人物详情
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web.View */
/* @var $model common\models\WarPerson */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-person-view">

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该人物？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'role_type',
            'birth_year',
            'death_year',
            'intro:ntext',
            'biography:ntext',
            [
                'attribute' => 'status',
                'value' => $model->status ? '展示' : '隐藏',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
