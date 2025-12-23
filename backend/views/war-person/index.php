<?php

/**
 * Ding 2310724
 * 抗战人物列表
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WarPersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抗战人物管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/war-person.css');

$totalCount = (int)$dataProvider->getTotalCount();
?>

<div class="we-page">

  <div class="we-hero">
    <div class="we-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">创建、编辑人物并维护事件关联与媒资。</div>
      </div>
      <div class="we-actions">
        <?= Html::a('新增人物', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <div class="we-card">
    <div class="we-card-head">
      <h3 class="we-card-title">人物列表</h3>
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
                  'attribute' => 'name',
                  'contentOptions' => ['style' => 'font-weight:900;'],
              ],
              [
                  'attribute' => 'role_type',
                  'contentOptions' => ['class' => 'we-muted'],
              ],
              [
                  'attribute' => 'birth_year',
                  'contentOptions' => ['class' => 'we-muted'],
              ],
              [
                  'attribute' => 'death_year',
                  'contentOptions' => ['class' => 'we-muted'],
              ],
              [
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($model) {
                      $isPublished = (bool)$model->status;
                      return $isPublished
                          ? '<span class="we-badge we-badge-pub">展示</span>'
                          : '<span class="we-badge we-badge-draft">隐藏</span>';
                  },
                  'filter' => [1 => '展示', 0 => '隐藏'],
                  'contentOptions' => ['style' => 'width:90px;'],
              ],
              [
                  'class' => 'yii\grid\ActionColumn',
                  'header' => '操作',
                  'template' => '{view} {update} {toggle-status} {delete}',
                  'buttons' => [
                      'view' => function ($url, $model) {
                          return Html::a('查看', ['view', 'id' => $model->id], ['class' => 'btn btn-xs btn-ghost']);
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
                                  'confirm' => '确认删除该人物？',
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
