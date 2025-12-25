<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\TeamMemberApply;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeamMemberApplySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '成员申请审批';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');

$totalCount = (int)$dataProvider->getTotalCount();
?>
<div class="team-member-apply-index">

  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">审批与管理成员加入申请</div>
      </div>
      <div class="adm-actions">
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">申请列表</h3>
      <span class="adm-pill"><span class="adm-dot"></span> 当前筛选总数：<?= $totalCount ?></span>
    </div>

    <?php Pjax::begin(['timeout' => 8000]); ?>
    <div class="adm-grid">
      <?= GridView::widget([
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'summary' => false,
          'tableOptions' => ['class' => 'table table-hover'],
          'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              [
                  'attribute' => 'team_id',
                  'value' => function ($model) {
                      return $model->team->name ?? '';
                  },
                  'filter' => false,
                  'contentOptions' => ['style' => 'font-weight:900;'],
              ],
              'name',
              'student_no',
              [
                  'attribute' => 'email',
                  'contentOptions' => ['class' => 'adm-muted'],
              ],
              [
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($model) {
                      $status = $model->status;
                      if ($status == TeamMemberApply::STATUS_PENDING) {
                          return '<span class="adm-badge adm-badge-pending">待审核</span>';
                      } elseif ($status == TeamMemberApply::STATUS_APPROVED) {
                          return '<span class="adm-badge adm-badge-active">已通过</span>';
                      } else {
                          return '<span class="adm-badge adm-badge-inactive">已拒绝</span>';
                      }
                  },
                  'filter' => TeamMemberApply::statusList(),
                  'contentOptions' => ['style' => 'width:100px;'],
              ],
              [
                  'attribute' => 'reviewer_id',
                  'value' => function ($model) {
                      return $model->reviewer->username ?? '';
                  },
                  'contentOptions' => ['class' => 'adm-muted'],
              ],
              [
                  'attribute' => 'created_at',
                  'format' => ['datetime', 'php:Y-m-d H:i'],
                  'contentOptions' => ['class' => 'adm-muted'],
              ],
              [
                  'class' => 'yii\grid\ActionColumn',
                  'header' => '操作',
                  'template' => '{reason} {approve} {reject}',
                  'buttons' => [
                      'reason' => function ($url, $model) {
                            $reason = trim((string)$model->reason);
                            if ($reason === '') return '';

                            return Html::a('查看', 'javascript:;', [
                                'class' => 'btn btn-xs btn-soft-ghost',
                                'title' => '查看申请原因',
                                'onclick' => "alert(" . json_encode($reason, JSON_UNESCAPED_UNICODE) . "); return false;",
                            ]);
                       },

                      'approve' => function ($url, $model) {
                          if ($model->status != TeamMemberApply::STATUS_PENDING) {
                              return '';
                          }
                          return Html::a('通过', ['approve', 'id' => $model->id], [
                              'class' => 'btn btn-xs btn-soft-success',
                              'data-method' => 'post',
                              'data-pjax' => 1,
                          ]);
                      },
                      'reject' => function ($url, $model) {
                          if ($model->status != TeamMemberApply::STATUS_PENDING) {
                              return '';
                          }
                          return Html::a('拒绝', ['reject', 'id' => $model->id], [
                              'class' => 'btn btn-xs btn-soft-danger',
                              'data-method' => 'post',
                              'data-pjax' => 1,
                          ]);
                      },
                  ],
                  'contentOptions' => ['class' => 'adm-actions-col', 'style' => 'min-width:150px;'],
              ],
          ],
      ]); ?>
    </div>
    <?php Pjax::end(); ?>
  </div>

</div>
