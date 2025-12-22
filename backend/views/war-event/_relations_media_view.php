<?php

/**
 * 事件关联与媒资展示（只读）
 */

use yii\helpers\Html;

/* @var $model common\models\WarEvent */
/* @var $relationMap array */
/* @var $mediaList common\models\WarMedia[] */

$imageList = array_filter($mediaList, fn($m) => $m->type === 'image');
$docList = array_filter($mediaList, fn($m) => $m->type === 'document');
?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">人物关联</div>
            <div class="list-group mb10">
                <?php foreach ($model->people as $person): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?= Html::encode($person->name) ?></strong>
                                <div class="text-muted small">
                                    关系：<?= Html::encode($relationMap[$person->id] ?? '未填写') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($model->people)): ?>
                    <div class="list-group-item text-muted">尚未绑定人物</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">媒资</div>
            <div class="panel-body">
                <h5 class="mt0">图片</h5>
                <table class="table table-condensed">
                    <thead><tr><th>标题</th><th>路径</th></tr></thead>
                    <tbody>
                    <?php foreach ($imageList as $media): ?>
                        <tr>
                            <td><?= Html::encode($media->title) ?></td>
                            <?php $url = '/' . ltrim($media->path, '/'); ?>
                            <td><?= Html::a(Html::encode($media->path), $url, ['target' => '_blank']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($imageList)): ?>
                        <tr><td colspan="2" class="text-muted">暂无图片</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <h5>文档</h5>
                <table class="table table-condensed">
                    <thead><tr><th>标题</th><th>路径</th></tr></thead>
                    <tbody>
                    <?php foreach ($docList as $media): ?>
                        <tr>
                            <td><?= Html::encode($media->title) ?></td>
                            <?php $url = '/' . ltrim($media->path, '/'); ?>
                            <td><?= Html::a(Html::encode($media->path), $url, ['target' => '_blank']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($docList)): ?>
                        <tr><td colspan="2" class="text-muted">暂无文档</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
