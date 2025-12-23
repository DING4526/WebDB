<?php
/**
 * WarPerson Workspace（对齐 war-event 样式）
 *
 * @var $this yii\web\View
 * @var $mode string view|edit|create
 * @var $model common\models\WarPerson
 * @var $eventOptions array|null
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

$eventOptions = $eventOptions ?? [];
$mediaList = $mediaList ?? [];
$relationMap = $relationMap ?? [];
$relationForm = $relationForm ?? null;
$mediaForm    = $mediaForm ?? null;

$imageList = array_filter($mediaList, function ($m) { return $m->type === 'image'; });
$docList   = array_filter($mediaList, function ($m) { return $m->type === 'document'; });
$eventCount = count($model->events ?? []);

$titleText = $isCreate ? '新增人物' : $model->name;
$subText = $isCreate
  ? '填写基础信息后创建，创建成功后可继续维护事件关联与媒资。'
  : '同页切换查看/编辑；事件关联与媒资在右侧面板维护。';
?>

<div class="we3 we3-wrap">

  <!-- Top -->
  <div class="we3-headcard">
    <div class="we3-head-left">
      <div class="we3-title"><?= Html::encode($titleText) ?></div>
      <div class="we3-subtitle"><?= Html::encode($subText) ?></div>

      <div class="we3-meta">
        <?php if (!$isCreate): ?>
          <span class="we3-chip we3-chip-light">ID：<?= (int)$model->id ?></span>
          <?= $model->status
            ? '<span class="we3-chip we3-chip-green">展示中</span>'
            : '<span class="we3-chip we3-chip-gray">隐藏</span>' ?>
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
        <?= Html::button('事件关联与媒资', ['class' => 'btn btn-soft-primary we3-btn', 'id' => 'we3-open-drawer']) ?>

        <?= Html::button($initialEdit ? '退出编辑' : '进入编辑', [
          'class' => 'btn ' . ($initialEdit ? 'btn-soft-warning' : 'btn-soft-success') . ' we3-btn',
          'id' => 'we3-toggle-edit',
        ]) ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Content -->
  <div class="we3-canvas <?= $initialEdit ? 'is-edit' : 'is-view' ?>" id="we3-canvas">

    <?php $form = ActiveForm::begin([
      'id' => 'we3-form',
      'options' => ['class' => 'we3-form'],
    ]); ?>

    <div class="we3-body">

      <div class="we3-card">
        <div class="we3-card-hd">
          <div>
            <div class="we3-card-title">基础信息</div>
            <div class="we3-card-desc">姓名、身份、年份、简介与生平。</div>
          </div>
          <div class="we3-card-side">
            <?php if (!$isCreate): ?>
              <span class="we3-badge"><?= $model->status ? '展示中' : '隐藏' ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="we3-card-bd">
          <div class="we3-grid2">

            <!-- 姓名 -->
            <div class="we3-fieldline">
              <div class="we3-lab">姓名</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'name', ['options' => ['class' => 'we3-field-inner']])
                  ->textInput(['maxlength' => true, 'placeholder' => '人物姓名（必填）'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->name) ?></div>
            </div>

            <!-- 身份 -->
            <div class="we3-fieldline">
              <div class="we3-lab">身份</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'role_type', ['options' => ['class' => 'we3-field-inner']])
                  ->textInput(['maxlength' => true, 'placeholder' => '如：将领、指挥官、战士等'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->role_type) ?></div>
            </div>

            <!-- 出生年份 -->
            <div class="we3-fieldline">
              <div class="we3-lab">出生年份</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'birth_year', ['options' => ['class' => 'we3-field-inner']])
                  ->input('number', ['placeholder' => '例：1901'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->birth_year) ?></div>
            </div>

            <!-- 去世年份 -->
            <div class="we3-fieldline">
              <div class="we3-lab">去世年份</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'death_year', ['options' => ['class' => 'we3-field-inner']])
                  ->input('number', ['placeholder' => '若未知可留空'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly"><?= Html::encode($model->death_year) ?></div>
            </div>

            <!-- 状态 -->
            <div class="we3-fieldline">
              <div class="we3-lab">状态</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'status', ['options' => ['class' => 'we3-field-inner']])
                  ->dropDownList([1 => '展示', 0 => '隐藏'], ['prompt' => '请选择'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly">
                <?= $model->status ? '展示' : '隐藏' ?>
              </div>
            </div>

            <!-- 简介 -->
            <div class="we3-fieldline we3-spanfull">
              <div class="we3-lab">简介</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'intro', ['options' => ['class' => 'we3-field-inner']])
                  ->textarea(['rows' => 3, 'placeholder' => '1-2 句话概括人物'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly we3-pre"><?= Html::encode($model->intro) ?></div>
            </div>

            <!-- 生平 -->
            <div class="we3-fieldline we3-spanfull">
              <div class="we3-lab">生平</div>
              <div class="we3-ctl we3-editable">
                <?= $form->field($model, 'biography', ['options' => ['class' => 'we3-field-inner']])
                  ->textarea(['rows' => 8, 'placeholder' => '可填写详细生平事迹'])
                  ->label(false) ?>
              </div>
              <div class="we3-ctl we3-readonly we3-pre"><?= Html::encode($model->biography) ?></div>
            </div>

          </div>
        </div>
      </div>

    </div>

    <!-- Save -->
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

  <!-- Drawer -->
  <?php if (!$isCreate): ?>
    <div class="we3-overlay" id="we3-overlay"></div>

    <aside class="we3-drawer" id="we3-drawer" aria-hidden="true">
      <div class="we3-drawer-hd">
        <div>
          <div class="we3-drawer-title">事件关联与媒资</div>
          <div class="we3-drawer-sub">关联、上传、删除都在这里完成。</div>
        </div>
        <button type="button" class="we3-iconbtn" id="we3-close-drawer" aria-label="关闭">×</button>
      </div>

      <div class="we3-drawer-bd">

        <!-- Events -->
        <div class="we3-panel we3-panel-people">
          <div class="we3-panel-hd">
            <div class="we3-panel-title">关联事件</div>
            <div class="we3-panel-meta">已绑定：<?= $eventCount ?></div>
          </div>

          <div class="we3-panel-bd">

            <div class="we3-person-grid">
              <?php foreach (($model->events ?? []) as $event): ?>
                <?php
                  $title = (string)$event->title;
                  $initial = mb_substr(trim($title), 0, 1, 'UTF-8');
                  $rel = $relationMap[$event->id] ?? '未填写';
                  $time = $event->event_date ?: '-';
                ?>
                <div class="we3-person-card">
                  <div class="we3-person-ava"><?= Html::encode($initial) ?></div>
                  <div class="we3-person-main">
                    <div class="we3-person-name"><?= Html::encode($title) ?></div>
                    <div class="we3-person-rel">关系：<?= Html::encode($rel) ?> · 日期：<?= Html::encode($time) ?></div>
                  </div>

                  <div class="we3-person-op we3-editable-inline">
                    <?= Html::beginForm(['detach-event', 'id' => $model->id], 'post', ['class' => 'we3-miniop']) .
                        Html::hiddenInput('event_id', $event->id) .
                        Html::submitButton('移除', ['class' => 'btn btn-xs btn-soft-danger']) .
                        Html::endForm(); ?>
                  </div>
                </div>
              <?php endforeach; ?>

              <?php if (empty($model->events)): ?>
                <div class="we3-empty">暂无关联事件</div>
              <?php endif; ?>
            </div>

            <div class="we3-split"></div>

            <div class="we3-editable-inline">
              <?php if ($relationForm): ?>
                <?php $pf = ActiveForm::begin([
                  'action' => ['attach-event', 'id' => $model->id],
                  'options' => ['class' => 'we3-miniForm we3-miniForm-people']
                ]); ?>

                <div class="we3-miniGrid we3-miniGrid-people">
                  <div class="we3-miniCol">
                    <?= $pf->field($relationForm, 'event_id')
                      ->dropDownList($eventOptions, ['prompt' => '选择事件'])
                      ->label('事件') ?>
                  </div>
                  <div class="we3-miniCol">
                    <?= $pf->field($relationForm, 'relation_type')
                      ->textInput(['placeholder' => '如：参加/指挥/相关'])
                      ->label('关系（可选）') ?>
                  </div>

                  <div class="we3-miniCol we3-miniColBtn">
                    <?= Html::submitButton('绑定事件', [
                      'class' => 'btn btn-soft-success we3-btn we3-btn-block',
                    ]) ?>
                  </div>
                </div>

                <?php ActiveForm::end(); ?>
              <?php else: ?>
                <div class="we3-empty">编辑页可维护事件关联</div>
              <?php endif; ?>
            </div>

          </div>
        </div>

        <!-- Media -->
        <div class="we3-panel we3-panel-media">
          <div class="we3-panel-hd">
            <div class="we3-panel-title">媒资</div>
            <div class="we3-panel-meta">图片 <?= count($imageList) ?> · 文档 <?= count($docList) ?></div>
          </div>

          <div class="we3-panel-bd">

            <div class="we3-uploadbar">
              <?= Html::beginForm(['upload-media', 'id' => $model->id], 'post', [
                'enctype' => 'multipart/form-data',
                'id' => 'we3-upload-form',
              ]) ?>
                <div class="we3-upload-modern">
                  <div class="we3-upload-hint">
                    <span class="we3-upload-icon">📎</span>
                    <div>
                      <div class="we3-upload-hint-title">上传媒资文件</div>
                      <div class="we3-upload-hint-desc">上传后自动识别类型，支持图片 / PDF / DOC 等</div>
                    </div>
                  </div>
                  
                  <div class="we3-upload-action">
                    <input type="file" name="file" id="we3-upload-input" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" style="display:none;">
                    <button type="button" class="btn btn-primary btn-we3-upload" id="we3-upload-btn">
                      <span class="glyphicon glyphicon-cloud-upload"></span>
                      <span id="we3-upload-filename">选择文件并上传</span>
                    </button>
                  </div>
                </div>
              <?= Html::endForm() ?>
            </div>

            <style>
            .we3-upload-modern {
              display: flex;
              align-items: center;
              gap: 24px;
              padding: 20px;
              background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
              border-radius: 16px;
              border: 2px dashed rgba(0,0,0,0.12);
              margin-bottom: 16px;
            }

            .we3-upload-hint {
              display: flex;
              align-items: center;
              gap: 12px;
              flex: 1;
            }

            .we3-upload-icon {
              font-size: 32px;
            }

            .we3-upload-hint-title {
              font-weight: 900;
              font-size: 16px;
              color: #0f172a;
              margin-bottom: 4px;
            }

            .we3-upload-hint-desc {
              font-size: 13px;
              color: #64748b;
              font-weight: 700;
            }

            .we3-upload-action {
              flex-shrink: 0;
            }

            .btn-we3-upload {
              border-radius: 12px;
              padding: 14px 28px;
              font-weight: 900;
              font-size: 15px;
              display: flex;
              align-items: center;
              gap: 10px;
              background: linear-gradient(135deg, #10b981 0%, #059669 100%);
              border: 0;
              box-shadow: 0 4px 12px rgba(16,185,129,0.25);
              transition: all 0.2s ease;
              white-space: nowrap;
            }

            .btn-we3-upload:hover {
              transform: translateY(-2px);
              box-shadow: 0 6px 16px rgba(16,185,129,0.35);
              background: linear-gradient(135deg, #059669 0%, #047857 100%);
            }

            .btn-we3-upload:active {
              transform: translateY(0);
            }

            .btn-we3-upload .glyphicon {
              font-size: 18px;
            }

            @media (max-width: 768px) {
              .we3-upload-modern {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
              }
              
              .btn-we3-upload {
                width: 100%;
                justify-content: center;
              }
            }
            </style>

            <script>
            (function() {
              var fileInput = document.getElementById('we3-upload-input');
              var uploadBtn = document.getElementById('we3-upload-btn');
              var filenameSpan = document.getElementById('we3-upload-filename');
              var uploadForm = document.getElementById('we3-upload-form');
              
              if (uploadBtn && fileInput) {
                uploadBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  fileInput.click();
                });
                
                fileInput.addEventListener('change', function() {
                  if (fileInput.files && fileInput.files.length > 0) {
                    var file = fileInput.files[0];
                    filenameSpan.textContent = file.name;
                    uploadForm.submit();
                  }
                });
              }
            })();
            </script>



            <div class="we3-editable-inline">
              <?php if ($mediaForm): ?>
                <?php
                  $typeLabel = ($mediaForm->type === 'document') ? '文档' : '图片';
                ?>
                <?php $mf = ActiveForm::begin([
                  'action' => ['add-media', 'id' => $model->id],
                  'options' => ['class' => 'we3-miniForm we3-miniForm-media']
                ]); ?>

                  <?= $mf->field($mediaForm, 'type')->hiddenInput()->label(false) ?>
                  <?= $mf->field($mediaForm, 'path')->hiddenInput()->label(false) ?>

                  <div class="we3-miniGrid we3-miniGrid-media">
                    <div class="we3-miniCol">
                      <?= $mf->field($mediaForm, 'title')
                        ->textInput(['maxlength' => true, 'placeholder' => '自动填入'])
                        ->label('标题') ?>
                    </div>

                    <div class="we3-miniCol">
                      <div class="form-group">
                        <label class="control-label">类型</label>
                        <div class="we3-typebadge"><?= Html::encode($typeLabel) ?></div>
                      </div>
                    </div>

                    <div class="we3-miniCol we3-miniColBtn">
                      <?= Html::submitButton('添加媒资', [
                        'class' => 'btn btn-soft-success we3-btn we3-btn-block',
                      ]) ?>
                    </div>
                  </div>

                <?php ActiveForm::end(); ?>
              <?php else: ?>
                <div class="we3-empty">编辑页可维护媒资</div>
              <?php endif; ?>
            </div>

            <div class="we3-split"></div>

            <div class="we3-media-sections">
              <div class="we3-media-sec">
                <div class="we3-media-sec-hd">
                  <div class="we3-mini">图片</div>
                </div>

                <div class="we3-media-grid">
                  <?php foreach ($imageList as $m): ?>
                    <?php $url = '/' . ltrim($m->path, '/'); ?>
                    <div class="we3-media-card">
                      <a class="we3-media-thumb" href="<?= Html::encode($url) ?>" target="_blank" title="打开原图">
                        <img src="<?= Html::encode($url) ?>" alt="<?= Html::encode($m->title ?: '图片') ?>">
                      </a>
                      <div class="we3-media-main">
                        <div class="we3-media-title">
                          <?= Html::encode($m->title ?: '未命名') ?>
                        </div>
                        <div class="we3-media-links">
                          <?= Html::a('查看', $url, ['target' => '_blank', 'class' => 'we3-link']) ?>
                        </div>
                      </div>
                      <div class="we3-media-op we3-editable-inline">
                        <?= Html::beginForm(['delete-media', 'id' => $model->id], 'post', ['class' => 'we3-miniop']) .
                            Html::hiddenInput('media_id', $m->id) .
                            Html::submitButton('删除', ['class' => 'btn btn-xs btn-soft-danger']) .
                            Html::endForm(); ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                  <?php if (empty($imageList)): ?><div class="we3-empty">暂无图片</div><?php endif; ?>
                </div>
              </div>

              <div class="we3-media-sec">
                <div class="we3-media-sec-hd">
                  <div class="we3-mini">文档</div>
                </div>

                <div class="we3-media-grid">
                  <?php foreach ($docList as $m): ?>
                    <?php
                      $url = '/' . ltrim($m->path, '/');
                      $docExt = strtoupper(pathinfo($m->path, PATHINFO_EXTENSION) ?: 'DOC');
                    ?>
                    <div class="we3-media-card we3-media-card-doc">
                      <div class="we3-docicon"><?= Html::encode($docExt) ?></div>
                      <div class="we3-media-main">
                        <div class="we3-media-title">
                          <?= Html::encode(($m->title ?: '未命名')) ?>
                        </div>
                        <div class="we3-media-links">
                          <?= Html::a('查看', $url, ['target' => '_blank', 'class' => 'we3-link']) ?>
                        </div>
                      </div>
                      <div class="we3-media-op we3-editable-inline">
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

      </div>
    </aside>
  <?php endif; ?>

</div>

<?php
$personId = $isCreate ? 'null' : (int)$model->id;

$js = <<<JS
(function(){
  var canvas = document.getElementById('we3-canvas');
  var form = document.getElementById('we3-form');
  var toggleBtn = document.getElementById('we3-toggle-edit');
  var cancelBtn = document.getElementById('we3-cancel-edit');
  var saveBar = document.getElementById('we3-savebar');

  var openDrawer = document.getElementById('we3-open-drawer');
  var closeDrawer = document.getElementById('we3-close-drawer');
  var drawer = document.getElementById('we3-drawer');
  var overlay = document.getElementById('we3-overlay');

  function setEdit(on){
    if(!canvas) return;
    canvas.classList.toggle('is-edit', on);
    canvas.classList.toggle('is-view', !on);

    var inputs = form ? form.querySelectorAll('input,select,textarea') : [];
    inputs.forEach(function(el){
      if (el.closest('.we3-drawer')) return;
      if (el.type === 'hidden' || el.type === 'submit') return;
      el.disabled = !on;
      el.readOnly = !on && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA');
    });

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

  setEdit(canvas && canvas.classList.contains('is-edit'));

  if(toggleBtn){
    toggleBtn.addEventListener('click', function(){
      var on = !canvas.classList.contains('is-edit');
      setEdit(on);
    });
  }
  if(cancelBtn){
    cancelBtn.addEventListener('click', function(){
      dirty = false;
      submitting = false;
      setEdit(false);
    });
  }

  var dirty = false;
  var submitting = false; 
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
    if (submitting) return;
    if(!dirty) return;
    e.preventDefault();
    e.returnValue = '';
    return '';
  });

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

  var personId = $personId;
  var stateKey = personId ? ('wp3_state_' + personId) : null;

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

  document.addEventListener('submit', function(e){
    // 只处理主表单提交（保存更新/创建）
    if (form && e.target === form) {
      // 先保存 state（你原本就想做）
      if (typeof saveState === 'function') saveState();

      // ✅关键：提交时关闭脏标记，并标记正在提交
      submitting = true;
      dirty = false;
    } else {
      // drawer 内的小表单（绑定/删除/上传）照旧保存 state
      if (typeof saveState === 'function') saveState();
    }
  }, true);

  restoreState();

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
