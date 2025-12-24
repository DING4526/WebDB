<?php

/**
 * Ding 2310724
 * 前台抗战专题主布局文件
 */


/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);

// 当前路由用于高亮
$cur = Yii::$app->controller->id;
$activeCtl = function($id) use ($cur) { return $cur === $id; };
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $this->registerCsrfMetaTags() ?>
  <title><?= Html::encode($this->title ?: Yii::$app->name) ?></title>
  <?php $this->head() ?>
  <style>
    body { background:#f7f9fb; }
    .navbar-brand b { letter-spacing: .5px; }
    .hero {
      margin-top: 70px;
      background: #ffffff;
      border: 1px solid #eef1f5;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,.04);
      padding: 18px 18px;
    }
    .hero h3 { margin-top:0; font-weight:800; }
    .hero .btn { margin-right:8px; }
    .content-wrap { margin-top: 120px; }
    .footer { background:#fff; border-top:1px solid #eef1f5; }
  </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- 开场动画遮罩层 -->
<section id="chip-section">
  <h1 id="intro-title">
    以时间作证<br>
    以数据铭记
  </h1>
  <div id="the-chip">
    <svg viewBox="0 0 1000 400" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" id="intro-svg">
      <defs>
        <mask id="text-mask">
          <rect width="100%" height="100%" fill="white" />
          <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-size="160" font-weight="800" font-family="Arial Black, Impact, sans-serif" fill="black" letter-spacing="6">
            1931–1945
          </text>
        </mask>
      </defs>
      <!-- 新增：白色背景层，用于初始显示白色文字，随后淡出显示地图 -->
      <rect id="white-bg" width="100%" height="100%" fill="white" />
      <!-- 红色背景，文字部分透明 -->
      <rect width="100%" height="100%" fill="#e10600" mask="url(#text-mask)" />
    </svg>
  </div>
</section>

<?php
NavBar::begin([
  'brandLabel' => '<b>烽火记忆</b> · 抗战胜利80周年',
  'brandUrl' => Url::to(['/site/index']),
  'options' => ['class' => 'navbar-inverse navbar-fixed-top'],
]);

$menuItemsLeft = [
  ['label' => '时间轴', 'url' => ['/timeline/index'], 'active' => $activeCtl('timeline')],
  ['label' => '人物库', 'url' => ['/person/index'], 'active' => $activeCtl('person')],
  ['label' => '纪念留言', 'url' => ['/message/index'], 'active' => $activeCtl('message')],
];

$menuItemsRight = [];
if (Yii::$app->user->isGuest) {
  $menuItemsRight[] = ['label' => '登录', 'url' => ['/site/login']];
  $menuItemsRight[] = ['label' => '注册', 'url' => ['/site/signup']];
} else {
  $menuItemsRight[] = '<li>'
    . Html::beginForm(['/site/logout'], 'post', ['class' => 'navbar-form'])
    . Html::submitButton('退出 (' . Html::encode(Yii::$app->user->identity->username) . ')', [
        'class' => 'btn btn-link',
        'style' => 'color:#ddd;text-decoration:none;',
    ])
    . Html::endForm()
    . '</li>';
}

echo Nav::widget([
  'options' => ['class' => 'navbar-nav navbar-left'],
  'items' => $menuItemsLeft,
]);
echo Nav::widget([
  'options' => ['class' => 'navbar-nav navbar-right'],
  'items' => $menuItemsRight,
]);

NavBar::end();
?>

<div class="container">
         <!-- 顶部专题横幅（可留可删）  -->
  <!-- <div class="hero">
    <h3>抗战史实时间轴 · 人物群像数据库</h3>
    <div class="text-muted" style="margin-bottom:10px;">
      以时间作证，以数据铭记 —— 1931–1945 史实与人物关联展示
    </div>
    <a class="btn btn-primary btn-sm" href="<?= Url::to(['/timeline/index']) ?>">
      <span class="glyphicon glyphicon-time"></span> 进入时间轴
    </a>
    <a class="btn btn-default btn-sm" href="<?= Url::to(['/person/index']) ?>">
      <span class="glyphicon glyphicon-user"></span> 浏览人物库
    </a>
  </div>  -->

  <div class="content-wrap">
    <?= Breadcrumbs::widget([
      'links' => $this->params['breadcrumbs'] ?? [],
      'options' => ['class' => 'breadcrumb', 'style' => 'background:#fff;border:1px solid #eef1f5;border-radius:10px;'],
    ]) ?>
    <?= Alert::widget() ?>
    <?= $content ?>
  </div>
</div>

<footer class="footer">
  <div class="container" style="padding:16px 0;">
    <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
    <p class="pull-right"><?= Yii::powered() ?></p>
  </div>
</footer>

<?php
// 注册开场动画 JS
$this->registerJs("
    (function() {
        var section = document.getElementById('chip-section');
        var chip = document.getElementById('the-chip');
        var title = document.getElementById('intro-title');
        var svg = document.getElementById('intro-svg');
        var whiteBg = document.getElementById('white-bg');
        
        // 动画状态
        var progress = 0;
        var maxProgress = 1500; // 增加总行程，让动画更从容
        var isActive = true;
        
        // 禁用页面滚动
        document.body.style.overflow = 'hidden';
        
        function handleScroll(e) {
            if (!isActive) return;
            
            // 阻止默认滚动
            e.preventDefault();
            
            // 计算进度
            var delta = e.deltaY;
            if (delta > 0) delta = Math.min(delta, 60);
            if (delta < 0) delta = Math.max(delta, -60);
            
            progress += delta * 2;
            
            // 限制范围
            if (progress < 0) progress = 0;
            
            // 动画逻辑
            requestAnimationFrame(updateAnimation);
            
            // 结束动画
            if (progress > maxProgress) {
                finishAnimation();
            }
        }
        
        function updateAnimation() {
            var ratio = progress / maxProgress; // 0 到 1
            
            // 1. 标题淡出 & 上移 (0.0 - 0.2)
            if (ratio < 0.2) {
                var titleOpacity = 1 - (ratio / 0.2);
                title.style.opacity = titleOpacity;
                // 向上移动
                title.style.marginTop = '-' + (ratio * 200) + 'px';
                
                // 此时 SVG 隐藏
                svg.style.opacity = 0;
                chip.style.backgroundColor = '#e10600'; // 填补空洞
            } else {
                title.style.opacity = 0;
            }
            
            // 2. SVG 淡入 (0.2 - 0.35)
            if (ratio >= 0.2 && ratio < 0.35) {
                var svgOpacity = (ratio - 0.2) / 0.15;
                svg.style.opacity = svgOpacity;
                chip.style.backgroundColor = '#e10600';
            } else if (ratio >= 0.35) {
                svg.style.opacity = 1;
            }
            
            // 3. 缩放 (0.2 - 1.0)
            if (ratio >= 0.2) {
                var zoomRatio = (ratio - 0.2) / 0.8;
                // 使用三次方缓动让缩放更有冲击力
                var scale = 1 + (zoomRatio * zoomRatio * zoomRatio * 300); 
                chip.style.width = (100 * scale) + 'px';
                chip.style.height = (40 * scale) + 'px';
            } else {
                chip.style.width = '100px';
                chip.style.height = '40px';
            }
            
            // 4. 文字透视 (0.5 - 0.8)
            if (ratio >= 0.5) {
                // 此时将 chip 背景设为透明，依靠 SVG 的白色背景层遮挡
                chip.style.backgroundColor = 'transparent';
                
                var transRatio = (ratio - 0.5) / 0.3;
                if (transRatio > 1) transRatio = 1;
                
                // 白色背景层淡出 -> 露出地图
                whiteBg.style.opacity = 1 - transRatio;
            } else {
                whiteBg.style.opacity = 1;
            }
            
            // 5. 整体淡出 (0.9 - 1.0)
            if (ratio > 0.9) {
                section.style.opacity = 1 - ((ratio - 0.9) * 10);
            } else {
                section.style.opacity = 1;
            }
        }
        
        function finishAnimation() {
            isActive = false;
            section.style.display = 'none';
            document.body.style.overflow = ''; // 恢复滚动
            window.removeEventListener('wheel', handleScroll);
        }
        
        // 监听滚轮
        window.addEventListener('wheel', handleScroll, { passive: false });
        
        // 初始状态
        updateAnimation();
    })();
", \yii\web\View::POS_END);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
