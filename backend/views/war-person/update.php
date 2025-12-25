<?php

/**
 * Ding 2310724
 * 编辑抗战人物
 */
 /* @var $this yii\web\View */
 /* @var $model common\models\WarPerson */
/* @var $eventOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */
/* @var $relationMap array */

$this->title = '编辑人物: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
$this->registerCssFile('@web/css/admin-common.css');
$this->registerCssFile('@web/css/war-person.css');

echo $this->render('_workspace', [
  'mode' => $mode ?? 'edit',
  'model' => $model,
  'eventOptions' => $eventOptions,
  'relationForm' => $relationForm,
  'mediaForm' => $mediaForm,
  'mediaList' => $mediaList,
  'relationMap' => $relationMap,
]);
