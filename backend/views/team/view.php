<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Team */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '团队管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$isRoot = ($user = Yii::$app->user->getUser()) && $user->isRoot();
?>
<div class="team-view">

    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-briefcase"></span>
            <?= Html::encode($this->title) ?>
            <?php if (!$isRoot): ?><span class="label label-default ml10">只读</span><?php endif; ?>
        </div>
        <div class="panel-body">
            <p>
                <?php if ($isRoot): ?>
                    <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('删除', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => '确认删除该团队？',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php else: ?>
                    <span class="text-muted">仅 root 可编辑/删除。</span>
                <?php endif; ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'topic',
                    'intro:ntext',
                    [
                        'attribute' => 'status',
                        'value' => \common\models\Team::getStatusList()[$model->status] ?? $model->status,
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['datetime', 'php:Y-m-d H:i'],
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => ['datetime', 'php:Y-m-d H:i'],
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
