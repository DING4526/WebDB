<?php

// http://localhost/advanced/backend/web/index.php?r=team

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Team;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '团队管理';
$this->params['breadcrumbs'][] = $this->title;
$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
?>
<div class="team-index">

    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-briefcase"></span>
            团队信息
            <?php if (!$isRoot): ?><span class="label label-default ml10">只读</span><?php endif; ?>
        </div>
        <div class="panel-body">
            <p>
                <?php if ($isRoot): ?>
                    <?= Html::a('新增团队', ['create'], ['class' => 'btn btn-success']) ?>
                <?php else: ?>
                    <span class="text-muted">仅 root 可新增/编辑团队。</span>
                <?php endif; ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-striped table-condensed'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    'topic',
                    [
                        'attribute' => 'intro',
                        'format' => 'ntext',
                        'contentOptions' => ['style' => 'max-width:320px; white-space:normal;'],
                    ],
                    [
                        'attribute' => 'status',
                        'value' => fn($m) => Team::getStatusList()[$m->status] ?? $m->status,
                        'filter' => Team::getStatusList(),
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => $isRoot ? '{view} {update} {delete}' : '{view}',
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
