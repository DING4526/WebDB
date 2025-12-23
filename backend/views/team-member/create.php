<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

$this->title = '新增成员';
$this->params['breadcrumbs'][] = ['label' => '团队管理', 'url' => ['/team/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
?>
<div class="team-member-create">

  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">填写成员基本信息</div>
      </div>
      <div class="adm-actions">
        <?= Html::a('返回团队管理', ['/team/index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">成员信息</h3>
    </div>
    <div class="adm-card-body">
      <?= $this->render('_form', [
          'model' => $model,
      ]) ?>
    </div>
  </div>

</div>
