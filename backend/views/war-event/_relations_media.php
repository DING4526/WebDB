<?php

/**
 * 事件关联人物与媒资管理片段
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model common\models\WarEvent */
/* @var $personOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */
/* @var $relationMap array */

$imageList = array_filter($mediaList, fn($m) => $m->type === 'image');
$docList = array_filter($mediaList, fn($m) => $m->type === 'document');
?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">人物关联</div>
            <div class="panel-body">
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
                                <?= Html::beginForm(['detach-person', 'id' => $model->id], 'post', ['class' => 'pull-right']) .
                                    Html::hiddenInput('person_id', $person->id) .
                                    Html::submitButton('移除', ['class' => 'btn btn-xs btn-link text-danger']) .
                                    Html::endForm(); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($model->people)): ?>
                        <div class="list-group-item text-muted">尚未绑定人物</div>
                    <?php endif; ?>
                </div>

                <?php $form = ActiveForm::begin([
                    'action' => ['attach-person', 'id' => $model->id],
                    'options' => ['class' => 'form-inline'],
                ]); ?>
                <?= $form->field($relationForm, 'person_id')->dropDownList($personOptions, ['prompt' => '选择人物'])->label(false) ?>
                <?= $form->field($relationForm, 'relation_type')->textInput(['placeholder' => '关系(可选)'])->label(false) ?>
                <?= Html::submitButton('绑定人物', ['class' => 'btn btn-success']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading d-flex justify-content-between">
                <span>媒资管理</span>
                <?= Html::button('上传文件', [
                    'class' => 'btn btn-xs btn-primary pull-right',
                    'data-toggle' => 'modal',
                    'data-target' => '#uploadMediaModalEvent',
                ]) ?>
            </div>
            <div class="panel-body">
                <h5 class="mt0">图片</h5>
                <table class="table table-condensed">
                    <thead><tr><th>标题</th><th>路径</th><th width="90"></th></tr></thead>
                    <tbody>
                    <?php foreach ($imageList as $media): ?>
                        <tr>
                            <td><?= Html::encode($media->title) ?></td>
                            <?php $url = '/' . ltrim($media->path, '/'); ?>
                            <td><?= Html::a(Html::encode($media->path), $url, ['target' => '_blank']) ?></td>
                            <td>
                                <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post') .
                                    Html::hiddenInput('media_id', $media->id) .
                                    Html::submitButton('删除', ['class' => 'btn btn-xs btn-link text-danger']) .
                                    Html::endForm(); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($imageList)): ?>
                        <tr><td colspan="3" class="text-muted">暂无图片</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <h5>文档</h5>
                <table class="table table-condensed">
                    <thead><tr><th>标题</th><th>路径</th><th width="90"></th></tr></thead>
                    <tbody>
                    <?php foreach ($docList as $media): ?>
                        <tr>
                            <td><?= Html::encode($media->title) ?></td>
                            <?php $url = '/' . ltrim($media->path, '/'); ?>
                            <td><?= Html::a(Html::encode($media->path), $url, ['target' => '_blank']) ?></td>
                            <td>
                                <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post') .
                                    Html::hiddenInput('media_id', $media->id) .
                                    Html::submitButton('删除', ['class' => 'btn btn-xs btn-link text-danger']) .
                                    Html::endForm(); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($docList)): ?>
                        <tr><td colspan="3" class="text-muted">暂无文档</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <?php $mediaFormWidget = ActiveForm::begin([
                    'action' => ['add-media', 'id' => $model->id],
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => ['options' => ['class' => 'form-group']],
                ]); ?>
                <?= $mediaFormWidget->field($mediaForm, 'title')->textInput(['maxlength' => true]) ?>
                <?= $mediaFormWidget->field($mediaForm, 'type')->dropDownList(['image' => '图片', 'document' => '文档']) ?>
                <?= $mediaFormWidget->field($mediaForm, 'path')->textInput(['maxlength' => true, 'readonly' => true, 'placeholder' => '通过上传自动填充']) ?>
                <p class="help-block">上传成功后自动带入路径与类型，可在此调整标题再保存。</p>
                <?= Html::submitButton('添加媒资', ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadMediaModalEvent" tabindex="-1" role="dialog" aria-labelledby="uploadMediaModalEventLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="uploadMediaModalEventLabel">上传文件</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['upload-media', 'id' => $model->id], 'post', ['enctype' => 'multipart/form-data']) ?>
                <div class="form-group">
                    <label>选择文件</label>
                    <?= Html::fileInput('file', null, ['class' => 'form-control']) ?>
                    <p class="help-block">支持图片(jpg/png/webp)或文档(pdf/doc/docx)，大小 ≤ 10MB。上传后自动识别类型并生成路径（基于 uploads/war/events/ 或 uploads/war/docs/）。</p>
                </div>
                <?= Html::submitButton('上传', ['class' => 'btn btn-primary']) ?>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>
