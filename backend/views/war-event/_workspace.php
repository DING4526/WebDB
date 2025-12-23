<?php
/**
 * WarEvent Workspace v4 (best-looking, aligned with index)
 * - 主内容限宽居中，表单更规整
 * - 两列栅格 + 横向标签对齐（左标签右控件）
 * - 右侧 Drawer 更宽、更紧凑、表单更合理
 *
 * @var $this yii\web\View
 * @var $mode string view|edit|create
 * @var $model common\models\WarEvent
 * @var $stageList array|null
 * @var $personOptions array|null
 * @var $relationForm common\models\WarEventPerson|null
 * @var $mediaForm common\models\WarMedia|null
 * @var $mediaList array
 * @var $relationMap array
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$mode = $mode ?? 'view';
$isCreate = ($mode === 'create');
$initialEdit = in_array($mode, ['edit', 'create'], true);

$stageList = $stageList ?? [];
$personOptions = $personOptions ?? [];
$mediaList = $mediaList ?? [];
$relationMap = $relationMap ?? [];

$imageList = array_filter($mediaList, fn($m) => $m->type === 'image');
$docList   = array_filter($mediaList, fn($m) => $m->type === 'document');

$titleText = $isCreate ? '新增事件' : $model->title;
$subText = $isCreate
  ? '填写基础信息后创建，创建成功后可继续维护人物关联与媒资。'
  : '同页切换查看/编辑；人物关联与媒资在右侧面板维护。';
?>

<div class="we3 we3-wrap">

  <!-- Top: Header Card (aligned with index card style) -->
  <div class="we3-headcard">
    <div class="we3-head-left">
      <div class="we3-title"><?= Html::encode($titleText) ?></div>
      <div class="we3-subtitle"><?= Html::encode($subText) ?></div>

      <div class="we3-meta">
        <?php if (!$isCreate): ?>
          <span class="we3-chip we3-chip-light">ID：<?= (int)$model->id ?></span>
          <?= $model->status
            ? '<span class="we3-chip we3-chip-green">已发布</span>'
            : '<span class="we3-chip we3-chip-gray">未发布</span>' ?>
          <span class="we3-chip we3-chip-muted">创建：<?= $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : '-' ?></span>
          <span class="we3-chip we3-chip-muted">更新：<?= $model->updated_at ? Yii::$app->formatter->asDatetime($model->updated_at) : '-' ?></span>
        <?php else: ?>
          <span class="we3-chip we3-chip-light">新建中</span>
        <?php endif; ?>
      </div>
    </div>

    <div class="we3-head-right">
      <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-ghost we3-btn']) ?>

      <?php if (!$isCreate): ?>
        <?= Html::button('人物关联与媒资', ['class' => 'btn btn-soft-primary we3-btn', 'id' => 'we3-open-drawer']) ?>

        <?= Html::button($initialEdit ? '退出编辑' : '进入编辑', [
          'class' => 'btn ' . ($initialEdit ? 'btn-soft-warning' : 'btn-soft-success') . ' we3-btn',
          'id' => 'we3-toggle-edit',
        ]) ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Content Card -->
  <div class="we3-canvas <?= $initialEdit ? 'is-edit' : 'is-view' ?>" id="we3-canvas">

    <?php $form = ActiveForm::begin([
      'id' => 'we3-form',
      'options' => ['class' => 'we3-form'],
    ]); ?>

    <div class="we3-body">

      <!-- Section: Basic -->
      <div class="we3-card">
        <div class="we3-card-hd">
          <div>
            <div class="we3-card-title">基础信息</div>
            <div class="we3-card-desc">标题、日期、阶段、地点、摘要与详情。</div>
          </div>
          <div class="we3-card-side">
            <?php if (!$isCreate): ?>
              <span class="we3-badge"><?= $model->status ? 'Published' : 'Draft' ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="we3-card-bd">
          <div class="we3-grid2">

            <!-- 标题 -->
            <div class="we3-fieldline">
              <div class="we3-lab">标题</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'title', ['options' => ['class' => 'we3-field-inner']])
                  ->textInput(['maxlength' => true, 'placeholder' => '事件标题（必填）'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->title) ?></div>
            </div>

            <!-- 日期 -->
            <div class="we3-fieldline">
              <div class="we3-lab">日期</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'event_date', ['options' => ['class' => 'we3-field-inner']])
                  ->input('date')
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->event_date) ?></div>
            </div>

            <!-- 阶段 -->
            <div class="we3-fieldline">
              <div class="we3-lab">阶段</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'stage_id', ['options' => ['class' => 'we3-field-inner']])
                  ->dropDownList($stageList, ['prompt' => '请选择阶段'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->stage->name ?? '') ?></div>
            </div>

            <!-- 地点 -->
            <div class="we3-fieldline">
              <div class="we3-lab">地点</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'location', ['options' => ['class' => 'we3-field-inner']])
                  ->textInput(['maxlength' => true, 'placeholder' => '发生地点'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->location) ?></div>
            </div>

            <!-- 摘要（跨整行） -->
            <div class="we3-fieldline we3-spanfull">
              <div class="we3-lab">摘要</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'summary', ['options' => ['class' => 'we3-field-inner']])
                  ->textarea(['rows' => 3, 'placeholder' => '1-2 句话概括重点'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly we3-pre"><?= Html::encode($model->summary) ?></div>
            </div>

            <!-- 详情（跨整行） -->
            <div class="we3-fieldline we3-spanfull">
              <div class="we3-lab">详情</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'content', ['options' => ['class' => 'we3-field-inner']])
                  ->textarea(['rows' => 8, 'placeholder' => '可分段，支持较长文本'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly we3-pre"><?= Html::encode($model->content) ?></div>
            </div>

          </div>
        </div>
      </div>

    </div>

    <!-- Save Footer (only in edit/create) -->
    <div class="we3-save" id="we3-savebar">
      <div class="we3-save-left">
        <span class="we3-hint">提示：离开页面前请保存；未保存更改会提醒。</span>
      </div>
      <div class="we3-save-right">
        <?= Html::submitButton($isCreate ? '创建' : '保存更新', [
          'class' => 'btn ' . ($isCreate ? 'btn-soft-success' : 'btn-soft-primary') . ' we3-btn we3-btn-strong',
          'id' => 'we3-submit',
        ]) ?>

        <?php if (!$isCreate): ?>
          <?= Html::button('取消编辑', ['class' => 'btn btn-ghost we3-btn', 'id' => 'we3-cancel-edit']) ?>
        <?php else: ?>
          <?= Html::a('取消', ['index'], ['class' => 'btn btn-ghost we3-btn']) ?>
        <?php endif; ?>
      </div>
    </div>

    <?php ActiveForm::end(); ?>
  </div>

  <!-- Drawer Overlay -->
  <?php if (!$isCreate): ?>
    <div class="we3-overlay" id="we3-overlay"></div>

    <!-- Drawer -->
    <aside class="we3-drawer" id="we3-drawer" aria-hidden="true">
      <div class="we3-drawer-hd">
        <div>
          <div class="we3-drawer-title">人物关联与媒资</div>
          <div class="we3-drawer-sub">关联、上传、删除都在这里完成。</div>
        </div>
        <button type="button" class="we3-iconbtn" id="we3-close-drawer" aria-label="关闭">×</button>
      </div>

      <div class="we3-drawer-bd">

        <!-- People -->
        <div class="we3-panel">
          <div class="we3-panel-hd">
            <div class="we3-panel-title">人物关联</div>
            <div class="we3-panel-meta">已绑定：<?= count($model->people ?? []) ?></div>
          </div>

          <div class="we3-panel-bd">
            <div class="we3-list">
              <?php foreach (($model->people ?? []) as $person): ?>
                <div class="we3-item">
                  <div class="we3-item-main">
                    <div class="we3-item-title"><?= Html::encode($person->name) ?></div>
                    <div class="we3-item-sub">关系：<?= Html::encode($relationMap[$person->id] ?? '未填写') ?></div>
                  </div>

                  <div class="we3-item-op we3-editable-inline">
                    <?= Html::beginForm(['detach-person', 'id' => $model->id], 'post', ['class' => 'we3-miniop']) .
                        Html::hiddenInput('person_id', $person->id) .
                        Html::submitButton('移除', ['class' => 'btn btn-xs btn-soft-danger']) .
                        Html::endForm(); ?>
                  </div>
                </div>
              <?php endforeach; ?>
              <?php if (empty($model->people)): ?>
                <div class="we3-empty">暂无关联人物</div>
              <?php endif; ?>
            </div>

            <div class="we3-split"></div>

            <div class="we3-editable-inline">
              <?php if ($relationForm): ?>
                <?php $pf = ActiveForm::begin(['action' => ['attach-person', 'id' => $model->id], 'options' => ['class' => 'we3-miniForm']]); ?>

                  <div class="we3-miniGrid">
                    <div class="we3-miniCol">
                      <?= $pf->field($relationForm, 'person_id')
                        ->dropDownList($personOptions, ['prompt' => '选择人物'])
                        ->label('人物') ?>
                    </div>
                    <div class="we3-miniCol">
                      <?= $pf->field($relationForm, 'relation_type')
                        ->textInput(['placeholder' => '如：指挥/参与/受害/相关'])
                        ->label('关系（可选）') ?>
                    </div>
                    <div class="we3-miniCol we3-miniColBtn">
                      <?= Html::submitButton('绑定', ['class' => 'btn btn-soft-success we3-btn', 'style' => 'width:100%;']) ?>
                    </div>
                  </div>

                <?php ActiveForm::end(); ?>
              <?php else: ?>
                <div class="we3-empty">编辑页可维护人物关联</div>
              <?php endif; ?>
            </div>

          </div>
        </div>

        <!-- Media -->
        <div class="we3-panel">
          <div class="we3-panel-hd">
            <div class="we3-panel-title">媒资</div>
            <div class="we3-panel-meta">图片 <?= count($imageList) ?> · 文档 <?= count($docList) ?></div>
          </div>

          <div class="we3-panel-bd">

            <div class="we3-minihead">
              <div class="we3-mini">上传文件</div>
              <button type="button" class="btn btn-xs btn-soft-primary we3-editable-inline" id="we3-upload-btn">选择文件</button>
              <?= Html::beginForm(['upload-media', 'id' => $model->id], 'post', [
                'enctype' => 'multipart/form-data',
                'id' => 'we3-upload-form',
                'style' => 'display:none;',
              ]) ?>
                <input type="file" name="file" id="we3-upload-input" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
              <?= Html::endForm() ?>
            </div>

            <div class="we3-note">上传后会自动回填到“添加媒资”表单，可改标题再保存。</div>

            <div class="we3-editable-inline">
              <?php if ($mediaForm): ?>
                <?php $mf = ActiveForm::begin(['action' => ['add-media', 'id' => $model->id], 'options' => ['class' => 'we3-miniForm']]); ?>

                  <div class="we3-miniGrid">
                    <div class="we3-miniCol">
                      <?= $mf->field($mediaForm, 'title')->textInput(['maxlength' => true])->label('标题') ?>
                    </div>
                    <div class="we3-miniCol">
                      <?= $mf->field($mediaForm, 'type')->dropDownList(['image' => '图片', 'document' => '文档'])->label('类型') ?>
                    </div>
                    <div class="we3-miniCol">
                      <?= $mf->field($mediaForm, 'path')->textInput(['readonly' => true, 'placeholder' => '上传后自动填充'])->label('路径') ?>
                    </div>
                    <div class="we3-miniCol we3-miniColBtn">
                      <?= Html::submitButton('添加媒资', ['class' => 'btn btn-soft-success we3-btn', 'style' => 'width:100%;']) ?>
                    </div>
                  </div>

                <?php ActiveForm::end(); ?>
              <?php else: ?>
                <div class="we3-empty">编辑页可维护媒资</div>
              <?php endif; ?>
            </div>

            <div class="we3-split"></div>

            <div class="we3-twoCols">
              <!-- Images -->
              <div>
                <div class="we3-minihead">
                  <div class="we3-mini">图片</div>
                </div>

                <?php foreach ($imageList as $m): ?>
                  <?php $url = '/' . ltrim($m->path, '/'); ?>
                  <div class="we3-file">
                    <div class="we3-file-main">
                      <div class="we3-file-title"><?= Html::encode($m->title ?: '未命名') ?></div>
                      <div class="we3-file-sub"><?= Html::a(Html::encode($m->path), $url, ['target' => '_blank']) ?></div>
                    </div>
                    <div class="we3-file-op we3-editable-inline">
                      <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post', ['class' => 'we3-miniop']) .
                          Html::hiddenInput('media_id', $m->id) .
                          Html::submitButton('删除', ['class' => 'btn btn-xs btn-soft-danger']) .
                          Html::endForm(); ?>
                    </div>
                  </div>
                <?php endforeach; ?>
                <?php if (empty($imageList)): ?><div class="we3-empty">暂无图片</div><?php endif; ?>
              </div>

              <!-- Docs -->
              <div>
                <div class="we3-minihead">
                  <div class="we3-mini">文档</div>
                </div>

                <?php foreach ($docList as $m): ?>
                  <?php $url = '/' . ltrim($m->path, '/'); ?>
                  <div class="we3-file">
                    <div class="we3-file-main">
                      <div class="we3-file-title"><?= Html::encode($m->title ?: '未命名') ?></div>
                      <div class="we3-file-sub"><?= Html::a(Html::encode($m->path), $url, ['target' => '_blank']) ?></div>
                    </div>
                    <div class="we3-file-op we3-editable-inline">
                      <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post', ['class' => 'we3-miniop']) .
                          Html::hiddenInput('media_id', $m->id) .
                          Html::submitButton('删除', ['class' => 'btn btn-xs btn-soft-danger']) .
                          Html::endForm(); ?>
                    </div>
                  </div>
                <?php endforeach; ?>
                <?php if (empty($docList)): ?><div class="we3-empty">暂无文档</div><?php endif; ?>
              </div>
            </div>

          </div>
        </div>

      </div>
    </aside>
  <?php endif; ?>

</div>

<?php
$eventId = $isCreate ? 'null' : (int)$model->id;

$js = <<<JS
(function(){
  var canvas = document.getElementById('we3-canvas');
  var form = document.getElementById('we3-form');
  var toggleBtn = document.getElementById('we3-toggle-edit');
  var cancelBtn = document.getElementById('we3-cancel-edit');
  var saveBar = document.getElementById('we3-savebar');

  // Drawer
  var openDrawer = document.getElementById('we3-open-drawer');
  var closeDrawer = document.getElementById('we3-close-drawer');
  var drawer = document.getElementById('we3-drawer');
  var overlay = document.getElementById('we3-overlay');

  function setEdit(on){
    if(!canvas) return;
    canvas.classList.toggle('is-edit', on);
    canvas.classList.toggle('is-view', !on);

    // 控制主表单可编辑：只禁用基础信息区域的 input/select/textarea
    var inputs = form ? form.querySelectorAll('input,select,textarea') : [];
    inputs.forEach(function(el){
      if (el.closest('.we3-drawer')) return; // drawer 内不受控
      if (el.type === 'hidden' || el.type === 'submit') return;
      el.disabled = !on;
      el.readOnly = !on && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA');
    });

    // drawer 内编辑专属
    var editableInline = document.querySelectorAll('.we3-editable-inline');
    editableInline.forEach(function(el){
      el.style.display = on ? '' : 'none';
    });

    if(saveBar){
      saveBar.style.display = on ? 'flex' : 'none';
    }
    if(toggleBtn){
      toggleBtn.textContent = on ? '退出编辑' : '进入编辑';
      toggleBtn.classList.toggle('btn-soft-warning', on);
      toggleBtn.classList.toggle('btn-soft-success', !on);
    }
  }

  // init
  setEdit(canvas && canvas.classList.contains('is-edit'));

  if(toggleBtn){
    toggleBtn.addEventListener('click', function(){
      var on = !canvas.classList.contains('is-edit');
      setEdit(on);
    });
  }
  if(cancelBtn){
    cancelBtn.addEventListener('click', function(){
      setEdit(false);
    });
  }

  // Dirty prompt（只在编辑态触发）
  var dirty = false;
  function markDirty(){ dirty = true; }
  if(form){
    form.addEventListener('input', function(e){
      if(canvas.classList.contains('is-edit') && !e.target.closest('.we3-drawer')) markDirty();
    }, true);
    form.addEventListener('change', function(e){
      if(canvas.classList.contains('is-edit') && !e.target.closest('.we3-drawer')) markDirty();
    }, true);
  }
  window.addEventListener('beforeunload', function(e){
    if(!dirty) return;
    e.preventDefault();
    e.returnValue = '';
    return '';
  });

  // Drawer control
  function drawerOpen(){
    if(!drawer || !overlay) return;
    drawer.classList.add('is-open');
    overlay.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
  }
  function drawerClose(){
    if(!drawer || !overlay) return;
    drawer.classList.remove('is-open');
    overlay.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
  }
  if(openDrawer) openDrawer.addEventListener('click', drawerOpen);
  if(closeDrawer) closeDrawer.addEventListener('click', drawerClose);
  if(overlay) overlay.addEventListener('click', drawerClose);
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') drawerClose();
  });

  // Keep state across reload
  var eventId = $eventId;
  var stateKey = eventId ? ('we3_state_' + eventId) : null;

  function saveState(){
    if(!stateKey) return;
    var st = {
      t: Date.now(),
      drawerOpen: drawer && drawer.classList.contains('is-open'),
      scrollY: window.scrollY || window.pageYOffset || 0
    };
    try { sessionStorage.setItem(stateKey, JSON.stringify(st)); } catch(e){}
  }

  function restoreState(){
    if(!stateKey) return;
    var raw = null;
    try { raw = sessionStorage.getItem(stateKey); } catch(e){}
    if(!raw) return;

    var st = null;
    try { st = JSON.parse(raw); } catch(e){}
    try { sessionStorage.removeItem(stateKey); } catch(e){}
    if(!st) return;
    if(st.t && (Date.now() - st.t) > 10000) return;

    if(st.drawerOpen){ drawerOpen(); }
    if(typeof st.scrollY === 'number'){
      setTimeout(function(){ window.scrollTo(0, st.scrollY); }, 30);
    }
  }

  window.addEventListener('beforeunload', function(){ saveState(); });

  document.addEventListener('click', function(e){
    var el = e.target.closest('a,button,input[type=submit]');
    if(!el) return;
    if(el.matches('input[type=submit],button[type=submit]') ||
       el.getAttribute('data-method') ||
       el.classList.contains('we3-keep-state')){
      saveState();
    }
  }, true);

  document.addEventListener('submit', function(){ saveState(); }, true);

  restoreState();

  // One-click upload
  var uploadBtn = document.getElementById('we3-upload-btn');
  var uploadInput = document.getElementById('we3-upload-input');
  var uploadForm = document.getElementById('we3-upload-form');

  if(uploadBtn && uploadInput){
    uploadBtn.addEventListener('click', function(){
      if(typeof saveState === 'function') saveState();
      uploadInput.click();
    });
  }
  if(uploadInput && uploadForm){
    uploadInput.addEventListener('change', function(){
      if(!uploadInput.files || !uploadInput.files.length) return;
      uploadForm.submit();
    });
  }

})();
JS;

$this->registerJs($js);
?>
