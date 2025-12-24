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
          <a href="#" id="helpBtn" data-toggle="modal" data-target="#helpModal">
            <span class="fa fa-question-circle pr5"></span> 帮助
          </a>
        </li>
        <li class="hidden-xs">
                     <a href="<?php echo Url::to(['site/logout']) ?>" data-method="post">
              <span class="fa fa-power-off pr5"></span> 登出 </a>
        </li>
      </ul>
      
      
    </header>
    <!-- End: Header -->

    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header" style="background: linear-gradient(135deg, #8B2500 0%, #6B4423 50%, #4A5568 100%); color: #fff; border-radius: 5px 5px 0 0;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="helpModalLabel">
              <span class="fa fa-question-circle"></span> 帮助中心
            </h4>
          </div>
          <div class="modal-body" id="helpModalBody" style="padding: 0;">
            <div style="text-align: center; padding: 40px;">
              <span class="fa fa-spinner fa-spin fa-2x"></span>
              <p style="margin-top: 10px; color: #64748b;">加载中...</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
          </div>
        </div>
      </div>
    </div>

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
            </a>
          </li>
          <?php endif; ?>

          <?php if ($isRoot): ?>
            <li class="<?= $activeCtl('team-member-apply') ?>">
              <a href="<?= Url::to(['team-member-apply/index']) ?>">
                <span class="glyphicon glyphicon-check"></span>
                <span class="sidebar-title">成员申请审批</span>
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

          <!-- 2) 项目数据管理：扁平化菜单 -->
          <?php if ($isRoot || $isMember): ?>
          <li class="<?= $activeCtl('war-event') ?>">
            <a href="<?= Url::to(['war-event/index']) ?>">
              <span class="glyphicon glyphicon-time"></span>
              <span class="sidebar-title">抗战事件管理</span>
            </a>
          </li>
          <li class="<?= $activeCtl('war-person') ?>">
            <a href="<?= Url::to(['war-person/index']) ?>">
              <span class="glyphicon glyphicon-education"></span>
              <span class="sidebar-title">抗战人物管理</span>
            </a>
          </li>
          <li class="<?= $activeCtl('war-message') ?>">
            <a href="<?= Url::to(['war-message/index']) ?>">
              <span class="glyphicon glyphicon-comment"></span>
              <span class="sidebar-title">留言审核</span>
            </a>
          </li>
          <?php endif; ?>

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
      </header>
      <!-- End: Topbar -->

      <style>
        #topbar.alt{ 
          min-height:50px !important; 
          padding:8px 15px 8px 30px !important; 
        }
      </style>

      <!-- Begin: Content -->
      <section id="content" class="table-layout animated fadeIn">
        <?= $content ?>
      
      </section>
      <!-- End: Content -->

    </section>
    <!-- End: Content-Wrapper -->

  </div>
  <!-- End: Main -->

  <!-- BEGIN: PAGE SCRIPTS -->
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

      // Help Modal - 加载帮助内容
      $('#helpModal').on('show.bs.modal', function() {
        var $body = $('#helpModalBody');
        $body.html('<div style="text-align: center; padding: 40px;"><span class="fa fa-spinner fa-spin fa-2x"></span><p style="margin-top: 10px; color: #64748b;">加载中...</p></div>');
        $.ajax({
          url: '<?= Url::to(['site/help']) ?>',
          type: 'GET',
          success: function(html) {
            $body.html(html);
          },
          error: function() {
            $body.html('<div style="text-align: center; padding: 40px; color: #ef4444;"><span class="fa fa-exclamation-triangle fa-2x"></span><p style="margin-top: 10px;">加载失败，请稍后重试</p></div>');
          }
        });
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
  background: linear-gradient(90deg, rgba(139,37,0,0.15) 0%, rgba(107,68,35,0.08) 100%) !important;
  color: #fff !important;
  border-left: 4px solid #A52A2A;
  padding-left: 5px;
  border-radius: 0 8px 8px 0;
  margin-right: 8px;
  box-shadow: 0 4px 12px rgba(139,37,0,0.15);
}

/* 选中项图标增强 */
#sidebar_left .sidebar-menu > li.active > a .glyphicon,
#sidebar_left .sidebar-menu > li.active > a .fa {
  color: #CD853F !important;
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
  background: rgba(139,37,0,0.12) !important;
  border-left: 3px solid #A52A2A;
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
  color: #8B7355;
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
  color: #B8860B;
}

.breadcrumb > .active {
  color: #0f172a;
  font-weight: 900;
}

/* 内容区域优化 */
#content {
  background: #F5F1E8 !important;
  min-height: calc(100vh - 160px);
  padding: 20px !important;
}

</style>

</html>
<?php $this->endPage() ?>
