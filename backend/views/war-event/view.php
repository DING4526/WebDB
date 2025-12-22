<?php
/**
 * 抗战事件详情（Workspace 模式）
 */
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/war-event.css');

echo $this->render('_workspace', [
  'mode' => 'view',
  'model' => $model,
  'mediaList' => $mediaList,
  'relationMap' => $relationMap,
]);
