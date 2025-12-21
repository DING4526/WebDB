<?php

/**
 * Ding 2310724
 * 抗战事件详情
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $personOptions array */
/* @var $relationForm common\models\WarEventPerson */
/* @var $mediaForm common\models\WarMedia */
/* @var $mediaList common\models\WarMedia[] */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-event-view">

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该事件？',
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
            'title',
            'event_date',
            [
                'label' => '阶段',
                'value' => $model->stage->name ?? '',
            ],
            'location',
            'summary:ntext',
            'content:ntext',
            [
                'attribute' => 'status',
                'value' => $model->status ? '发布' : '草稿',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">人物关联</div>
                <div class="panel-body">
                    <ul class="list-group mb10">
                        <?php foreach ($model->people as $person): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= Html::encode($person->name) ?></span>
                                <?= Html::beginForm(['detach-person', 'id' => $model->id], 'post', ['class' => 'pull-right']) .
                                    Html::hiddenInput('person_id', $person->id) .
                                    Html::submitButton('移除', ['class' => 'btn btn-xs btn-link text-danger']) .
                                    Html::endForm(); ?>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($model->people)): ?>
                            <li class="list-group-item text-muted">尚未绑定人物</li>
                        <?php endif; ?>
                    </ul>

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
                <div class="panel-heading">媒资管理</div>
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
</div>
