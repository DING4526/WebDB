<?php
/**
 * 新建抗战事件（Workspace 模式）
 */
$this->title = '新增事件';
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
$this->registerCssFile('@web/css/war-event.css');

echo $this->render('_workspace', [
  'mode' => 'create',
  'model' => $model,
  'stageList' => $stageList,
]);
