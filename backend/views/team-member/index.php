<?php

// http://localhost/advanced/backend/web/index.php?r=team-member

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\TeamMember;
use yii\helpers\ArrayHelper;
use common\models\Team;
use common\models\User;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeamMemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '成员管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');

$user = Yii::$app->user->getUser();
$isRoot = $user && $user->isRoot();

$userList = ArrayHelper::map(
    User::find()->orderBy(['id' => SORT_DESC])->all(),
    'id',
    'username'
);

$totalCount = (int)$dataProvider->getTotalCount();
?>
<div class="team-member-index">

  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">管理团队成员信息与权限</div>
      </div>
      <div class="adm-actions">
        <?php if ($isRoot): ?>
          <?= Html::a('新增成员', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">成员列表</h3>
      <span class="adm-pill"><span class="adm-dot"></span> 当前筛选总数：<?= $totalCount ?></span>
    </div>

    <?php Pjax::begin(['timeout' => 8000]); ?>
    <div class="adm-grid">
      <?php if (!$isRoot): ?>
        <div class="adm-hint adm-mb-12">仅 root 可新增/编辑成员，当前为只读模式。</div>
      <?php endif; ?>

      <?= GridView::widget([
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'summary' => false,
          'tableOptions' => ['class' => 'table table-hover'],
           'columns' => [
               ['class' => 'yii\grid\SerialColumn'],
               [
                   'attribute' => 'user_id',
                   'value' => function ($m) {
                       return $m->user ? $m->user->username : '';
                   },
                  'filter' => $userList,
                  'contentOptions' => ['style' => 'font-weight:900;'],
              ],
              'name',
              'student_no',
              [
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($m) {
                      $isActive = (bool)$m->status;
                      return $isActive
                          ? '<span class="adm-badge adm-badge-active">正常</span>'
                          : '<span class="adm-badge adm-badge-inactive">禁用</span>';
                  },
                  'filter' => TeamMember::getStatusList(),
                  'contentOptions' => ['style' => 'width:90px;'],
              ],
              [
                  'class' => 'yii\grid\ActionColumn',
                  'header' => '操作',
                  'template' => $isRoot ? '{view} {update} {delete}' : '{view}',
                  'buttons' => [
                      'view' => function ($url, $model) {
                          return Html::a('查看', ['view', 'id' => $model->id], ['class' => 'btn btn-xs btn-ghost']);
                      },
                      'update' => function ($url, $model) {
                          return Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-soft-primary']);
                      },
                      'delete' => function ($url, $model) {
                          return Html::a('删除', ['delete', 'id' => $model->id], [
                              'class' => 'btn btn-xs btn-soft-danger',
                              'data' => [
                                  'confirm' => '确认删除该成员？',
                                  'method' => 'post',
                              ],
                              'data-pjax' => 0,
                          ]);
                      },
                  ],
                  'contentOptions' => ['class' => 'adm-actions-col', 'style' => 'min-width:200px;'],
              ],
          ],
      ]); ?>
    </div>
    <?php Pjax::end(); ?>
  </div>

</div>
