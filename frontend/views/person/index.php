<?php
/**
 * 苏奕扬 2311330
 * 前台人物列表视图
 */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = '抗战人物';
?>

<style>
.sidebar-dark {
    background-color: #fff;
    padding: 20px 0;
    min-height: 100vh;
    border-radius: 4px;
    height: 100%;
    border: 1px solid #ddd;
}
.sidebar-dark .nav-link {
    display: block;
    padding: 15px 25px;
    color: #333;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.3s;
}
.sidebar-dark .nav-link:hover {
    color: #000;
    background-color: #f5f5f5;
}
.sidebar-dark .nav-link.active {
    color: #000;
    background-color: #e0e0e0;
    font-weight: bold;
}
</style>

<div class="person-index">
    <div class="row" style="display: flex; flex-wrap: wrap;">
        <!-- 左侧角色分类栏 -->
        <div class="col-md-2" style="padding-right: 0; display: flex; flex-direction: column;">
            <!-- <h3 style="margin-top:0; margin-bottom:20px; text-align: center;">
                <?= Html::encode($this->title) ?>
            </h3> -->
            <div class="sidebar-dark" style="flex: 1;">
                <a href="<?= \yii\helpers\Url::to(['index']) ?>"
                   class="nav-link <?= $currentRole === null ? 'active' : '' ?>">
                    抗战人物
                </a>
                <?php foreach ($roles as $role): ?>
                    <a href="<?= \yii\helpers\Url::to(['index', 'role_type' => $role]) ?>"
                       class="nav-link <?= $currentRole === $role ? 'active' : '' ?>">
                        <?= Html::encode($role) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 右侧人物列表 -->
        <div class="col-md-10">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => function ($model) {
                    /** @var \common\models\WarPerson $model */
                    $img = '';
                    if ($model->coverImage) {
                        $img = Html::img($model->coverImage->path, [
                            'class' => 'img-thumbnail',
                            'style' => 'width:100%; height:200px; object-fit:contain; margin-bottom:10px;'
                        ]);
                    }
                    return '<div class="panel panel-default">'
                        . '<div class="panel-heading"><strong>' . Html::encode($model->name) . '</strong> '
                        . '<span class="text-muted">' . Html::encode($model->role_type) . '</span></div>'
                        . '<div class="panel-body">'
                        . $img
                        . '<p>' . Html::encode($model->intro) . '</p>'
                        . Html::a('查看详情', ['view', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs'])
                        . '</div></div>';
                },
                'summary' => '',
                'emptyText' => '<div class="alert alert-info">暂无人物数据，请先在后台录入。</div>',
                'options' => ['class' => 'row'],
                'itemOptions' => ['class' => 'col-md-4 col-sm-6'],
            ]) ?>
        </div>
    </div>
</div>

