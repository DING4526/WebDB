<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '烽火记忆 · 抗战胜利80周年';
?>
<div class="site-index">
    <!-- 标题区域 -->
    <!-- <div class="jumbotron text-center" style="background:transparent; border:none;">
        <h1 class="display-4" style="color:#fff; text-shadow: 2px 2px 8px rgba(0,0,0,0.8);">烽火记忆 · 抗战胜利80周年</h1>
        <p class="lead" style="color:#fff; text-shadow: 1px 1px 4px rgba(0,0,0,0.8);">以时间作证，以数据铭记 —— 1931–1945 </p>
    </div> -->

<div class="site-index">
    <div class="body-content" style="display:flex; justify-content:center; align-items:center; height: 90vh; width: 100%; overflow:hidden;">
        
        <div id="china-map-wrapper" style="width: 100%; height: 100%; max-width: 1200px; display: flex; justify-content: center; align-items: center;">
            
            <object id="china-map-object" type="image/svg+xml" data="<?= \yii\helpers\BaseUrl::base(true) . '/images/china-map.svg' ?>" style="width:100%; height:100%; display:block;">
                您的浏览器不支持 SVG，请升级浏览器。
            </object>
        </div>
    </div>
</div>

<!-- 事件详情弹窗 (单事件版) -->
<div id="event-detail-modal">
    <div class="modal-close" onclick="closeEventModal()">×</div>
    <div class="modal-content">
        <!-- 左侧单图 -->
        <div class="modal-image-container">
            <img id="modal-image" src="" alt="Event Image">
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
    modal.classList.remove('show');
}
", \yii\web\View::POS_END);
?>
