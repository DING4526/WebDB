<?php

/**
 * Ding 2310724
 * 抗战人物详情
 */
/* @var $this yii\web\View */
/* @var $model common\models\WarPerson */
/* @var $eventOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */
/* @var $relationMap array */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
$this->registerCssFile('@web/css/war-person.css');

echo $this->render('_workspace', [
  'mode' => 'view',
  'model' => $model,
  'mediaList' => $mediaList,
  'relationMap' => $relationMap,
]);
