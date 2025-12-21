<?php

/**
 * Ding 2310724
 * 创建抗战人物
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarPerson */

$this->title = '新增人物';
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-person-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
