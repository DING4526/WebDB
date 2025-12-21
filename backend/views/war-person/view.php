<?php

/**
 * Ding 2310724
 * 抗战人物详情
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web.View */
/* @var $model common\models\WarPerson */
/* @var $eventOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '抗战人物管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-person-view">

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该人物？',
                'method' => 'post',
            ],
        ]) ?>
        <?php if ($model->status === 0): ?>
            <?= Html::a('发布', ['publish', 'id' => $model->id], ['class' => 'btn btn-success', 'data-method' => 'post']) ?>
        <?php else: ?>
            <?= Html::a('下线', ['offline', 'id' => $model->id], ['class' => 'btn btn-warning', 'data-method' => 'post']) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'role_type',
            'birth_year',
            'death_year',
            'intro:ntext',
            'biography:ntext',
            [
                'attribute' => 'status',
                'value' => $model->status ? '展示' : '隐藏',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">关联事件</div>
                <div class="panel-body">
                    <ul class="list-group mb10">
                        <?php foreach ($model->events as $event): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= Html::encode($event->title) ?></span>
                                <?= Html::beginForm(['detach-event', 'id' => $model->id], 'post', ['class' => 'pull-right']) .
                                    Html::hiddenInput('event_id', $event->id) .
                                    Html::submitButton('移除', ['class' => 'btn btn-xs btn-link text-danger']) .
                                    Html::endForm(); ?>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($model->events)): ?>
                            <li class="list-group-item text-muted">尚未绑定事件</li>
                        <?php endif; ?>
                    </ul>

                    <?php $form = ActiveForm::begin([
                        'action' => ['attach-event', 'id' => $model->id],
                        'options' => ['class' => 'form-inline'],
                    ]); ?>
                    <?= $form->field($relationForm, 'event_id')->dropDownList($eventOptions, ['prompt' => '选择事件'])->label(false) ?>
                    <?= $form->field($relationForm, 'relation_type')->textInput(['placeholder' => '关系(可选)'])->label(false) ?>
                    <?= Html::submitButton('绑定事件', ['class' => 'btn btn-success']) ?>
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
                        'data-target' => '#uploadMediaModalPerson',
                    ]) ?>
                </div>
                <div class="panel-body">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th>标题</th><th>类型</th><th>路径</th><th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($mediaList as $media): ?>
                            <tr>
                                <td><?= Html::encode($media->title) ?></td>
                                <td><?= Html::encode($media->type) ?></td>
                                <td><?= Html::a(Html::encode($media->path), $media->path, ['target' => '_blank']) ?></td>
                                <td>
                                    <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post') .
                                        Html::hiddenInput('media_id', $media->id) .
                                        Html::submitButton('删除', ['class' => 'btn btn-xs btn-link text-danger']) .
                                        Html::endForm(); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($mediaList)): ?>
                            <tr><td colspan="4" class="text-muted">暂无媒资</td></tr>
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
                    <?= $mediaFormWidget->field($mediaForm, 'path')->textInput(['maxlength' => true]) ?>
                    <?= Html::submitButton('添加媒资', ['class' => 'btn btn-primary']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadMediaModalPerson" tabindex="-1" role="dialog" aria-labelledby="uploadMediaModalPersonLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="uploadMediaModalPersonLabel">上传文件</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['upload-media', 'id' => $model->id], 'post', ['enctype' => 'multipart/form-data']) ?>
                    <div class="form-group">
                        <label>标题（可选）</label>
                        <?= Html::textInput('title', null, ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group">
                        <label>类型</label>
                        <?= Html::dropDownList('type', 'image', ['image' => '图片', 'document' => '文档'], ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group">
                        <label>选择文件</label>
                        <?= Html::fileInput('file', null, ['class' => 'form-control']) ?>
                        <p class="help-block">支持图片(jpg/png/webp)或文档(pdf/doc/docx)，大小 ≤ 10MB。</p>
                    </div>
                    <?= Html::submitButton('上传', ['class' => 'btn btn-primary']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
</div>
