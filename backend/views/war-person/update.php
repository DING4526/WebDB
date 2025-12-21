<?php

/**
 * Ding 2310724
 * 编辑抗战人物
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarPerson */

$this->title = '编辑人物: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="war-person-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
