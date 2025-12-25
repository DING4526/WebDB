<?php
/**
 * 抗战留言审核（全新 UI 版：Tabs + Stats + Modern Table + Detail Modal）
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
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
$this->registerCssFile('@web/css/war-message.css');

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

// 统计
$pendingCount  = (int)$pendingProvider->getTotalCount();
$approvedCount = (int)$approvedProvider->getTotalCount();
$rejectedCount = (int)$rejectedProvider->getTotalCount();
$totalCount    = $pendingCount + $approvedCount + $rejectedCount;
?>

<div class="wm-page">

    <!-- Hero -->
    <div class="wm-hero">
        <div class="wm-hero-inner">
            <div>
                <h2><?= Html::encode($this->title) ?></h2>
                <div class="wm-desc">统一处理待审留言，支持查看关联人物/事件信息与批量操作。</div>
            </div>

            <div class="wm-hero-actions">
                <?= Html::a('一键通过待审', ['approve-all'], [
                    'class' => 'btn btn-success',
                    'data-method' => 'post',
                    'data-confirm' => '确认通过所有待审留言？',
                ]) ?>
                <?= Html::a('一键拒绝待审', ['reject-all'], [
                    'class' => 'btn btn-warning',
                    'data-method' => 'post',
                    'data-confirm' => '确认拒绝所有待审留言？',
                ]) ?>
                <?= Html::a('刷新', ['index'], [
                    'class' => 'btn btn-default',
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="wm-stats">
        <div class="wm-stat">
            <div class="k">Total</div>
            <div class="v"><?= $totalCount ?></div>
        </div>
        <div class="wm-stat">
            <div class="k">Pending</div>
            <div class="v"><?= $pendingCount ?></div>
            <div style="margin-top:8px;">
                <span class="wm-pill"><span class="wm-dot wm-dot-p"></span>待审核</span>
            </div>
        </div>
        <div class="wm-stat">
            <div class="k">Approved</div>
            <div class="v"><?= $approvedCount ?></div>
            <div style="margin-top:8px;">
                <span class="wm-pill"><span class="wm-dot wm-dot-a"></span>已通过</span>
            </div>
        </div>
        <div class="wm-stat">
            <div class="k">Rejected</div>
            <div class="v"><?= $rejectedCount ?></div>
            <div style="margin-top:8px;">
                <span class="wm-pill"><span class="wm-dot wm-dot-r"></span>已拒绝</span>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="wm-card">
        <div class="wm-card-head">
            <div class="left">
                <h3 class="title">列表</h3>
                <div class="hint">支持筛选昵称 / 内容 / 目标 / 时间等字段（按你 SearchModel 实现为准）。</div>
            </div>
            <div class="left">
                <span class="wm-pill"><span class="wm-dot wm-dot-t"></span>点击“查看”可弹出详情</span>
            </div>
        </div>

        <div class="wm-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab-pending" aria-controls="tab-pending" role="tab" data-toggle="tab">
                        待审核 <span class="wm-tab-count"><?= $pendingCount ?></span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-approved" aria-controls="tab-approved" role="tab" data-toggle="tab">
                        已通过 <span class="wm-tab-count"><?= $approvedCount ?></span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-rejected" aria-controls="tab-rejected" role="tab" data-toggle="tab">
                        已拒绝 <span class="wm-tab-count"><?= $rejectedCount ?></span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                <!-- Pending -->
                <div role="tabpanel" class="tab-pane active" id="tab-pending">
                    <div class="wm-grid">
                        <?php if ($pendingCount <= 0): ?>
                            <div class="wm-empty">暂无待审核留言</div>
                        <?php else: ?>
                            <?= GridView::widget([
                                'options' => ['class' => 'wm-grid-inner'],
                                'tableOptions' => ['class' => 'table table-hover'],
                                'dataProvider' => $pendingProvider,
                                'filterModel' => $pendingSearch,
                                'summary' => false,
                                'columns' => [
                                    [
                                        'attribute' => 'nickname',
                                        'contentOptions' => ['style' => 'font-weight:900;'],
                                    ],
                                    [
                                        'attribute' => 'content',
                                        'format' => 'ntext',
                                        'contentOptions' => ['class' => 'wm-col-content'],
                                    ],
                                    [
                                        'label' => '目标',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'wm-col-target'],
                                        'value' => function ($model) use ($getTargetInfo) {
                                            $info = $getTargetInfo($model);
                                            return Html::a($info['label'], $info['link'], ['target' => '_blank']);
                                        },
                                    ],
                                    [
                                        'attribute' => 'created_at',
                                        'format' => 'datetime',
                                        'contentOptions' => ['class' => 'wm-time'],
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'contentOptions' => ['class' => 'wm-actions'],
                                        'template' => '{view} {approve} {reject}',
                                        'buttons' => [
                                            'view' => function ($url, $model) use ($getTargetInfo) {
                                                $info = $getTargetInfo($model);
                                                return Html::button('查看', [
                                                    'class' => 'btn btn-xs btn-soft-ghost js-view-message',
                                                    'data-nickname' => Html::encode($model->nickname),
                                                    'data-content' => Html::encode($model->content),
                                                    'data-target-type' => Html::encode($info['type']),
                                                    'data-target-name' => Html::encode($info['name']),
                                                    'data-target-info' => Html::encode(json_encode($info['basic_info'], JSON_UNESCAPED_UNICODE)),
                                                    'data-time' => Html::encode(Yii::$app->formatter->asDatetime($model->created_at)),
                                                ]);
                                            },
                                            'approve' => function ($url, $model) {
                                                return Html::a('通过', ['approve', 'id' => $model->id], [
                                                    'data-method' => 'post',
                                                    'class' => 'btn btn-xs btn-soft-success',
                                                ]);
                                            },
                                            'reject' => function ($url, $model) {
                                                return Html::a('拒绝', ['reject', 'id' => $model->id], [
                                                    'data-method' => 'post',
                                                    'class' => 'btn btn-xs btn-soft-danger',
                                                ]);
                                            },
                                        ],
                                    ],
                                ],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Approved -->
                <div role="tabpanel" class="tab-pane" id="tab-approved">
                    <div class="wm-grid">
                        <?php if ($approvedCount <= 0): ?>
                            <div class="wm-empty">暂无已通过留言</div>
                        <?php else: ?>
                            <?= GridView::widget([
                                'options' => ['class' => 'wm-grid-inner'],
                                'tableOptions' => ['class' => 'table table-hover'],
                                'dataProvider' => $approvedProvider,
                                'filterModel' => $approvedSearch,
                                'summary' => false,
                                'columns' => [
                                    [
                                        'attribute' => 'nickname',
                                        'contentOptions' => ['style' => 'font-weight:900;'],
                                    ],
                                    [
                                        'attribute' => 'content',
                                        'format' => 'ntext',
                                        'contentOptions' => ['class' => 'wm-col-content'],
                                    ],
                                    [
                                        'label' => '目标',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'wm-col-target'],
                                        'value' => function ($model) use ($getTargetInfo) {
                                            $info = $getTargetInfo($model);
                                            return Html::a($info['label'], $info['link'], ['target' => '_blank']);
                                        },
                                    ],
                                    [
                                        'attribute' => 'created_at',
                                        'format' => 'datetime',
                                        'contentOptions' => ['class' => 'wm-time'],
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'contentOptions' => ['class' => 'wm-actions'],
                                        'template' => '{view} {revert}',
                                        'buttons' => [
                                            'view' => function ($url, $model) use ($getTargetInfo) {
                                                $info = $getTargetInfo($model);
                                                return Html::button('查看', [
                                                    'class' => 'btn btn-xs btn-soft-ghost js-view-message',
                                                    'data-nickname' => Html::encode($model->nickname),
                                                    'data-content' => Html::encode($model->content),
                                                    'data-target-type' => Html::encode($info['type']),
                                                    'data-target-name' => Html::encode($info['name']),
                                                    'data-target-info' => Html::encode(json_encode($info['basic_info'], JSON_UNESCAPED_UNICODE)),
                                                    'data-time' => Html::encode(Yii::$app->formatter->asDatetime($model->created_at)),
                                                ]);
                                            },
                                            'revert' => function ($url, $model) {
                                                return Html::a('撤销', ['revert', 'id' => $model->id], [
                                                    'data-method' => 'post',
                                                    'class' => 'btn btn-xs btn-soft-warning',
                                                ]);
                                            },
                                        ],
                                    ],
                                ],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Rejected -->
                <div role="tabpanel" class="tab-pane" id="tab-rejected">
                    <div class="wm-grid">
                        <?php if ($rejectedCount <= 0): ?>
                            <div class="wm-empty">暂无已拒绝留言</div>
                        <?php else: ?>
                            <?= GridView::widget([
                                'options' => ['class' => 'wm-grid-inner'],
                                'tableOptions' => ['class' => 'table table-hover'],
                                'dataProvider' => $rejectedProvider,
                                'filterModel' => $rejectedSearch,
                                'summary' => false,
                                'columns' => [
                                    [
                                        'attribute' => 'nickname',
                                        'contentOptions' => ['style' => 'font-weight:900;'],
                                    ],
                                    [
                                        'attribute' => 'content',
                                        'format' => 'ntext',
                                        'contentOptions' => ['class' => 'wm-col-content'],
                                    ],
                                    [
                                        'label' => '目标',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'wm-col-target'],
                                        'value' => function ($model) use ($getTargetInfo) {
                                            $info = $getTargetInfo($model);
                                            return Html::a($info['label'], $info['link'], ['target' => '_blank']);
                                        },
                                    ],
                                    [
                                        'attribute' => 'created_at',
                                        'format' => 'datetime',
                                        'contentOptions' => ['class' => 'wm-time'],
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'contentOptions' => ['class' => 'wm-actions'],
                                        'template' => '{view} {revert}',
                                        'buttons' => [
                                            'view' => function ($url, $model) use ($getTargetInfo) {
                                                $info = $getTargetInfo($model);
                                                return Html::button('查看', [
                                                    'class' => 'btn btn-xs btn-soft-ghost js-view-message',
                                                    'data-nickname' => Html::encode($model->nickname),
                                                    'data-content' => Html::encode($model->content),
                                                    'data-target-type' => Html::encode($info['type']),
                                                    'data-target-name' => Html::encode($info['name']),
                                                    'data-target-info' => Html::encode(json_encode($info['basic_info'], JSON_UNESCAPED_UNICODE)),
                                                    'data-time' => Html::encode(Yii::$app->formatter->asDatetime($model->created_at)),
                                                ]);
                                            },
                                            'revert' => function ($url, $model) {
                                                return Html::a('撤销', ['revert', 'id' => $model->id], [
                                                    'data-method' => 'post',
                                                    'class' => 'btn btn-xs btn-soft-warning',
                                                ]);
                                            },
                                        ],
                                    ],
                                ],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div><!-- tab-content -->
        </div><!-- wm-tabs -->
    </div><!-- wm-card -->

    <!-- Detail Modal -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">留言详情</h4>
                </div>

                <div class="modal-body">
                    <div class="wm-detail-grid">
                        <div class="wm-kv">
                            <div class="k">昵称</div>
                            <div class="v" id="md-nickname"></div>
                        </div>
                        <div class="wm-kv">
                            <div class="k">时间</div>
                            <div class="v" id="md-time"></div>
                        </div>
                        <div class="wm-kv">
                            <div class="k">关联类型</div>
                            <div class="v" id="md-target-type"></div>
                        </div>
                        <div class="wm-kv">
                            <div class="k">关联名称</div>
                            <div class="v" id="md-target-name"></div>
                        </div>
                    </div>

                    <div style="margin-top:10px; font-weight:900; color:#0f172a;">留言内容</div>
                    <div id="md-content" style="margin-top:8px;"></div>

                    <div style="margin-top:16px; font-weight:900; color:#0f172a;">关联信息</div>
                    <table class="table" id="md-target-info">
                        <!-- JS fill -->
                    </table>
                </div>


            </div>
        </div>
    </div>

</div><!-- wm-page -->

<?php
$js = <<<JS
$(document).on('click', '.js-view-message', function () {
    $('#md-nickname').text($(this).data('nickname') || '');
    $('#md-time').text($(this).data('time') || '');
    $('#md-content').text($(this).data('content') || '');
    $('#md-target-type').text($(this).data('target-type') || '');
    $('#md-target-name').text($(this).data('target-name') || '');

    var raw = $(this).data('target-info');
    var targetInfo = {};
    try { targetInfo = JSON.parse(raw); } catch (e) { targetInfo = {}; }

    var html = '';
    $.each(targetInfo || {}, function(key, value) {
        if (value && value !== '未知' && value !== '暂无简介') {
            var safeVal = $('<div/>').text(value).html();
            html += '<tr><th>' + key + '</th><td style="white-space: pre-wrap;">' + safeVal + '</td></tr>';
        }
    });
    if (!html) {
        html = '<tr><td colspan="2" class="text-muted" style="padding:14px;">未能获取到目标信息</td></tr>';
    }
    $('#md-target-info').html(html);

    $('#messageDetailModal').modal('show');
});
JS;

$this->registerJs($js);
?>
