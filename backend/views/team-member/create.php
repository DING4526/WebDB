<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

$this->title = '新增成员';
$this->params['breadcrumbs'][] = ['label' => '成员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
?>
<div class="team-member-create">

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="adm-card-body adm-form">
      <?= $this->render('_form', [
          'model' => $model,
      ]) ?>
    </div>
  </div>

</div>
