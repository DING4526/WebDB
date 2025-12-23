<?php

/**
 * Ding 2310724
 * 后台主布局文件
 */

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use common\models\User;

AppAsset::register($this);

$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
$isMember = $currentUser && $currentUser->isMember();
?>

<?php
// 只要同一个 controller 就高亮（更符合侧边栏习惯）
$cur = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
$activeCtl = function($controllerId) {
  return Yii::$app->controller->id === $controllerId ? 'active' : '';
};
$this->registerCssFile('@web/css/admin-common.css');
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
</head>
<body>
<?php $this->beginBody() ?>

  <!-- Start: Main -->
  <div id="main">

    <!-- Start: Header -->
    <header class="navbar navbar-fixed-top navbar-shadow">
      <div class="navbar-branding">
        <a class="navbar-brand" href="<?= Url::to(['site/index']) ?>">
          <b>管理</b>后台
        </a>
        <span id="toggle_sidemenu_l" class="ad ad-lines"></span>
      </div>
      <ul class="nav navbar-nav navbar-left">

        <li class="hidden-xs">
          <a class="request-fullscreen toggle-active" href="#">
            <span class="ad ad-screen-full fs18"></span>
          </a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">

        <li class="hidden-xs">
                     <a href="<?php echo Url::to(['site/logout']) ?>" data-method="post">
              <span class="fa fa-power-off pr5"></span> 登出 </a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="menu-divider hidden-xs">
          <i class="fa fa-circle"></i>
        </li>
        <li class="dropdown menu-merge">
          <a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown">
          	<img src="../statics/assets/img/avatars/1.jpg" alt="avatar" class="mw30 br64">
          	<span class="hidden-xs pl15"> 
                <?= Yii::$app->user->isGuest ? '游客' : (Yii::$app->user->getUser()->username ?? '') ?>
            </span>
            <span class="caret caret-tp hidden-xs"></span>
          </a>
          
          <ul class="dropdown-menu list-group dropdown-persist w250" role="menu">

            <li class="dropdown-header clearfix">
              <div class="pull-left ml10">
                <div class="text-muted">当前身份</div>
                <div><strong>
                  <?php if (Yii::$app->user->isGuest): ?>
                    游客 (guest)
                  <?php else: ?>
                    <?= Html::encode(Yii::$app->user->getUser()->role ?? 'member') ?>
                  <?php endif; ?>
                </strong></div>
              </div>

              <!-- <div class="pull-right mr10 text-right">
                <div class="text-muted">可访问范围</div>
                <div class="label label-primary">作业/任务</div>
              </div> -->
            </li>
   
          </ul>
        </li>
      
      </ul>
      
      
    </header>
    <!-- End: Header -->

    <!-- Start: Sidebar -->
    <aside id="sidebar_left" class="nano nano-light affix">

      <!-- Start: Sidebar Left Content -->
      <div class="sidebar-left-content nano-content">

        <!-- Start: Sidebar Header -->
        <header class="sidebar-header">

          <!-- Sidebar Widget - Author -->
          <div class="sidebar-widget author-widget">
            <div class="media">
              <a class="media-left" href="#">
                <img src="../statics/assets/img/avatars/3.jpg" class="img-responsive">
              </a>
              <div class="media-body">
                <div class="media-links">
                   <a href="#" class="sidebar-menu-toggle">用户菜单-</a> <a href="<?php echo Url::to(['site/logout']) ?>" data-method="post">登出 </a>
                </div>
                <div class="media-author">
                    <?= Yii::$app->user->isGuest ? '游客' : (Yii::$app->user->getUser()->username ?? '') ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar Widget - Menu (slidedown) -->
          <div class="sidebar-widget menu-widget">
            <div class="row text-center mbn">
              
              <div class="col-xs-4">
                <a href="pages_profile.html" class="text-danger" data-toggle="tooltip" data-placement="top" title="Settings">
                  <span class="fa fa-gears"></span>
                </a>
              </div>
             
            </div>
          </div>

          <!-- Sidebar Widget - Search (hidden) -->
          <div class="sidebar-widget search-widget hidden">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-search"></i>
              </span>
              <input type="text" id="sidebar-search" class="form-control" placeholder="Search...">
            </div>
          </div>

        </header>
        <!-- End: Sidebar Header -->

        <!-- Start: Sidebar Menu -->
        <ul class="nav sidebar-menu">

          <!-- 首页与权限 -->
          <li class="sidebar-label pt15">主页与权限</li>
          <li class="<?= $activeCtl('site') ?>">
            <a href="<?= Url::to(['site/index']) ?>">
              <span class="glyphicon glyphicon-home"></span>
              <span class="sidebar-title">后台主页概览</span>
            </a>
          </li>
          <li class="<?= $activeCtl('team') ?>">
            <a href="<?= Url::to(['team/index']) ?>">
              <span class="glyphicon glyphicon-king"></span>
                <?php if ($isRoot): ?>
              <span class="sidebar-title">团队管理</span>
              <?php else: ?>
                <span class="sidebar-title">团队信息</span>
              <?php endif; ?>
            </a>
          </li>

          <!-- 作业展示 -->
          <li class="sidebar-label pt20">作业与内容</li>
          <li class="<?= $activeCtl('teamwork') ?>">
            <a href="<?= Url::to(['teamwork/index']) ?>">
              <span class="glyphicon glyphicon-folder-open"></span>
              <span class="sidebar-title">团队作业</span>
            </a>
          </li>
          <li class="<?= $activeCtl('personalwork') ?>">
            <a href="<?= Url::to(['personalwork/index']) ?>">
              <span class="glyphicon glyphicon-user"></span>
              <span class="sidebar-title">个人作业</span>
            </a>
          </li>

          <!-- 任务分工 -->
          <li class="sidebar-label pt20">任务与协作</li>
          <?php if ($isRoot || $isMember): ?>
            <li class="<?= $activeCtl('taskboard') ?>">
              <a href="<?= Url::to(['taskboard/index']) ?>">
                <span class="glyphicon glyphicon-check"></span>
                <span class="sidebar-title">任务分工板</span>
              </a>
            </li>
          <?php endif; ?>
          
          <?php if (!$isRoot && !$isMember): ?>
          <li class="<?= $activeCtl('team-member-apply') ?>">
            <a href="<?= Url::to(['team-member-apply/create']) ?>">
              <span class="glyphicon glyphicon-send"></span>
              <span class="sidebar-title">申请成为成员</span>
                <span class="label label-sm label-primary ml10">USER</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if ($isRoot): ?>
            <li class="<?= $activeCtl('team-member-apply') ?>">
              <a href="<?= Url::to(['team-member-apply/index']) ?>">
                <span class="glyphicon glyphicon-check"></span>
                <span class="sidebar-title">成员申请审批</span>
                <span class="label label-sm label-primary ml10">ROOT</span>
              </a>
            </li>
          <?php endif; ?>

          <?php // —— 抗战专题：用于自动展开 ——
            // 当前 controller id
            $curCtl = Yii::$app->controller->id;
            // 展示模块 controller
            $warShowCtls = ['project-show']; 
            // 管理模块 controller（随时可加）
            $warManageCtls = [
              'war-event',
              'war-person',
              'war-message',
            ];

            // 当前是否命中“展示/管理”
            $isWarShowActive = in_array($curCtl, $warShowCtls, true);
            $isWarManageActive = in_array($curCtl, $warManageCtls, true);
          ?>

          <!-- 抗战专题 -->
          <li class="sidebar-label pt20">团队项目-抗战专题</li>

          <!-- 1) 项目数据展示：单独入口 -->
          <li class="<?= $isWarShowActive ? 'active' : '' ?>">
            <a href="<?= Url::to(['project-show/index']) ?>">
              <span class="glyphicon glyphicon-stats"></span>
              <span class="sidebar-title">项目数据展示</span>
            </a>
          </li>

          <!-- 2) 项目数据管理：点击展开 -->
          <?php if ($isRoot || $isMember): ?>
          <li class="<?= $isWarManageActive ? 'active' : '' ?>">
            <a class="accordion-toggle <?= $isWarManageActive ? '' : 'collapsed' ?>"
              data-toggle="collapse"
              href="#menu-war-manage"
              aria-expanded="<?= $isWarManageActive ? 'true' : 'false' ?>">
              <span class="glyphicon glyphicon-folder-open"></span>
              <span class="sidebar-title">项目数据管理</span>
              <span class="caret"></span>
            </a>

            <ul id="menu-war-manage" class="nav sub-nav collapse <?= $isWarManageActive ? 'in' : '' ?>">
              <!-- 核心 CRUD -->
              <li class="<?= $activeCtl('war-event') ?>">
                <a href="<?= Url::to(['war-event/index']) ?>">
                  <span class="glyphicon glyphicon-time"></span>
                  抗战事件
                </a>
              </li>
              <li class="<?= $activeCtl('war-person') ?>">
                <a href="<?= Url::to(['war-person/index']) ?>">
                  <span class="glyphicon glyphicon-education"></span>
                  抗战人物
                </a>
              </li>
              <li class="<?= $activeCtl('war-message') ?>">
                <a href="<?= Url::to(['war-message/index']) ?>">
                  <span class="glyphicon glyphicon-comment"></span>
                  留言审核
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

          <!-- 状态（可留可删） -->
          <li class="sidebar-label pt25 pb10">治理进度</li>
          <li class="sidebar-stat">
            <a href="#" class="fs11">
              <span class="fa fa-inbox text-info"></span>
              <span class="sidebar-title text-muted">后台信息梳理</span>
              <span class="pull-right mr20 text-muted">70%</span>
              <div class="progress progress-bar-xs mh20 mb10">
                <div class="progress-bar progress-bar-info" role="progressbar" style="width: 70%">
                  <span class="sr-only">70% Complete</span>
                </div>
              </div>
            </a>
          </li>

        </ul>
        <!-- End: Sidebar Menu -->

	      <!-- Start: Sidebar Collapse Button -->
	      <div class="sidebar-toggle-mini">
	        <a href="#">
	          <span class="fa fa-sign-out"></span>
	        </a>
	      </div>
	      <!-- End: Sidebar Collapse Button -->

      </div>
      <!-- End: Sidebar Left Content -->

    </aside>

    <!-- Start: Content-Wrapper -->
    <section id="content_wrapper">

      <!-- Start: Topbar-Dropdown -->
      <div id="topbar-dropmenu" class="alt">
        <div class="topbar-menu row">
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile bg-primary light">
              <span class="glyphicon glyphicon-inbox text-muted"></span>
              <span class="metro-title">Messages</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile bg-info light">
              <span class="glyphicon glyphicon-user text-muted"></span>
              <span class="metro-title">Users</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile bg-success light">
              <span class="glyphicon glyphicon-headphones text-muted"></span>
              <span class="metro-title">Support</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile bg-system light">
              <span class="glyphicon glyphicon-facetime-video text-muted"></span>
              <span class="metro-title">Videos</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile bg-warning light">
              <span class="fa fa-gears text-muted"></span>
              <span class="metro-title">Settings</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile bg-alert light">
              <span class="glyphicon glyphicon-picture text-muted"></span>
              <span class="metro-title">Pictures</span>
            </a>
          </div>
        </div>
      </div>
      <!-- End: Topbar-Dropdown -->

      <!-- Start: Topbar -->
      <header id="topbar" class="alt">
        <div class="topbar-left">
          <?= Breadcrumbs::widget([
            'homeLink' => ['label' => '主页', 'url' => ['site/index']],
            'links' => $this->params['breadcrumbs'] ?? [],
          ]) ?>
        </div>
        <div class="topbar-right">
          <!-- <div class="ib topbar-dropdown">
            <label for="topbar-multiple" class="control-label pr10 fs11 text-muted">Reporting Period</label>
            <select id="topbar-multiple" class="hidden">
              <optgroup label="Filter By:">
                <option value="1-1">Last 30 Days</option>
                <option value="1-2" selected="selected">Last 60 Days</option>
                <option value="1-3">Last Year</option>
              </optgroup>
            </select>
          </div> -->
          <div class="ml15 ib va-m" id="toggle_sidemenu_r">
            <a href="#" class="pl5">
              <i class="fa fa-sign-in fs22 text-primary"></i>
              <span class="badge badge-hero badge-danger"></span>
            </a>
          </div>
        </div>
      </header>
      <!-- End: Topbar -->

      <!-- Begin: Content -->
      <section id="content" class="table-layout animated fadeIn">
        <?= $content ?>
      
      </section>
      <!-- End: Content -->

    </section>

    <!-- Start: Right Sidebar -->
    <aside id="sidebar_right" class="nano affix">

      <!-- Start: Sidebar Right Content -->
      <div class="sidebar-right-content nano-content">

        <div class="tab-block sidebar-block br-n">
          <ul class="nav nav-tabs tabs-border nav-justified hidden">
            <li class="active">
              <a href="#sidebar-right-tab1" data-toggle="tab">Tab 1</a>
            </li>
            <li>
              <a href="#sidebar-right-tab2" data-toggle="tab">Tab 2</a>
            </li>
            <li>
              <a href="#sidebar-right-tab3" data-toggle="tab">Tab 3</a>
            </li>
          </ul>
          <div class="tab-content br-n">
            <div id="sidebar-right-tab1" class="tab-pane active">

              <h5 class="title-divider text-muted mb20"> Server Statistics
                <span class="pull-right"> 2013
                  <i class="fa fa-caret-down ml5"></i>
                </span>
              </h5>
              <div class="progress mh5">
                <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 44%">
                  <span class="fs11">DB Request</span>
                </div>
              </div>


              <h5 class="title-divider text-muted mt30 mb10">Traffic Margins</h5>
              <div class="row">
                <div class="col-xs-5">
                  <h3 class="text-primary mn pl5">132</h3>
                </div>
                <div class="col-xs-7 text-right">
                  <h3 class="text-success-dark mn">
                    <i class="fa fa-caret-up"></i> 13.2% </h3>
                </div>
              </div>

            </div>
            <div id="sidebar-right-tab2" class="tab-pane"></div>
            <div id="sidebar-right-tab3" class="tab-pane"></div>
          </div>
          <!-- end: .tab-content -->
        </div>
      </div>
    </aside>
    <!-- End: Right Sidebar -->

  </div>
  <!-- End: Main -->

  <!-- Admin Dock Quick Compose Message -->
 <!--  <div class="quick-compose-form">
    <form id="">
      <input type="email" class="form-control" id="inputEmail" placeholder="Email">
      <input type="text" class="form-control" id="inputSubject" placeholder="Subject">
      <div class="summernote-quick">Compose your message here...</div>
    </form>
  </div>
 -->

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

 
 <script type="text/javascript">
 <?php $this->beginBlock('js_end') ?>
  jQuery(document).ready(function() {

    "use strict";

    // Init Theme Core
    Core.init();

    // Init Demo JS
    Demo.init();

    var msgListing = $('#message-table > tbody > tr > td');
    var msgCheckbox = $('#message-table > tbody > tr input[type=checkbox]');

    // on message table checkbox click, toggle highlighted class
    msgCheckbox.on('click', function() {
      $(this).parents('tr').toggleClass('highlight');
    });

    // on message table row click, redirect page. Unless target was a checkbox
    msgListing.not(":first-child").on('click', function(e) {

      // stop event bubble if clicked item is not a checkbox
      e.stopPropagation();
      e.preventDefault();

      // Redirect to message compose page if clicked item is not a checkbox
      window.location = "pages_compose.html";
    });

    // On button click display quick compose message form
    $('#quick-compose').on('click', function() {

      // Admin Dock Plugin
      $('.quick-compose-form').dockmodal({
        minimizedWidth: 260,
        width: 470,
        height: 480,
        title: 'Compose Message',
        initialState: "docked",
        buttons: [{
          html: "Send",
          buttonClass: "btn btn-primary btn-sm",
          click: function(e, dialog) {
            // do something when the button is clicked
            dialog.dockmodal("close");

            // after dialog closes fire a success notification
            setTimeout(function() {
              msgCallback();
            }, 500);
          }
        }]
      });
    });

    // example email compose success notification
    function msgCallback() {
      (new PNotify({
        title: 'Message Success!',
        text: 'Your message has been <b>Sent.</b>',
        hide: false,
        type: 'success',
        addclass: "mt50",
        buttons: {
          closer: false,
          sticker: false
        },
        history: {
          history: false
        }
      }));
    };

    // Init Summernote
    $('.summernote-quick').summernote({
      height: 275, //set editable area's height
      focus: false, //set focus editable area after Initialize summernote
      toolbar: [
        ['style', ['bold', 'italic', 'underline', ]],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
      ]
    });

  });
  <?php $this->endBlock() ?>
  </script>
  <?php $this->registerJs($this->blocks['js_end'], yii\web\View::POS_LOAD) ?>
  
  <!-- END: PAGE SCRIPTS -->
<?php $this->endBody() ?>
</body>

<style>
/* ===== 侧边栏现代化样式增强 ===== */

/* 侧边栏选中项：渐变背景 + 左侧高亮条 */
#sidebar_left .sidebar-menu > li.active > a {
  background: linear-gradient(90deg, rgba(74,163,255,0.15) 0%, rgba(99,102,241,0.08) 100%) !important;
  color: #fff !important;
  border-left: 4px solid #4aa3ff;
  padding-left: 5px;
  border-radius: 0 8px 8px 0;
  margin-right: 8px;
  box-shadow: 0 4px 12px rgba(74,163,255,0.15);
}

/* 选中项图标增强 */
#sidebar_left .sidebar-menu > li.active > a .glyphicon,
#sidebar_left .sidebar-menu > li.active > a .fa {
  color: #4aa3ff !important;
  font-weight: 900;
}

/* hover 效果优化 */
#sidebar_left .sidebar-menu > li > a:hover {
  background: rgba(255,255,255,0.08);
  border-radius: 0 8px 8px 0;
  margin-right: 8px;
  transition: all 0.2s ease;
}

/* 侧边栏标签现代化 */
.sidebar-label {
  font-weight: 900 !important;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  font-size: 11px !important;
  color: rgba(255,255,255,0.5) !important;
}

/* 子菜单样式优化 */
#sidebar_left .sidebar-menu .sub-nav > li.active > a {
  background: rgba(74,163,255,0.12) !important;
  border-left: 3px solid #4aa3ff;
  padding-left: 28px;
  color: #fff !important;
}

#sidebar_left .sidebar-menu .sub-nav > li > a:hover {
  background: rgba(255,255,255,0.06);
  padding-left: 28px;
}

/* 徽章优化 */
.sidebar-menu .label {
  border-radius: 12px;
  padding: 3px 8px;
  font-weight: 900;
  font-size: 10px;
}

/* 面包屑现代化 */
.breadcrumb {
  background: transparent !important;
  padding: 8px 0 !important;
  margin: 0 !important;
}

.breadcrumb > li + li:before {
  content: "›" !important;
  padding: 0 8px;
  color: #94a3b8;
  font-size: 16px;
}

.breadcrumb > li {
  font-weight: 700;
}

.breadcrumb > li > a {
  color: #64748b;
  text-decoration: none;
}

.breadcrumb > li > a:hover {
  color: #3b82f6;
}

.breadcrumb > .active {
  color: #0f172a;
  font-weight: 900;
}

/* 内容区域优化 */
#content {
  background: #f8fafc !important;
  min-height: calc(100vh - 160px);
  padding: 20px !important;
}

/* footer 优化 */
.footer {
  background: #fff;
  border-top: 1px solid rgba(0,0,0,0.06);
  margin-top: 0;
  padding: 20px 0;
}

.footer p {
  margin: 0;
  color: #64748b;
  font-weight: 700;
  font-size: 13px;
}
</style>

</html>
<?php $this->endPage() ?>
