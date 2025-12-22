<?php
/**
 * WarEvent Workspace v3
 * - 单页：查看/编辑页内切换
 * - 主内容全宽单列
 * - 关联/媒资/元信息放入右侧抽屉 Drawer（需要时打开）
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
$subText = $isCreate ? '填写基础信息后创建，创建成功后可继续维护人物关联与媒资。' : '同页切换查看/编辑；关联与媒资在右侧面板维护。';
?>

<div class="we3">
  <!-- Sticky Action Bar -->
  <div class="we3-bar">
    <div class="we3-bar-left">
      <div class="we3-h1"><?= Html::encode($titleText) ?></div>
      <div class="we3-sub"><?= Html::encode($subText) ?></div>
    </div>

    <div class="we3-bar-right">
      <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default we3-btn']) ?>

      <?php if (!$isCreate): ?>
        <?= Html::button('人物关联与媒资', ['class' => 'btn btn-primary we3-btn', 'id' => 'we3-open-drawer']) ?>

        <?= Html::a($model->status ? '下线' : '发布', ['toggle-status', 'id' => $model->id], [
          'class' => 'btn ' . ($model->status ? 'btn-warning' : 'btn-success') . ' we3-btn',
          'data-method' => 'post',
          'data-pjax' => 0,
        ]) ?>

        <?= Html::a('删除', ['delete', 'id' => $model->id], [
          'class' => 'btn btn-danger we3-btn',
          'data' => ['confirm' => '确认删除该事件？', 'method' => 'post'],
          'data-pjax' => 0,
        ]) ?>
      <?php endif; ?>

      <?php if (!$isCreate): ?>
        <?= Html::button('编辑', ['class' => 'btn btn-primary we3-btn', 'id' => 'we3-toggle-edit']) ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Content -->
  <div class="we3-canvas <?= $initialEdit ? 'is-edit' : 'is-view' ?>" id="we3-canvas">

    <?php $form = ActiveForm::begin([
      'id' => 'we3-form',
      'options' => ['class' => 'we3-form'],
    ]); ?>

    <!-- Top info strip -->
    <div class="we3-strip">
      <?php if (!$isCreate): ?>
        <span class="we3-chip we3-chip-light">ID：<?= (int)$model->id ?></span>
        <?= $model->status
          ? '<span class="we3-chip we3-chip-green">发布</span>'
          : '<span class="we3-chip we3-chip-gray">草稿</span>' ?>
        <span class="we3-chip we3-chip-muted">创建：<?= $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : '-' ?></span>
        <span class="we3-chip we3-chip-muted">更新：<?= $model->updated_at ? Yii::$app->formatter->asDatetime($model->updated_at) : '-' ?></span>
      <?php else: ?>
        <span class="we3-chip we3-chip-light">新建中</span>
      <?php endif; ?>
    </div>

    <!-- Main section (single column) -->
    <div class="we3-section">
      <div class="we3-sec-hd">
        <div class="we3-sec-title">基础信息</div>
        <div class="we3-sec-desc">标题、日期、阶段、地点、摘要与详情。</div>
      </div>

      <div class="we3-sec-bd">
        <div class="we3-grid">
          <div class="we3-field">
            <div class="we3-label">标题</div>
            <div class="we3-editable">
              <?= $form->field($model, 'title', ['options' => ['class' => 'we3-field-inner']])
                ->textInput(['maxlength' => true, 'placeholder' => '事件标题（必填）'])
                ->label(false) ?>
            </div>
            <div class="we3-readonly"><?= Html::encode($model->title) ?></div>
          </div>

          <div class="we3-field">
            <div class="we3-label">日期</div>
            <div class="we3-editable">
              <?= $form->field($model, 'event_date', ['options' => ['class' => 'we3-field-inner']])
                ->input('date')
                ->label(false) ?>
            </div>
            <div class="we3-readonly"><?= Html::encode($model->event_date) ?></div>
          </div>

          <div class="we3-field">
            <div class="we3-label">阶段</div>
            <div class="we3-editable">
              <?= $form->field($model, 'stage_id', ['options' => ['class' => 'we3-field-inner']])
                ->dropDownList($stageList, ['prompt' => '请选择阶段'])
                ->label(false) ?>
            </div>
            <div class="we3-readonly"><?= Html::encode($model->stage->name ?? '') ?></div>
          </div>

          <div class="we3-field">
            <div class="we3-label">地点</div>
            <div class="we3-editable">
              <?= $form->field($model, 'location', ['options' => ['class' => 'we3-field-inner']])
                ->textInput(['maxlength' => true, 'placeholder' => '发生地点'])
                ->label(false) ?>
            </div>
            <div class="we3-readonly"><?= Html::encode($model->location) ?></div>
          </div>

          <div class="we3-field we3-span-2">
            <div class="we3-label">摘要</div>
            <div class="we3-editable">
              <?= $form->field($model, 'summary', ['options' => ['class' => 'we3-field-inner']])
                ->textarea(['rows' => 2, 'placeholder' => '1-2 句话概括重点'])
                ->label(false) ?>
            </div>
            <div class="we3-readonly we3-pre"><?= Html::encode($model->summary) ?></div>
          </div>

          <div class="we3-field we3-span-2">
            <div class="we3-label">详情</div>
            <div class="we3-editable">
              <?= $form->field($model, 'content', ['options' => ['class' => 'we3-field-inner']])
                ->textarea(['rows' => 12, 'placeholder' => '可分段，支持较长文本'])
                ->label(false) ?>
            </div>
            <div class="we3-readonly we3-pre"><?= Html::encode($model->content) ?></div>
          </div>

          <div class="we3-field we3-span-2">
            <div class="we3-label">状态</div>
            <div class="we3-editable">
              <?= $form->field($model, 'status', ['options' => ['class' => 'we3-field-inner']])
                ->dropDownList([1 => '发布', 0 => '草稿'], ['prompt' => '请选择'])
                ->label(false) ?>
            </div>
            <div class="we3-readonly">
              <?= $model->status ? '<span class="we3-chip we3-chip-green">发布</span>' : '<span class="we3-chip we3-chip-gray">草稿</span>' ?>
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
          'class' => 'btn ' . ($isCreate ? 'btn-success' : 'btn-primary') . ' we3-btn we3-btn-strong',
          'id' => 'we3-submit',
        ]) ?>

        <?php if (!$isCreate): ?>
          <?= Html::button('取消编辑', ['class' => 'btn btn-default we3-btn', 'id' => 'we3-cancel-edit']) ?>
        <?php else: ?>
          <?= Html::a('取消', ['index'], ['class' => 'btn btn-default we3-btn']) ?>
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
          <div class="we3-drawer-title">资源与关联</div>
          <div class="we3-drawer-sub">人物关联、媒资、元信息集中维护。</div>
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
                    <?= Html::beginForm(['detach-person', 'id' => $model->id], 'post') .
                        Html::hiddenInput('person_id', $person->id) .
                        Html::submitButton('移除', ['class' => 'btn btn-xs btn-link text-danger', 'style' => 'padding:0;']) .
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
                <?php $pf = ActiveForm::begin(['action' => ['attach-person', 'id' => $model->id]]); ?>
                  <?= $pf->field($relationForm, 'person_id')->dropDownList($personOptions, ['prompt' => '选择人物'])->label('绑定人物') ?>
                  <?= $pf->field($relationForm, 'relation_type')->textInput(['placeholder' => '关系（可选）'])->label('关系') ?>
                  <?= Html::submitButton('绑定', ['class' => 'btn btn-success we3-btn']) ?>
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
              <div class="we3-mini">图片</div>
              <button type="button" class="btn btn-xs btn-primary we3-btn we3-editable-inline" id="we3-upload-btn">上传</button>
              <?php if (!$isCreate): ?>
                <?= Html::beginForm(['upload-media', 'id' => $model->id], 'post', [
                    'enctype' => 'multipart/form-data',
                    'id' => 'we3-upload-form',
                    'style' => 'display:none;',
                ]) ?>
                  <input type="file" name="file" id="we3-upload-input" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
                <?= Html::endForm() ?>
              <?php endif; ?>
            </div>

            <?php foreach ($imageList as $m): ?>
              <?php $url = '/' . ltrim($m->path, '/'); ?>
              <div class="we3-file">
                <div class="we3-file-main">
                  <div class="we3-file-title"><?= Html::encode($m->title ?: '未命名') ?></div>
                  <div class="we3-file-sub"><?= Html::a(Html::encode($m->path), $url, ['target' => '_blank']) ?></div>
                </div>
                <div class="we3-file-op we3-editable-inline">
                  <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post') .
                      Html::hiddenInput('media_id', $m->id) .
                      Html::submitButton('删除', ['class' => 'btn btn-xs btn-link text-danger', 'style' => 'padding:0;']) .
                      Html::endForm(); ?>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($imageList)): ?><div class="we3-empty">暂无图片</div><?php endif; ?>

            <div class="we3-split"></div>

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
                  <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post') .
                      Html::hiddenInput('media_id', $m->id) .
                      Html::submitButton('删除', ['class' => 'btn btn-xs btn-link text-danger', 'style' => 'padding:0;']) .
                      Html::endForm(); ?>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($docList)): ?><div class="we3-empty">暂无文档</div><?php endif; ?>

            <div class="we3-split"></div>

            <div class="we3-editable-inline">
              <?php if ($mediaForm): ?>
                <div class="we3-hint" style="margin-bottom:8px;">上传后会回填路径与类型，可改标题再“添加媒资”。</div>
                <?php $mf = ActiveForm::begin(['action' => ['add-media', 'id' => $model->id]]); ?>
                  <?= $mf->field($mediaForm, 'title')->textInput(['maxlength' => true])->label('标题') ?>
                  <?= $mf->field($mediaForm, 'type')->dropDownList(['image' => '图片', 'document' => '文档'])->label('类型') ?>
                  <?= $mf->field($mediaForm, 'path')->textInput(['readonly' => true, 'placeholder' => '通过上传自动填充'])->label('路径') ?>
                  <?= Html::submitButton('添加媒资', ['class' => 'btn btn-success we3-btn']) ?>
                <?php ActiveForm::end(); ?>
              <?php else: ?>
                <div class="we3-empty">编辑页可维护媒资</div>
              <?php endif; ?>
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

    // 控制表单可编辑：只禁用基础信息区域的 input/select/textarea
    var inputs = form ? form.querySelectorAll('input,select,textarea') : [];
    inputs.forEach(function(el){
      // drawer 内的表单不受此控制（attach-person/add-media/upload 都在 drawer，且自身会提交）
      if (el.closest('.we3-drawer')) return;
      // CSRF hidden / submit 不动
      if (el.type === 'hidden' || el.type === 'submit') return;
      el.disabled = !on;
      el.readOnly = !on && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA');
    });

    // drawer 内的“编辑专属操作”也跟随可见性
    var editableInline = document.querySelectorAll('.we3-editable-inline');
    editableInline.forEach(function(el){
      el.style.display = on ? '' : 'none';
    });

    if(saveBar){
      saveBar.style.display = on ? 'flex' : 'none';
    }
    if(toggleBtn){
      toggleBtn.textContent = on ? '退出编辑' : '编辑';
      toggleBtn.classList.toggle('btn-default', on);
      toggleBtn.classList.toggle('btn-primary', !on);
    }
  }

  // 初始：如果是 edit/create 则编辑态，否则查看态
  setEdit(canvas && canvas.classList.contains('is-edit'));

  if(toggleBtn){
    toggleBtn.addEventListener('click', function(){
      var on = !canvas.classList.contains('is-edit');
      setEdit(on);
    });
  }
  if(cancelBtn){
    cancelBtn.addEventListener('click', function(){
      // 取消编辑：回到查看态（不自动刷新；用户如需撤销可手动刷新）
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

  // ==========================
  // Keep UI state across reload (drawer + scroll)
  // ==========================
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
    // 用完即清（避免之后每次都强制打开）
    try { sessionStorage.removeItem(stateKey); } catch(e){}

    if(!st) return;

    // 10 秒内的状态才恢复（防止用户隔很久返回）
    if(st.t && (Date.now() - st.t) > 10000) return;

    // 恢复 Drawer
    if(st.drawerOpen){
      drawerOpen();
    }

    // 恢复滚动位置（稍微延迟，确保布局稳定）
    if(typeof st.scrollY === 'number'){
      setTimeout(function(){
        window.scrollTo(0, st.scrollY);
      }, 30);
    }
  }

  // 在即将离开页面时尽量保存（兼容 data-method=post 的跳转）
  window.addEventListener('beforeunload', function(){
    saveState();
  });

  // 额外：点击任何会触发跳转/提交的元素时也保存一次（更稳）
  document.addEventListener('click', function(e){
    var el = e.target.closest('a,button,input[type=submit]');
    if(!el) return;

    // 常见的会导致刷新：data-method(post)、提交按钮、删除/发布等
    if(el.matches('input[type=submit],button[type=submit]') ||
       el.getAttribute('data-method') ||
       el.classList.contains('we3-keep-state')){
      saveState();
    }
  }, true);

  document.addEventListener('submit', function(e){
    // 任何表单提交都保存
    saveState();
  }, true);

  // 页面加载后恢复
  restoreState();

  // One-click upload: open file picker directly
  var uploadBtn = document.getElementById('we3-upload-btn');
  var uploadInput = document.getElementById('we3-upload-input');
  var uploadForm = document.getElementById('we3-upload-form');

  if(uploadBtn && uploadInput){
    uploadBtn.addEventListener('click', function(){
      // 记住抽屉&滚动（如果你已经加了 saveState，保留即可）
      if(typeof saveState === 'function') saveState();

      uploadInput.click(); // 直接弹出系统文件选择器
    });
  }

  if(uploadInput && uploadForm){
    uploadInput.addEventListener('change', function(){
      if(!uploadInput.files || !uploadInput.files.length) return;

      // 选完自动提交，无需二次弹窗确认
      uploadForm.submit();
    });
  }

})();
JS;

$this->registerJs($js);
?>
