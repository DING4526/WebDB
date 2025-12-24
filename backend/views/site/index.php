<?php

/**
 * Ding 2310724
 * 后台主页面 - 仪表板
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\TeamMember;

/* @var $this yii\web\View */

$this->title = '团队后台主页';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
$this->registerCssFile('@web/css/dashboard.css');

$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
$isMember = $currentUser && $currentUser->isMember();
$memberRecord = null;
if ($currentUser && Yii::$app->teamProvider) {
    $teamId = Yii::$app->teamProvider->getId();
    if ($teamId) {
        $memberRecord = TeamMember::find()
            ->andWhere(['team_id' => $teamId, 'user_id' => $currentUser->id])
            ->one();
    }
}

$teamInfo = Yii::$app->teamProvider->getTeam();
?>

<div class="site-index dashboard-page">
    <!-- 页面头部：团队名 + 主题 + 刷新按钮 -->
    <div class="adm-hero">
        <div class="adm-hero-inner">
            <div>
                <h2><?= Html::encode($teamInfo->name ?? '团队后台') ?></h2>
                <div class="desc"><?= Html::encode($teamInfo->topic ?? '管理团队信息、成员权限、作业文件与项目数据') ?></div>
            </div>
            <div class="adm-actions">
                <button type="button" class="btn btn-default" id="refreshDashboard">
                    <span class="glyphicon glyphicon-refresh"></span> 刷新
                </button>
            </div>
        </div>
    </div>

    <!-- KPI 卡片区（1行6张）-->
    <div class="kpi-cards-row">
        <div class="kpi-card">
            <div class="kpi-icon"><span class="glyphicon glyphicon-user"></span></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-members">-</div>
                <div class="kpi-label">团队成员</div>
            </div>
            <?php if ($isRoot): ?>
                <a href="<?= Url::to(['team-member-apply/index']) ?>" class="kpi-link">管理</a>
            <?php endif; ?>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon-warning"><span class="glyphicon glyphicon-check"></span></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-pending-apply">-</div>
                <div class="kpi-label">待审批申请</div>
            </div>
            <?php if ($isRoot): ?>
                <a href="<?= Url::to(['team-member-apply/index']) ?>" class="kpi-link">去审批</a>
            <?php endif; ?>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon-info"><span class="glyphicon glyphicon-comment"></span></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-pending-message">-</div>
                <div class="kpi-label">待审留言</div>
            </div>
            <?php if ($isRoot || $isMember): ?>
                <a href="<?= Url::to(['war-message/index']) ?>" class="kpi-link">去审核</a>
            <?php endif; ?>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon-success"><span class="glyphicon glyphicon-list-alt"></span></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-content-total">-</div>
                <div class="kpi-label">内容总量</div>
                <div class="kpi-sub" id="kpi-content-detail">事/人/媒</div>
            </div>
            <a href="<?= Url::to(['project-show/index']) ?>" class="kpi-link">查看</a>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon-primary"><span class="glyphicon glyphicon-stats"></span></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-visits-7d">-</div>
                <div class="kpi-label">近7天访问</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon-new"><span class="glyphicon glyphicon-plus"></span></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-new-7d">-</div>
                <div class="kpi-label">近7天新增</div>
            </div>
        </div>
    </div>

    <!-- 主工作区（两列：左8 / 右4）-->
    <div class="row main-workspace">
        <div class="col-md-8">
            <!-- 访问趋势（折线图）-->
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">访问趋势</h3>
                    <div class="chart-toggle">
                        <button type="button" class="btn btn-xs btn-soft-ghost active" data-mode="day">按天</button>
                    </div>
                </div>
                <div class="adm-card-body">
                    <div class="chart-container">
                        <canvas id="visitTrendChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- 留言趋势（堆叠柱状图）-->
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">留言趋势</h3>
                    <?php if ($isRoot || $isMember): ?>
                        <a href="<?= Url::to(['war-message/index']) ?>" class="btn btn-xs btn-soft-primary">查看详情</a>
                    <?php endif; ?>
                </div>
                <div class="adm-card-body">
                    <div class="chart-container">
                        <canvas id="messageTrendChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- 待处理（To-do）-->
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">待处理（To-do）</h3>
                </div>
                <div class="adm-card-body">
                    <ul class="todo-list">
                        <?php if ($isRoot): ?>
                            <li class="todo-item">
                                <span class="todo-label">成员申请待审批</span>
                                <span class="todo-count" id="todo-apply">-</span>
                                <a href="<?= Url::to(['team-member-apply/index']) ?>" class="todo-link">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($isRoot || $isMember): ?>
                            <li class="todo-item">
                                <span class="todo-label">留言待审核</span>
                                <span class="todo-count" id="todo-message">-</span>
                                <a href="<?= Url::to(['war-message/index']) ?>" class="todo-link">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </a>
                            </li>
                            <li class="todo-item">
                                <span class="todo-label">内容待完善</span>
                                <span class="todo-count" id="todo-quality">-</span>
                                <a href="#quality-section" class="todo-link scroll-to">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- 快捷入口（Actions）-->
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">快捷入口（Actions）</h3>
                </div>
                <div class="adm-card-body">
                    <div class="quick-actions">
                        <?php if ($isRoot || $isMember): ?>
                            <a href="<?= Url::to(['war-event/create']) ?>" class="quick-action-btn">
                                <span class="glyphicon glyphicon-time"></span>
                                <span>新增事件</span>
                            </a>
                            <a href="<?= Url::to(['war-person/create']) ?>" class="quick-action-btn">
                                <span class="glyphicon glyphicon-education"></span>
                                <span>新增人物</span>
                            </a>
                            <a href="<?= Url::to(['war-message/index']) ?>" class="quick-action-btn">
                                <span class="glyphicon glyphicon-comment"></span>
                                <span>审核中心</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($isRoot): ?>
                            <a href="<?= Url::to(['team-member-apply/index']) ?>" class="quick-action-btn">
                                <span class="glyphicon glyphicon-user"></span>
                                <span>成员管理</span>
                            </a>
                            <a href="<?= Url::to(['team/index']) ?>" class="quick-action-btn">
                                <span class="glyphicon glyphicon-cog"></span>
                                <span>团队设置</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?= Url::to(['project-show/index']) ?>" class="quick-action-btn">
                            <span class="glyphicon glyphicon-stats"></span>
                            <span>数据展示</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 第二屏：内容质量 + 热榜（两列：左6 / 右6）-->
    <div class="row second-screen" id="quality-section">
        <div class="col-md-6">
            <!-- 内容质量概览（覆盖率）-->
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">内容质量概览</h3>
                </div>
                <div class="adm-card-body">
                    <div class="quality-list">
                        <div class="quality-item">
                            <span class="quality-label">事件有封面</span>
                            <div class="quality-bar-wrap">
                                <div class="quality-bar" id="quality-event-cover" style="width: 0%"></div>
                            </div>
                            <span class="quality-percent" id="quality-event-cover-pct">--%</span>
                            <?php if ($isRoot || $isMember): ?>
                                <a href="<?= Url::to(['war-event/index']) ?>" class="quality-link">去补齐</a>
                            <?php endif; ?>
                        </div>
                        <div class="quality-item">
                            <span class="quality-label">事件有摘要</span>
                            <div class="quality-bar-wrap">
                                <div class="quality-bar" id="quality-event-summary" style="width: 0%"></div>
                            </div>
                            <span class="quality-percent" id="quality-event-summary-pct">--%</span>
                            <?php if ($isRoot || $isMember): ?>
                                <a href="<?= Url::to(['war-event/index']) ?>" class="quality-link">去补齐</a>
                            <?php endif; ?>
                        </div>
                        <div class="quality-item">
                            <span class="quality-label">事件有关联人物</span>
                            <div class="quality-bar-wrap">
                                <div class="quality-bar" id="quality-event-person" style="width: 0%"></div>
                            </div>
                            <span class="quality-percent" id="quality-event-person-pct">--%</span>
                            <?php if ($isRoot || $isMember): ?>
                                <a href="<?= Url::to(['war-event/index']) ?>" class="quality-link">去补齐</a>
                            <?php endif; ?>
                        </div>
                        <div class="quality-item">
                            <span class="quality-label">人物有简介</span>
                            <div class="quality-bar-wrap">
                                <div class="quality-bar" id="quality-person-intro" style="width: 0%"></div>
                            </div>
                            <span class="quality-percent" id="quality-person-intro-pct">--%</span>
                            <?php if ($isRoot || $isMember): ?>
                                <a href="<?= Url::to(['war-person/index']) ?>" class="quality-link">去补齐</a>
                            <?php endif; ?>
                        </div>
                        <div class="quality-item">
                            <span class="quality-label">人物有封面</span>
                            <div class="quality-bar-wrap">
                                <div class="quality-bar" id="quality-person-cover" style="width: 0%"></div>
                            </div>
                            <span class="quality-percent" id="quality-person-cover-pct">--%</span>
                            <?php if ($isRoot || $isMember): ?>
                                <a href="<?= Url::to(['war-person/index']) ?>" class="quality-link">去补齐</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- 热榜 TOP5（Tab）-->
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3 class="adm-card-title">热榜 TOP5</h3>
                    <div class="top-tabs">
                        <button type="button" class="btn btn-xs btn-soft-primary active" data-tab="events">事件</button>
                        <button type="button" class="btn btn-xs btn-soft-ghost" data-tab="persons">人物</button>
                    </div>
                </div>
                <div class="adm-card-body">
                    <div class="top-list" id="top-events-list">
                        <div class="top-empty">暂无数据</div>
                    </div>
                    <div class="top-list" id="top-persons-list" style="display: none;">
                        <div class="top-empty">暂无数据</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
(function() {
    var visitChart = null;
    var messageChart = null;

    function loadDashboardData() {
        $.ajax({
            url: '<?= Url::to(['site/dashboard-data']) ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // KPI卡片
                $('#kpi-members').text(data.memberCount);
                $('#kpi-pending-apply').text(data.pendingApplyCount);
                $('#kpi-pending-message').text(data.pendingMessageCount);
                
                var total = data.eventCount + data.personCount + data.mediaCount;
                $('#kpi-content-total').text(total);
                $('#kpi-content-detail').text(data.eventCount + '事/' + data.personCount + '人/' + data.mediaCount + '媒');
                
                $('#kpi-visits-7d').text(data.visits7Days.toLocaleString());
                $('#kpi-new-7d').text(data.newContent7Days);

                // To-do
                $('#todo-apply').text(data.pendingApplyCount);
                $('#todo-message').text(data.pendingMessageCount);
                
                var qualityIssues = 0;
                if (data.quality.eventCover < 100) qualityIssues++;
                if (data.quality.eventSummary < 100) qualityIssues++;
                if (data.quality.eventPerson < 100) qualityIssues++;
                if (data.quality.personIntro < 100) qualityIssues++;
                if (data.quality.personCover < 100) qualityIssues++;
                $('#todo-quality').text(qualityIssues > 0 ? qualityIssues + '项' : '✓');

                // 内容质量
                updateQualityBar('event-cover', data.quality.eventCover);
                updateQualityBar('event-summary', data.quality.eventSummary);
                updateQualityBar('event-person', data.quality.eventPerson);
                updateQualityBar('person-intro', data.quality.personIntro);
                updateQualityBar('person-cover', data.quality.personCover);

                // 访问趋势图
                renderVisitChart(data.visitTrend);

                // 留言趋势图
                renderMessageChart(data.messageTrend);

                // 热榜
                renderTopList('events', data.topEvents);
                renderTopList('persons', data.topPersons);
            },
            error: function() {
                console.error('加载仪表板数据失败');
            }
        });
    }

    function updateQualityBar(key, percent) {
        $('#quality-' + key).css('width', percent + '%');
        $('#quality-' + key + '-pct').text(percent + '%');
        
        var bar = $('#quality-' + key);
        if (percent >= 80) {
            bar.css('background', '#22c55e');
        } else if (percent >= 50) {
            bar.css('background', '#f59e0b');
        } else {
            bar.css('background', '#ef4444');
        }
    }

    function renderVisitChart(trend) {
        var ctx = document.getElementById('visitTrendChart').getContext('2d');
        var labels = trend.map(function(item) { return item.date; });
        var values = trend.map(function(item) { return item.count; });

        if (visitChart) {
            visitChart.destroy();
        }

        visitChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '访问量',
                    data: values,
                    borderColor: '#8B2500',
                    backgroundColor: 'rgba(139, 37, 0, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#8B2500'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    function renderMessageChart(trend) {
        var ctx = document.getElementById('messageTrendChart').getContext('2d');
        var labels = trend.map(function(item) { return item.date; });
        var pending = trend.map(function(item) { return item.pending; });
        var approved = trend.map(function(item) { return item.approved; });
        var rejected = trend.map(function(item) { return item.rejected; });

        if (messageChart) {
            messageChart.destroy();
        }

        messageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '待审核',
                        data: pending,
                        backgroundColor: '#f59e0b'
                    },
                    {
                        label: '通过',
                        data: approved,
                        backgroundColor: '#22c55e'
                    },
                    {
                        label: '拒绝',
                        data: rejected,
                        backgroundColor: '#ef4444'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    function renderTopList(type, items) {
        var container = $('#top-' + type + '-list');
        if (!items || items.length === 0) {
            container.html('<div class="top-empty">暂无数据</div>');
            return;
        }

        var html = '';
        items.forEach(function(item, index) {
            var title = type === 'events' ? item.title : item.name;
            var url = type === 'events' 
                ? '<?= Url::to(['war-event/view']) ?>?id=' + item.id
                : '<?= Url::to(['war-person/view']) ?>?id=' + item.id;
            html += '<div class="top-item">' +
                '<span class="top-rank">' + (index + 1) + '</span>' +
                '<a href="' + url + '" class="top-title">' + escapeHtml(title) + '</a>' +
                '<span class="top-visits">' + item.visits + '访问</span>' +
                '</div>';
        });
        container.html(html);
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Tab切换
    $(document).on('click', '.top-tabs button', function() {
        var tab = $(this).data('tab');
        $('.top-tabs button').removeClass('active').addClass('btn-soft-ghost');
        $(this).addClass('active').removeClass('btn-soft-ghost').addClass('btn-soft-primary');
        $('.top-list').hide();
        $('#top-' + tab + '-list').show();
    });

    // 刷新按钮
    $('#refreshDashboard').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true);
        btn.find('.glyphicon').addClass('spin');
        loadDashboardData();
        setTimeout(function() {
            btn.prop('disabled', false);
            btn.find('.glyphicon').removeClass('spin');
        }, 1000);
    });

    // 滚动链接
    $(document).on('click', '.scroll-to', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 100
        }, 500);
    });

    // 初始加载
    $(document).ready(function() {
        loadDashboardData();
    });
})();
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.spin {
    animation: spin 1s linear infinite;
}
</style>
