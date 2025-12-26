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
    /* 导航栏样式优化 */
    .navbar-inverse {
      background: rgba(20, 15, 10, 0.95) !important;
      border-bottom: 1px solid rgba(201, 162, 39, 0.2) !important;
    }
    .navbar-inverse .navbar-brand {
      color: #C9A227 !important;
    }
    .navbar-inverse .navbar-brand b {
      color: #F5E6C8 !important;
    }
    .navbar-inverse .navbar-nav > li > a {
      color: #F5E6C8 !important;
      transition: color 0.2s ease;
    }
    .navbar-inverse .navbar-nav > li > a:hover,
    .navbar-inverse .navbar-nav > li > a:focus {
      color: #C9A227 !important;
      background: transparent !important;
    }
    .navbar-inverse .navbar-nav > .active > a,
    .navbar-inverse .navbar-nav > .active > a:hover,
    .navbar-inverse .navbar-nav > .active > a:focus {
      color: #C9A227 !important;
      background: rgba(201, 162, 39, 0.15) !important;
    }
    
    .navbar-brand b { letter-spacing: .5px; }
    .content-wrap { 
      margin-top: 120px; 
      position: relative;
      z-index: 2;
    }
    .footer { 
      background: rgba(20, 15, 10, 0.95) !important; 
      border-top: 1px solid rgba(201, 162, 39, 0.2) !important;
      color: #F5E6C8 !important;
    }
    
    /* 面包屑导航样式 */
    .breadcrumb {
      background: rgba(30, 25, 20, 0.9) !important;
      border: 1px solid rgba(201, 162, 39, 0.2) !important;
      border-radius: 10px;
    }
    .breadcrumb > li + li:before {
      color: #A88B2A;
    }
    .breadcrumb > li > a {
      color: #C9A227;
    }
    .breadcrumb > .active {
      color: #F5E6C8;
    }
  </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- 全局背景遮罩层 -->
<div class="background-overlay"></div>
<div class="vignette-overlay"></div>

<?php
NavBar::begin([
  'brandLabel' => '<b>烽火记忆</b> · 抗战胜利80周年',
  'brandUrl' => Url::to(['/site/index']),
  'options' => ['class' => 'navbar-inverse navbar-fixed-top', 'style' => 'background: rgba(20, 15, 10, 0.95); border-bottom: 1px solid rgba(201, 162, 39, 0.2);'],
]);

$menuItemsLeft = [
  ['label' => '时间轴', 'url' => ['/timeline/index'], 'active' => $activeCtl('timeline')],
  ['label' => '人物库', 'url' => ['/person/index'], 'active' => $activeCtl('person')],
  // ['label' => '纪念留言', 'url' => ['/message/index'], 'active' => $activeCtl('message')],
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
        'style' => 'color:#C9A227;text-decoration:none;',
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

<div class="container" style="position: relative; z-index: 2;">
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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
