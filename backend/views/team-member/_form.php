<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Team;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */
/* @var $form yii\widgets\ActiveForm */

// 获取当前团队（如果是新建，从teamProvider获取）
$teamId = $model->team_id ?: (Yii::$app->teamProvider ? Yii::$app->teamProvider->getId() : null);
$teamName = '未指定';
if ($teamId) {
    $team = Team::findOne($teamId);
    if ($team) {
        $teamName = $team->name;
    }
}


$this->registerCssFile('@web/css/team-workspace.css');
?>

<div class="team-member-form team-ws-form-wrap">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'team-ws-form']]); ?>

    <div class="team-ws-form-section">
      <div class="team-ws-form-section-title">基础信息</div>

      <!-- 团队（只读显示） -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">所属团队</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'team_id')->hiddenInput()->label(false) ?>
          <div class="team-ws-readonly-value">
            <?= Html::encode($teamName) ?>
          </div>
        </div>
      </div>
            
      <!-- 选择账号（User） -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">选择账号</div>
        <div class="team-ws-control">
          <?php
            $activeUsers = \common\models\User::find()->where(['status' => \common\models\User::STATUS_ACTIVE])->all();
            $userItems = ArrayHelper::map($activeUsers, 'id', function($u) {
                $name = ArrayHelper::getValue($u->getAttributes(), 'name');
                $student = ArrayHelper::getValue($u->getAttributes(), 'student_no');
                $label = $u->username;
                if ($name || $student) {
                    $label .= '（' . ($name ?: '-') . ' / ' . ($student ?: '-') . '）';
                }
                return $label;
            });
            $optionAttrs = [];
            foreach ($activeUsers as $u) {
                $attrs = $u->getAttributes();
                $optionAttrs[$u->id] = [
                    'data-name' => ArrayHelper::getValue($attrs, 'name'),
                    'data-student' => ArrayHelper::getValue($attrs, 'student_no'),
                    'data-username' => $u->username,
                ];
            }
            echo $form->field($model, 'user_id', ['options' => ['class' => 'team-ws-field']])
              ->dropDownList($userItems, [
                  'prompt' => '请选择一个账号',
                  'options' => $optionAttrs,
                  'class' => 'form-control',
                  'id' => 'team-member-user-select',
              ])->label(false);
          ?>
        </div>
      </div>

      <!-- 姓名 -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">姓名</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'name', ['options' => ['class' => 'team-ws-field']])
            ->textInput(['maxlength' => true, 'placeholder' => '将自动填充所选账号姓名', 'readonly' => true, 'id' => 'team-member-name'])
            ->label(false) ?>
        </div>
      </div>

      <!-- 角色 -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">角色</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'role', ['options' => ['class' => 'team-ws-field']])
            ->textInput(['maxlength' => true, 'placeholder' => '如：组长、开发、测试等'])
            ->label(false) ?>
        </div>
      </div>

    <?php
      $js = <<<JS
      (function(){
        var sel = document.getElementById('team-member-user-select');
        if (!sel) return;
        function fillBySelect(){
          var opt = sel.options[sel.selectedIndex];
          if (!opt || !opt.getAttribute) return;
          var name = opt.getAttribute('data-name');
          var student = opt.getAttribute('data-student');
          var username = opt.getAttribute('data-username');
          var nameInput = document.getElementById('team-member-name');
          var stuInput = document.getElementById('team-member-student');
          if (nameInput) nameInput.value = name || username || '';
          if (stuInput) stuInput.value = student || '';
        }
        sel.addEventListener('change', fillBySelect);
        // 初始化时尝试填充
        fillBySelect();
      })();
      JS;
      $this->registerJs($js);
    ?>
      <!-- 工作范围 -->
      <div class="team-ws-fieldline team-ws-fieldline-full">
        <div class="team-ws-label">工作范围</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'work_scope', ['options' => ['class' => 'team-ws-field']])
            ->textarea(['rows' => 4, 'placeholder' => '描述成员负责的工作内容'])
            ->label(false) ?>
        </div>
      </div>

      <!-- 状态 -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">状态</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'status', ['options' => ['class' => 'team-ws-field']])
            ->dropDownList($model::getStatusList())
            ->label(false) ?>
        </div>
      </div>

    </div>

    <div class="team-ws-form-footer">
      <div class="team-ws-footer-hint">
        <span class="team-ws-icon">💡</span>
        <span>保存后立即生效</span>
      </div>
      <div class="team-ws-footer-actions">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success team-ws-btn-save']) ?>
        <?= Html::a('取消', ['/team/index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>

    <?php ActiveForm::end(); ?>

