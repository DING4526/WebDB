<?php

/**
 * 抗战留言审核列表
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\WarMessage;
use common\models\WarPerson;
use common\models\WarEvent;

/* @var $this yii\web\View */
/* @var $pendingSearch backend\models\WarMessageSearch */
/* @var $approvedSearch backend\models\WarMessageSearch */
/* @var $rejectedSearch backend\models\WarMessageSearch */
/* @var $pendingProvider yii\data\ActiveDataProvider */
/* @var $approvedProvider yii\data\ActiveDataProvider */
/* @var $rejectedProvider yii\data\ActiveDataProvider */

$this->title = '留言审核';
$this->params['breadcrumbs'][] = $this->title;

// 简易缓存，避免重复查询
$personCache = [];
$eventCache = [];

$getTargetInfo = function ($model) use (&$personCache, &$eventCache) {
    if ($model->target_type === 'person') {
        if (!isset($personCache[$model->target_id])) {
            $personCache[$model->target_id] = WarPerson::findOne($model->target_id);
        }
        $person = $personCache[$model->target_id];
        return [
            'label' => $person ? '人物：' . $person->name : '人物(ID:' . $model->target_id . ')',
            'link' => "/advanced/frontend/web/index.php?r=person%2Fview&id={$model->target_id}",
            'name' => $person->name ?? ('ID:' . $model->target_id),
        ];
    }

    if (!isset($eventCache[$model->target_id])) {
        $eventCache[$model->target_id] = WarEvent::findOne($model->target_id);
    }
    $event = $eventCache[$model->target_id];
    return [
        'label' => $event ? '事件：' . $event->title : '事件(ID:' . $model->target_id . ')',
        'link' => "/advanced/frontend/web/index.php?r=timeline%2Findex&event_id={$model->target_id}",
        'name' => $event->title ?? ('ID:' . $model->target_id),
    ];
};
?>

<div class="war-message-index">
    <h2><?= Html::encode($this->title) ?></h2>

    <div class="row">
        <div class="col-md-12 mb20">
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
        </div>
    </div>

    <div class="panel-group" id="msg-accordion">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#msg-accordion" href="#pending-panel" aria-expanded="true">待审核</a>
                </h4>
            </div>
            <div id="pending-panel" class="panel-collapse collapse in">
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $pendingProvider,
                        'filterModel' => $pendingSearch,
                        'summary' => false,
                        'columns' => [
                            'nickname',
                            [
                                'attribute' => 'content',
                                'format' => 'ntext',
                            ],
                            [
                                'label' => '目标',
                                'format' => 'raw',
                                'value' => function ($model) use ($getTargetInfo) {
                                    $info = $getTargetInfo($model);
                                    return Html::a($info['label'], $info['link'], ['target' => '_blank']);
                                },
                            ],
                            'created_at:datetime',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {approve} {reject}',
                                'buttons' => [
                                    'approve' => function ($url, $model) {
                                        return Html::a('通过', ['approve', 'id' => $model->id], [
                                            'data-method' => 'post',
                                            'class' => 'btn btn-xs btn-success',
                                        ]);
                                    },
                                    'reject' => function ($url, $model) {
                                        return Html::a('拒绝', ['reject', 'id' => $model->id], [
                                            'data-method' => 'post',
                                            'class' => 'btn btn-xs btn-danger',
                                        ]);
                                    },
                                    'view' => function ($url, $model) use ($getTargetInfo) {
                                        $info = $getTargetInfo($model);
                                        return Html::button('查看', [
                                            'class' => 'btn btn-xs btn-info js-view-message',
                                            'data-nickname' => $model->nickname,
                                            'data-content' => $model->content,
                                            'data-target' => $info['label'],
                                            'data-target-link' => $info['link'],
                                            'data-time' => Yii::$app->formatter->asDatetime($model->created_at),
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>

        <div class="panel panel-success">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#msg-accordion" href="#approved-panel" aria-expanded="false">已通过</a>
                </h4>
            </div>
            <div id="approved-panel" class="panel-collapse collapse">
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $approvedProvider,
                        'filterModel' => $approvedSearch,
                        'summary' => false,
                        'columns' => [
                            'nickname',
                            [
                                'attribute' => 'content',
                                'format' => 'ntext',
                            ],
                            [
                                'label' => '目标',
                                'format' => 'raw',
                                'value' => function ($model) use ($getTargetInfo) {
                                    $info = $getTargetInfo($model);
                                    return Html::a($info['label'], $info['link'], ['target' => '_blank']);
                                },
                            ],
                            'created_at:datetime',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {revert}',
                                'buttons' => [
                                    'view' => function ($url, $model) use ($getTargetInfo) {
                                        $info = $getTargetInfo($model);
                                        return Html::button('查看', [
                                            'class' => 'btn btn-xs btn-info js-view-message',
                                            'data-nickname' => $model->nickname,
                                            'data-content' => $model->content,
                                            'data-target' => $info['label'],
                                            'data-target-link' => $info['link'],
                                            'data-time' => Yii::$app->formatter->asDatetime($model->created_at),
                                        ]);
                                    },
                                    'revert' => function ($url, $model) {
                                        return Html::a('撤销', ['revert', 'id' => $model->id], [
                                            'data-method' => 'post',
                                            'class' => 'btn btn-xs btn-warning',
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#msg-accordion" href="#rejected-panel" aria-expanded="false">已拒绝</a>
                </h4>
            </div>
            <div id="rejected-panel" class="panel-collapse collapse">
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $rejectedProvider,
                        'filterModel' => $rejectedSearch,
                        'summary' => false,
                        'columns' => [
                            'nickname',
                            [
                                'attribute' => 'content',
                                'format' => 'ntext',
                            ],
                            [
                                'label' => '目标',
                                'format' => 'raw',
                                'value' => function ($model) use ($getTargetInfo) {
                                    $info = $getTargetInfo($model);
                                    return Html::a($info['label'], $info['link'], ['target' => '_blank']);
                                },
                            ],
                            'created_at:datetime',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {revert}',
                                'buttons' => [
                                    'view' => function ($url, $model) use ($getTargetInfo) {
                                        $info = $getTargetInfo($model);
                                        return Html::button('查看', [
                                            'class' => 'btn btn-xs btn-info js-view-message',
                                            'data-nickname' => $model->nickname,
                                            'data-content' => $model->content,
                                            'data-target' => $info['label'],
                                            'data-target-link' => $info['link'],
                                            'data-time' => Yii::$app->formatter->asDatetime($model->created_at),
                                        ]);
                                    },
                                    'revert' => function ($url, $model) {
                                        return Html::a('撤销', ['revert', 'id' => $model->id], [
                                            'data-method' => 'post',
                                            'class' => 'btn btn-xs btn-warning',
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 查看留言 Modal -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">留言详情</h4>
                </div>
                <div class="modal-body">
                    <p><strong>昵称：</strong><span id="md-nickname"></span></p>
                    <p><strong>目标：</strong><a id="md-target" href="#" target="_blank"></a></p>
                    <p><strong>时间：</strong><span id="md-time"></span></p>
                    <p><strong>内容：</strong></p>
                    <div id="md-content" style="white-space: pre-wrap;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(document).on('click', '.js-view-message', function () {
    $('#md-nickname').text($(this).data('nickname'));
    $('#md-target').text($(this).data('target')).attr('href', $(this).data('target-link'));
    $('#md-time').text($(this).data('time'));
    $('#md-content').text($(this).data('content'));
    $('#messageDetailModal').modal('show');
});
JS;
$this->registerJs($js);
?>
