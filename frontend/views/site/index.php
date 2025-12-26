<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '烽火记忆 · 抗战胜利80周年';
?>

<!-- 页面加载遮罩层 -->
<div id="intro-overlay" class="intro-overlay">
    <div class="intro-content">
        <h1 class="intro-title">纪念中国人民抗日战争胜利80周年</h1>
        <!-- <h2 class="intro-subtitle">暨世界反法西斯战争胜利80周年</h2> -->
        <p class="intro-subsubtitle">1945 — 2025</p>
    </div>
</div>

<div class="site-index">
    <div class="body-content" style="position: fixed; top: 50px; left: 0; right: 0; bottom: 0; display: flex; justify-content: center; align-items: center; overflow: hidden; padding: 0; margin: 0;">

        <div id="china-map-wrapper" style="width: 100%; height: 100%; max-width: 1200px; display: flex; justify-content: center; align-items: center;">
            
            <object id="china-map-object" type="image/svg+xml" data="<?= \yii\helpers\BaseUrl::base(true) . '/images/china-map.svg' ?>" style="width:100%; height:100%; display:block;">
                您的浏览器不支持 SVG，请升级浏览器。
            </object>
        </div>

        <!-- 右上角文案区域 -->
        <div class="homepage-text-area">
            <!-- 铭句 - 主视觉效果，偏左，大字 -->
            <div class="motto-text hero-text" id="motto-text">
                <div class="motto-line">以数据铭记</div>
                <div class="motto-line">以时间作证</div>
            </div>

            <!-- 分隔竖线 -->
            <div class="vertical-divider"></div>

            <!-- 图注 - 大标题，最右侧 -->
            <div class="map-title hero-text" id="map-title">
                <div class="map-title-label">【图注】</div>
                <div class="map-title-main">抗战历史地图</div>
                <div class="map-title-year">1931—1945</div>
            </div>
        </div>

        <!-- 动态时间标签 -->
        <div id="timeline-label" class="timeline-label">
            <div class="timeline-label-year"></div>
            <div class="timeline-label-date"></div>
            <div class="timeline-label-event"></div>
        </div>

        <!-- 动画控制按钮 -->
        <div id="animation-controls" class="animation-controls">
            <button id="btn-play-animation" class="anim-btn" title="播放历史动画">
                <i class="glyphicon glyphicon-play"></i>
                <span>回顾历史</span>
            </button>
            <button id="btn-skip-animation" class="anim-btn anim-btn-secondary" title="跳过动画" style="display: none;">
                <i class="glyphicon glyphicon-forward"></i>
                <span>跳过</span>
            </button>
        </div>
    </div>
</div>

<!-- 弹窗背景遮罩 -->
<div id="modal-backdrop" class="modal-backdrop-overlay" onclick="closeEventModal()"></div>

<!-- 事件详情弹窗 -->
<div id="event-detail-modal">
    <div class="modal-close" onclick="event.stopPropagation(); closeEventModal();" title="关闭">×</div>
    <div class="modal-content" onclick="navigateToEvent()">
        <!-- 左侧单图 -->
        <div class="modal-image-container">
            <img id="modal-image" src="" alt="事件图片">
            <div class="overlay">
                <h2 id="modal-image-title"></h2>
            </div>
        </div>

        <!-- 右侧信息 -->
        <div class="modal-info">
            <h1 class="event-title" id="modal-title"></h1>
            <div class="event-meta">
                <div class="event-meta-item">
                    <span class="tag">日期</span>
                    <span id="modal-date"></span>
                </div>
                <div class="event-meta-item">
                    <span class="tag">地区</span>
                    <span id="modal-location"></span>
                </div>
                <div class="event-meta-item">
                    <!-- <span class="tag">摘要</span> -->
                    <span id="modal-summary"></span>
                </div>
            </div>
        </div>
        <!-- 点击提示 -->
        <div class="modal-click-hint">
            <span class="hint-icon">→</span>
            <span class="hint-text">点击查看详情</span>
        </div>
    </div>
</div>

<?php
// 注册全局变量
$eventUrl = Url::to(['/event/index'], true);
$baseWebUrl = \yii\helpers\BaseUrl::base(true);

$this->registerJs("
    window._EVENT_INDEX_URL = " . json_encode($eventUrl) . ";
    window._BASE_WEB_URL = " . json_encode($baseWebUrl) . ";
    window._AUTO_START_ANIMATION = true;  // 标记自动启动动画
", \yii\web\View::POS_HEAD);

// 注册地图 JS
$this->registerJsFile('@web/js/china-map.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// 注册关闭弹窗函数
$this->registerJs("
function closeEventModal() {
    var modal = document.getElementById('event-detail-modal');
    var backdrop = document.getElementById('modal-backdrop');
    modal.classList.remove('show');
    if (backdrop) backdrop.classList.remove('show');
}

// 导航到事件详情页
function navigateToEvent() {
    var modal = document.getElementById('event-detail-modal');
    var eventId = modal.getAttribute('data-event-id');
    if (!eventId) return;
    
    var url = window._EVENT_INDEX_URL || '/event/index';
    if (url.indexOf('event%2Findex') > -1) {
        url = url.replace('event%2Findex', 'timeline%2Fview') + '&id=' + eventId;
    } else if (url.indexOf('event/index') > -1) {
        url = url.replace('event/index', 'timeline/view');
        url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + eventId;
    } else {
        url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + eventId;
    }
    window.open(url, '_blank');
}

// ESC 键关闭弹窗
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEventModal();
    }
});
", \yii\web\View::POS_END);
?>
