<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

$this->title = '查看成员';
$this->params['breadcrumbs'][] = ['label' => '团队管理', 'url' => ['/team/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
\yii\web\YiiAsset::register($this);
$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
?>
<div class="team-member-view">

  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($model->name) ?></h2>
        <div class="desc">成员详细信息</div>
      </div>
      <div class="adm-actions">
        <?php if ($isRoot): ?>
          <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
          <?= Html::a('删除', ['delete', 'id' => $model->id], [
              'class' => 'btn btn-danger',
              'data' => [
                  'confirm' => '确认删除该成员？',
                  'method' => 'post',
              ],
          ]) ?>
        <?php endif; ?>
        <?= Html::a('返回团队管理', ['/team/index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">成员信息</h3>
      <?php if (!$isRoot): ?>
        <span class="adm-badge adm-badge-info">只读</span>
      <?php endif; ?>
    </div>
    <div class="adm-card-body">
      <?= DetailView::widget([
          'model' => $model,
          'options' => ['class' => 'table table-striped table-bordered detail-view'],
          'attributes' => [
              [
                  'attribute' => 'team_id',
                  'value' => $model->team ? $model->team->name : $model->team_id,
              ],
              'name',
              'student_no',
              'role',
              'work_scope:ntext',
              [
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($model) {
                      $isActive = (bool)$model->status;
                      return $isActive
                          ? '<span class="adm-badge adm-badge-active">正常</span>'
                          : '<span class="adm-badge adm-badge-inactive">禁用</span>';
                  },
              ],
              [
                  'attribute' => 'created_at',
                  'format' => ['datetime', 'php:Y-m-d H:i'],
              ],
              [
                  'attribute' => 'updated_at',
                  'format' => ['datetime', 'php:Y-m-d H:i'],
              ],
          ],
      ]) ?>
    </div>
  </div>

</div>
