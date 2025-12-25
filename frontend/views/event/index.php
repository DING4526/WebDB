<?php
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $location string|null 接收 Controller 传来的 location 变量 */

$this->title = '抗战事件';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!empty($location)): ?>
        <div class="alert alert-info">
            当前正在查看 <strong><?= Html::encode($location) ?></strong> 的抗战历史事件。
            <?= Html::a('查看全部', ['index'], ['class' => 'btn btn-xs btn-default']) ?>
        </div>
    <?php endif; ?>

    <!-- 现有事件列表渲染 -->
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            // 简单的列表项展示示例，请保留你原有的 itemView
            return Html::a(Html::encode($model->title), ['view', 'id' => $model->id]);
        },
    ]) ?>

</div>
