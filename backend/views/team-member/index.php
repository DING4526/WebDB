<?php

// http://localhost/advanced/backend/web/index.php?r=team-member

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\TeamMember;
use yii\helpers\ArrayHelper;
use common\models\Team;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeamMemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Team Members';
$this->params['breadcrumbs'][] = $this->title;

$teamFilter = ArrayHelper::map(Team::find()->all(), 'id', 'name');

$userList = ArrayHelper::map(
    User::find()->orderBy(['id' => SORT_DESC])->all(),
    'id',
    'username'
);

?>
<div class="team-member-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Team Member', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'team_id',
                'value' => fn($m) => $m->team ? $m->team->name : '',
                'filter' => $teamFilter,
            ],
            [
                'attribute' => 'user_id',
                'value' => fn($m) => $m->user ? $m->user->username : '',
                'filter' => $userList,
            ],
            'name',
            'student_no',
            //'role',
            //'work_scope:ntext',
            [
                'attribute' => 'status',
                'value' => fn($m) => TeamMember::getStatusList()[$m->status] ?? $m->status,
                'filter' => TeamMember::getStatusList(),
            ],
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
