<?php

/**
 * Ding 2310724
 * 团队项目后台管理（占位）
 */

use yii\helpers\Html;

$this->title = '团队项目网站管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<h2><?= Html::encode($this->title) ?></h2>
<div class="alert alert-info">
  已完成核心数据表和基础前台骨架（时间轴、人物列表/详情、留言等扩展表待完善），可通过执行最新迁移创建表结构：
  <code>php yii migrate</code>。前台可直接访问 <code>/timeline</code> 查看时间轴、<code>/person</code> 查看人物列表。
</div>

<h4>下一步建议</h4>
<ul>
  <li>root/member 在后台补充事件/人物数据，完善阶段与关联。</li>
  <li>为 war_message 等表补充审核/展示的管理页。</li>
  <li>按设计文档继续扩充标签、媒资、访问统计的可视化。</li>
</ul>
