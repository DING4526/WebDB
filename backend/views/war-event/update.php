<?php
/**
 * 编辑抗战事件（Workspace 模式）
 */
$this->title = '编辑事件：' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title];
$this->registerCssFile('@web/css/admin-common.css');
$this->registerCssFile('@web/css/war-event.css');

echo $this->render('_workspace', [
  'mode' => $mode ?? 'edit',
  'model' => $model,
  'stageList' => $stageList,
  'personOptions' => $personOptions,
  'relationForm' => $relationForm,
  'mediaForm' => $mediaForm,
  'mediaList' => $mediaList,
  'relationMap' => $relationMap,
]);
