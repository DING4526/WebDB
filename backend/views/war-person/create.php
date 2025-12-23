<?php

/**
 * Ding 2310724
 * 创建抗战人物
 */
 /* @var $this yii\web\View */
 /* @var $model common\models\WarPerson */

$this->title = '新增人物';
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/war-person.css');

echo $this->render('_workspace', [
  'mode' => 'create',
  'model' => $model,
]);
