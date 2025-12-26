<?php
/**
 * 苏奕扬 2311330
 * 前台人物列表视图 - 深色主题优化版
 * 注意：主题色变量定义在 site.css 中
 */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = '抗战人物志';
?>

<style>
/* 页面头部 */
.page-header {
    margin: 40px 0 20px;
    border-bottom: none;
}

.page-header h1 {
    color: var(--gold-primary) !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.page-header .text-muted {
    color: var(--text-light) !important;
    opacity: 0.8;
}

/* 分隔线 */
.page-header div[style*="background"] {
    background: linear-gradient(90deg, transparent, var(--gold-primary), transparent) !important;
}

/* 侧边栏样式优化 */
.sidebar-wrapper {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.sidebar-header {
    background: rgba(201, 162, 39, 0.1);
    padding: 15px 20px;
    border-bottom: 1px solid var(--card-border);
    font-weight: bold;
    color: var(--gold-primary);
    font-size: 16px;
}

.sidebar-nav .nav-link {
    display: block;
    padding: 12px 20px;
    color: var(--text-light);
    text-decoration: none;
    border-bottom: 1px solid var(--card-border);
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.sidebar-nav .nav-link:last-child {
    border-bottom: none;
}

.sidebar-nav .nav-link:hover {
    background-color: rgba(201, 162, 39, 0.1);
    color: var(--gold-light);
    padding-left: 25px;
    border-left-color: var(--gold-muted);
}

.sidebar-nav .nav-link.active {
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted));
    color: var(--text-dark);
    border-left-color: var(--gold-light);
    font-weight: 600;
}

/* 人物卡片样式 */
.person-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    margin-bottom: 25px;
    height: 420px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.person-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(201, 162, 39, 0.3);
    border-color: var(--gold-muted);
    transform: translateY(-5px);
}

.person-img-wrapper {
    height: 220px;
    overflow: hidden;
    position: relative;
    background-color: rgba(20, 15, 10, 0.5);
    border-bottom: 1px solid var(--card-border);
}

.person-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.5s ease;
}

.person-card:hover .person-img-wrapper img {
    transform: scale(1.05);
}

.person-info {
    padding: 15px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.person-name {
    font-size: 18px;
    font-weight: bold;
    color: var(--text-light);
    margin-bottom: 5px;
}

.person-role {
    display: inline-block;
    font-size: 12px;
    color: var(--text-dark);
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted));
    padding: 2px 10px;
    border-radius: 10px;
    margin-bottom: 10px;
    font-weight: 600;
}

.person-intro {
    font-size: 13px;
    color: var(--text-light);
    opacity: 0.75;
    line-height: 1.6;
    margin-bottom: 15px;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.person-footer {
    text-align: right;
    margin-top: auto;
}

.person-footer .btn-danger {
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted)) !important;
    border: none !important;
    color: var(--text-dark) !important;
    font-weight: 600;
    transition: all 0.2s ease;
}

.person-footer .btn-danger:hover {
    background: linear-gradient(135deg, var(--gold-light), var(--gold-primary)) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.4);
}

/* 分页样式 */
.pagination > li > a,
.pagination > li > span {
    color: var(--gold-primary);
    background-color: var(--card-bg);
    border: 1px solid var(--card-border);
}

.pagination > li > a:hover,
.pagination > li > span:hover,
.pagination > li > a:focus,
.pagination > li > span:focus {
    color: var(--gold-light);
    background-color: rgba(201, 162, 39, 0.15);
    border-color: var(--gold-muted);
}

.pagination > .active > a,
.pagination > .active > span,
.pagination > .active > a:hover,
.pagination > .active > span:hover,
.pagination > .active > a:focus,
.pagination > .active > span:focus {
    z-index: 2;
    color: var(--text-dark);
    cursor: default;
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted));
    border-color: var(--gold-primary);
}

.pagination > .disabled > span,
.pagination > .disabled > span:hover,
.pagination > .disabled > span:focus,
.pagination > .disabled > a,
.pagination > .disabled > a:hover,
.pagination > .disabled > a:focus {
    color: rgba(245, 230, 200, 0.3);
    background-color: var(--card-bg);
    border-color: var(--card-border);
}

/* 无数据提示 */
.alert-warning {
    background: rgba(201, 162, 39, 0.15) !important;
    border-color: var(--card-border) !important;
    color: var(--text-light) !important;
}
</style>

<div class="person-index container">
    
    <div class="page-header text-center" style="border-bottom: none; margin-bottom: 40px;">
        <h1 style="color: #8b0000; font-weight: bold; margin-bottom: 10px;">
            <i class="glyphicon glyphicon-user"></i> 抗战人物志
        </h1>
        <p class="text-muted" style="font-size: 16px;">铭记历史，缅怀先烈，传承抗战精神</p>
        <div style="width: 60px; height: 3px; background: #a94442; margin: 20px auto;"></div>
    </div>

    <div class="row">
        <!-- 左侧角色分类栏 -->
        <div class="col-md-3">
            <div class="sidebar-wrapper">
                <div class="sidebar-header">
                    <i class="glyphicon glyphicon-filter"></i> 人物分类
                </div>
                <div class="sidebar-nav">
                    <a href="<?= \yii\helpers\Url::to(['index']) ?>"
                       class="nav-link <?= $currentRole === null ? 'active' : '' ?>">
                        全部人物
                    </a>
                    <?php foreach ($roles as $role): ?>
                        <a href="<?= \yii\helpers\Url::to(['index', 'role_type' => $role]) ?>"
                           class="nav-link <?= $currentRole === $role ? 'active' : '' ?>">
                            <?= Html::encode($role) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 右侧人物列表 -->
        <div class="col-md-9">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{items}\n<div class='col-xs-12 text-center'>{pager}</div>",
                'itemView' => function ($model) {
                    /** @var \common\models\WarPerson $model */
                    $imgUrl = $model->coverImage ? $model->coverImage->path : 'images/default-person.png'; // 假设有个默认图
                    // 如果没有默认图，可以用一个占位符或者判断
                    $imgTag = '';
                    if ($model->coverImage) {
                        $imgTag = Html::img($model->coverImage->path, ['alt' => $model->name]);
                    } else {
                        // 简单的文字占位
                        $imgTag = '<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ccc; background:#f0f0f0;"><i class="glyphicon glyphicon-picture" style="font-size:40px;"></i></div>';
                    }

                    return '<div class="col-md-4 col-sm-6">'
                        . '<div class="person-card">'
                            . '<div class="person-img-wrapper">'
                                . Html::a($imgTag, ['view', 'id' => $model->id])
                            . '</div>'
                            . '<div class="person-info">'
                                . '<div>'
                                    . '<div class="person-name">' . Html::encode($model->name) . '</div>'
                                    . '<span class="person-role">' . Html::encode($model->role_type) . '</span>'
                                . '</div>'
                                . '<div class="person-intro">' . Html::encode($model->intro) . '</div>'
                                . '<div class="person-footer">'
                                    . Html::a('查看详情 <i class="glyphicon glyphicon-menu-right"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-danger btn-xs', 'style' => 'border-radius: 15px; padding: 4px 12px;'])
                                . '</div>'
                            . '</div>'
                        . '</div>'
                        . '</div>';
                },
                'summary' => '',
                'emptyText' => '<div class="alert alert-warning text-center" style="margin-top: 20px;">暂无相关人物数据。</div>',
                'options' => ['class' => 'row'],
                'itemOptions' => ['tag' => false], // 移除包裹的 div，因为我们在 itemView 里自己写了 col-md-4
            ]) ?>
        </div>
    </div>
</div>

