<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '成员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
\yii\web\YiiAsset::register($this);
$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
?>
<div class="team-member-view">

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">
        <?= Html::encode($this->title) ?>
      </h3>
      <div>
        <?php if (!$isRoot): ?>
          <span class="adm-badge adm-badge-info">只读</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="adm-card-body">
      <div style="margin-bottom:16px;">
        <?php if ($isRoot): ?>
          <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-soft-primary']) ?>
          <?= Html::a('删除', ['delete', 'id' => $model->id], [
              'class' => 'btn btn-soft-danger',
              'data' => [
                  'confirm' => '确认删除该成员？',
                  'method' => 'post',
              ],
          ]) ?>
          <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-ghost']) ?>
        <?php else: ?>
          <span class="adm-hint">仅 root 可编辑/删除。</span>
          <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-ghost']) ?>
        <?php endif; ?>
      </div>

      <?= DetailView::widget([
          'model' => $model,
          'options' => ['class' => 'table table-striped table-bordered detail-view'],
          'attributes' => [
              'team_id',
              [
                  'attribute' => 'user_id',
                  'value' => $model->user ? $model->user->username : '（未关联）',
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
