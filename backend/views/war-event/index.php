<?php

/**
 * Ding 2310724
 * 抗战事件列表
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WarEventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抗战事件管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
$this->registerCssFile('@web/css/war-event.css');

$totalCount = (int)$dataProvider->getTotalCount(); 
?>

<div class="we-page">

  <div class="we-hero">
    <div class="we-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">创建、编辑事件并维护人物关联与媒资。</div>
      </div>
      <div class="we-actions">
        <?= Html::a('新增事件', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <div class="we-card">
    <div class="we-card-head">
      <h3 class="we-card-title">事件列表</h3>
      <span class="we-pill"><span class="we-dot"></span> 当前筛选总数：<?= $totalCount ?></span>
    </div>

    <?php Pjax::begin(['timeout' => 8000]); ?>
    <div class="we-grid">
      <?= GridView::widget([
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'summary' => false,
          'tableOptions' => ['class' => 'table table-hover'],
          'columns' => [
              ['class' => 'yii\grid\SerialColumn'],

              [
                  'attribute' => 'title',
                  'contentOptions' => ['style' => 'font-weight:900;'],
              ],
              'event_date',
              [
                  'attribute' => 'stage_id',
                  'value' => function ($model) {
                      return $model->stage->name ?? '';
                  },
                  'contentOptions' => ['class' => 'we-muted'],
              ],
              [
                  'attribute' => 'location',
                  'contentOptions' => ['class' => 'we-muted'],
              ],
              [
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($model) {
                      $isPublished = (bool)$model->status;
                      return $isPublished
                          ? '<span class="we-badge we-badge-pub">已发布</span>'
                          : '<span class="we-badge we-badge-draft">未发布</span>';
                  },
                  'filter' => [1 => '已发布', 0 => '未发布'],
                  'contentOptions' => ['style' => 'width:90px;'],
              ],
              [
                  'class' => 'yii\grid\ActionColumn',
                  'header' => '操作',
                  'template' => '{view} {update} {toggle-status} {delete}',
                  'buttons' => [
                      'view' => function ($url, $model) {
                          return Html::a('查看', ['view', 'id' => $model->id], ['class' => 'btn btn-xs btn-soft-ghost']);
                      },
                      'update' => function ($url, $model) {
                          return Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-soft-primary']);
                      },
                      'toggle-status' => function ($url, $model) {
                          $isPublished = (bool)$model->status;
                          return Html::a($isPublished ? '下线' : '发布', ['toggle-status', 'id' => $model->id], [
                              'class' => 'btn btn-xs ' . ($isPublished ? 'btn-soft-warning' : 'btn-soft-success'),
                              'data-method' => 'post',
                              'data-pjax' => 1,
                          ]);
                      },
                      'delete' => function ($url, $model) {
                          return Html::a('删除', ['delete', 'id' => $model->id], [
                              'class' => 'btn btn-xs btn-soft-danger',
                              'data' => [
                                  'confirm' => '确认删除该事件？',
                                  'method' => 'post',
                              ],
                              'data-pjax' => 0,
                          ]);
                      },
                  ],
                  'contentOptions' => ['class' => 'we-actions-col', 'style' => 'min-width:260px;'],
              ],
          ],
      ]); ?>
    </div>
    <?php Pjax::end(); ?>
  </div>

</div>