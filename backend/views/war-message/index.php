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
            'type' => '人物',
            'label' => $person ? '人物：' . $person->name : '人物(ID:' . $model->target_id . ')',
            'name' => $person->name ?? ('ID:' . $model->target_id),
            'basic_info' => $person ? [
                'id' => $person->id,
                '姓名' => $person->name,
                '性别' => $person->gender ?? '未知',
                '出生日期' => $person->birth_date ?? '未知',
                '逝世日期' => $person->death_date ?? '未知',
                '简介' => $person->summary ?? '暂无简介',
            ] : null,
            'link' => "/advanced/frontend/web/index.php?r=person%2Fview&id={$model->target_id}",
        ];
    }

    if (!isset($eventCache[$model->target_id])) {
        $eventCache[$model->target_id] = WarEvent::findOne($model->target_id);
    }
    $event = $eventCache[$model->target_id];
    
    return [
        'type' => '事件',
        'label' => $event ? '事件：' . $event->title : '事件(ID:' . $model->target_id . ')',
        'name' => $event->title ?? ('ID:' . $model->target_id),
        'basic_info' => $event ? [
            'id' => $event->id,
            '事件标题' => $event->title,
            '发生时间' => $event->happened_at ?? '未知',
            '发生地点' => $event->location ?? '未知',
            '事件类型' => $event->type ?? '未知',
            '简介' => $event->description ?? '暂无简介',
        ] : null,
        'link' => "/advanced/frontend/web/index.php?r=timeline%2Findex&event_id={$model->target_id}",
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
                                            'data-target-type' => $info['type'],
                                            'data-target-name' => $info['name'],
                                            'data-target-info' => json_encode($info['basic_info'], JSON_UNESCAPED_UNICODE),
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
                                            'data-target-type' => $info['type'],
                                            'data-target-name' => $info['name'],
                                            'data-target-info' => json_encode($info['basic_info'], JSON_UNESCAPED_UNICODE),
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
                                            'data-target-type' => $info['type'],
                                            'data-target-name' => $info['name'],
                                            'data-target-info' => json_encode($info['basic_info'], JSON_UNESCAPED_UNICODE),
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
        <div class="modal-dialog modal-lg"> <!-- 改为大模态框 -->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">留言详情</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title">留言信息</h3>
                                </div>
                                <div class="panel-body">
                                    <p><strong>昵称：</strong><span id="md-nickname"></span></p>
                                    <p><strong>时间：</strong><span id="md-time"></span></p>
                                    <p><strong>内容：</strong></p>
                                    <div id="md-content" style="white-space: pre-wrap; border: 1px solid #eee; padding: 10px; border-radius: 4px; background-color: #f9f9f9;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h3 class="panel-title" id="md-target-title"></h3>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-bordered table-striped" id="md-target-info">
                                        <!-- 目标信息将通过JavaScript动态填充 -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
$(document).on('click', '.js-view-message', function () {
    $('#md-nickname').text($(this).data('nickname'));
    $('#md-time').text($(this).data('time'));
    $('#md-content').text($(this).data('content'));
    
    // 设置目标标题
    var targetType = $(this).data('target-type');
    var targetName = $(this).data('target-name');
    $('#md-target-title').text(targetType + '信息：' + targetName);
    
    // 解析并显示目标信息
    var targetInfo = JSON.parse($(this).data('target-info'));
    var html = '';
    
    if (targetInfo) {
        $.each(targetInfo, function(key, value) {
            if (value && value !== '未知' && value !== '暂无简介') {
                // 对简介字段特殊处理，换行显示
                if (key === '简介' || key === 'description') {
                    html += '<tr><th style="width: 100px;">' + key + '</th><td style="white-space: pre-wrap;">' + value + '</td></tr>';
                } else {
                    html += '<tr><th style="width: 100px;">' + key + '</th><td>' + value + '</td></tr>';
                }
            }
        });
    } else {
        html = '<tr><td colspan="2" class="text-muted">未能获取到目标信息</td></tr>';
    }
    
    $('#md-target-info').html(html);
    $('#messageDetailModal').modal('show');
});
JS;
$this->registerJs($js);
?>
