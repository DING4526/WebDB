<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '烽火记忆 · 抗战胜利80周年';
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">烽火记忆 · 抗战胜利80周年</h1>
        <p class="lead">以时间作证，以数据铭记 —— 1931–1945 </p>
    </div>

    <div class="body-content">
        <!-- 地图容器 -->
        <div id="china-map-wrapper" style="max-width:1000px; margin:0 auto; border: 1px solid #eee; padding: 10px;">
            <!-- 使用 object 标签加载 SVG -->
            <object id="china-map-object" type="image/svg+xml" data="<?= \yii\helpers\BaseUrl::base(true) . '/images/china-map.svg' ?>" style="width:100%; height:900px; display:block;">
                您的浏览器不支持 SVG，请升级浏览器。
            </object>
        </div>
    </div>
</div>

<?php
// 注册全局变量
$eventUrl = Url::to(['/event/index'], true);
$this->registerJs("window._EVENT_INDEX_URL = " . json_encode($eventUrl) . " ;", \yii\web\View::POS_HEAD);

// 注册地图 JS (只保留这一个)
$this->registerJsFile('@web/js/china-map.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
