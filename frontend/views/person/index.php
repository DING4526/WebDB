<?php

/**
 * Ding 2310724
 * 前台人物列表视图
 */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = '抗战人物';
?>

<div class="person-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function ($model) {
            /** @var \common\models\WarPerson $model */
            return '<div class="panel panel-default">' .
                '<div class="panel-heading"><strong>' . Html::encode($model->name) . '</strong> ' .
                '<span class="text-muted">' . Html::encode($model->role_type) . '</span></div>' .
                '<div class="panel-body">' .
                '<p>' . Html::encode($model->intro) . '</p>' .
                Html::a('查看详情', ['view', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) .
                '</div></div>';
        },
        'summary' => '',
        'emptyText' => '<div class="alert alert-info">暂无人物数据，请先在后台录入。</div>',
        'options' => ['class' => 'row'],
        'itemOptions' => ['class' => 'col-md-4 col-sm-6'],
    ]) ?>
</div>
