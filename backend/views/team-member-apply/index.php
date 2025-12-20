<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\TeamMemberApply;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeamMemberApplySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '成员申请审批';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-member-apply-index">

    <p>
        <?= Html::a('提交申请', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'student_no',
            'email:email',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },
                'filter' => TeamMemberApply::statusList(),
            ],
            [
                'attribute' => 'reviewer_id',
                'value' => function ($model) {
                    return $model->reviewer->username ?? '';
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:Y-m-d H:i'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{approve} {reject}',
                'buttons' => [
                    'approve' => function ($url, $model) {
                        if ($model->status != TeamMemberApply::STATUS_PENDING) {
                            return '';
                        }
                        return Html::a('通过', ['approve', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-success',
                            'data-method' => 'post',
                        ]);
                    },
                    'reject' => function ($url, $model) {
                        if ($model->status != TeamMemberApply::STATUS_PENDING) {
                            return '';
                        }
                        return Html::a('拒绝', ['reject', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-danger',
                            'data-method' => 'post',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
