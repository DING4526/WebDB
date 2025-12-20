<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '成员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$isRoot = !Yii::$app->user->isGuest && Yii::$app->user->identity->isRoot();
?>
<div class="team-member-view">

    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-user"></span>
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
                            'confirm' => '确认删除该成员？',
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
                    'team_id',
                    'user_id',
                    'name',
                    'student_no',
                    'role',
                    'work_scope:ntext',
                    [
                        'attribute' => 'status',
                        'value' => \common\models\TeamMember::getStatusList()[$model->status] ?? $model->status,
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
