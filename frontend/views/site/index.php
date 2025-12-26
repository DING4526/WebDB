<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '烽火记忆 · 抗战胜利80周年';
?>

<!-- 背景遮罩层 - 压低背景对比度 -->
<div class="background-overlay"></div>
<div class="vignette-overlay"></div>

<div class="site-index">
    <div class="body-content" style="position: fixed; top: 50px; left: 0; right: 0; bottom: 0; display: flex; justify-content: center; align-items: center; overflow: hidden; padding: 0; margin: 0;">
        
        <div id="china-map-wrapper" style="width: 100%; height: 100%; max-width: 1200px; display: flex; justify-content: center; align-items: center;">
            
            <object id="china-map-object" type="image/svg+xml" data="<?= \yii\helpers\BaseUrl::base(true) . '/images/china-map.svg' ?>" style="width:100%; height:100%; display:block;">
                您的浏览器不支持 SVG，请升级浏览器。
            </object>
        </div>

        <!-- 右侧竖排艺术字 -->
        <div class="vertical-slogan">
            <div class="slogan-line">以时间作证，</div>
            <div class="slogan-line">以数据铭记。</div>
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
                    <span class="tag">摘要</span>
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
