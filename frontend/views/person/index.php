<?php
/**
 * 苏奕扬 2311330
 * 前台人物列表视图
 */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = '抗战人物志';
?>

<style>
/* 复用/模仿 Timeline 风格 */
.page-header {
    margin: 40px 0 20px;
    border-bottom: 1px solid #eee;
}

/* 侧边栏样式优化 */
.sidebar-wrapper {
    background: #fff;
    border: 1px solid #e1e4e8;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}
.sidebar-header {
    background-color: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e1e4e8;
    font-weight: bold;
    color: #8b0000;
    font-size: 16px;
}
.sidebar-nav .nav-link {
    display: block;
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}
.sidebar-nav .nav-link:last-child {
    border-bottom: none;
}
.sidebar-nav .nav-link:hover {
    background-color: #fff5f5;
    color: #a94442;
    padding-left: 25px;
}
.sidebar-nav .nav-link.active {
    background-color: #a94442;
    color: #fff;
    border-left-color: #8b0000;
}

/* 人物卡片样式 - 模仿 Timeline Panel */
.person-card {
    background: #fff;
    border: 1px solid #e1e4e8;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    margin-bottom: 25px;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.person-card:hover {
    box-shadow: 0 8px 20px rgba(169, 68, 66, 0.15);
    border-color: #a94442;
    transform: translateY(-3px);
}
.person-img-wrapper {
    height: 220px;
    overflow: hidden;
    position: relative;
    background-color: #f8f9fa;
    border-bottom: 1px solid #f0f0f0;
}
.person-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* 改为 contain 以完整显示图片 */
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
    color: #333;
    margin-bottom: 5px;
}
.person-role {
    display: inline-block;
    font-size: 12px;
    color: #fff;
    background-color: #a94442;
    padding: 2px 8px;
    border-radius: 10px;
    margin-bottom: 10px;
}
.person-intro {
    font-size: 13px;
    color: #666;
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
                'layout' => "{items}\n<div class='text-center'>{pager}</div>",
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

