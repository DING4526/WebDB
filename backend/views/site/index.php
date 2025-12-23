<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\TeamMember;

/* @var $this yii\web\View */

$this->title = '团队后台主页';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');

$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
$isMember = $currentUser && $currentUser->isMember();
$memberRecord = null;
if ($currentUser && Yii::$app->teamProvider) {
    $teamId = Yii::$app->teamProvider->getId();
    if ($teamId) {
        $memberRecord = TeamMember::find()
            ->andWhere(['team_id' => $teamId, 'user_id' => $currentUser->id])
            ->one();
    }
}

$roleMatrix = [
    [
        'name' => 'root 管理员',
        'color' => 'danger',
        'abilities' => ['系统配置', '成员审批/维护'],
    ],
    [
        'name' => '团队成员（member）',
        'color' => 'primary',
        'abilities' => ['查看任务版', '提交作业文件', '参与团队协作'],
    ],
    [
        'name' => '普通用户（user）',
        'color' => 'info',
        'abilities' => ['仅浏览公共信息', '无法操作数据', '可申请成为团队成员'],
    ],
    [
        'name' => '游客（guest）',
        'color' => 'ghost',
        'abilities' => ['仅浏览首页', '无法访问后台'],
    ],
];

$applySteps = [
    '提交成员信息' => '通过"成员管理"录入或发起申请；暂未上线自助申请，可由管理员代填。',
    '管理员审批' => 'root / 管理员确认信息后设为启用状态，必要时分配角色。',
    '通知与初始权限' => '审批通过后即可访问作业与任务模块；高级操作需管理员调整角色。',
];

$teamInfo = Yii::$app->teamProvider->getTeam();
?>

<div class="site-index">
  
  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">管理团队信息、成员权限、作业文件与项目数据</div>
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">后台概览</h3>
    </div>
    <div class="adm-card-body">
      <div class="row">
        <div class="col-sm-4">
          <div class="adm-section">
            <div class="adm-section-title">当前身份</div>
            <?php if (Yii::$app->user->isGuest): ?>
              <div>游客（仅浏览）</div>
            <?php else: ?>
              <div style="font-weight:900;font-size:16px;margin-bottom:6px;">
                <?= Html::encode(Yii::$app->user->getUser()->username ?? '') ?>
              </div>
              <div class="adm-muted">
                角色：<?= Html::encode(Yii::$app->user->getUser()->role ?? 'member') ?>
              </div>
            <?php endif; ?>
            <?php if ($memberRecord && $memberRecord->student_no): ?>
              <div class="adm-muted" style="margin-top:4px;">
                学号：<?= Html::encode($memberRecord->student_no) ?>
              </div>
            <?php elseif ($isMember): ?>
              <div style="color:#f59e0b;margin-top:4px;">学号未登记，请补充。</div>
            <?php endif; ?>
            <?php if ($isMember): ?>
              <div style="margin-top:8px;">
                <a class="btn btn-xs btn-soft-primary" href="<?= Url::to(['team-member/my']) ?>">更新学号</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="adm-section">
            <div class="adm-section-title">团队信息</div>
            <?php if (!empty($teamInfo)): ?>
              <div style="font-weight:900;margin-bottom:4px;">
                <?= Html::encode($teamInfo->name) ?>
              </div>
              <div class="adm-muted">主题：<?= Html::encode($teamInfo->topic) ?></div>
            <?php else: ?>
              <div class="adm-muted">尚未创建团队</div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-sm-4">
          <!-- <div class="adm-section">
            <div class="adm-section-title">快速操作</div>
            <?php if ($isRoot): ?>
              <a class="btn btn-xs btn-soft-warning" href="<?= Url::to(['team/index']) ?>">查看团队信息</a>
              <a class="btn btn-xs btn-soft-primary" href="<?= Url::to(['team-member-apply/index']) ?>">审批成员申请</a>
              <a class="btn btn-xs btn-soft-success" href="<?= Url::to(['taskboard/index']) ?>">查看任务分工板</a>
            <?php elseif($isMember): ?>
              <a class="btn btn-xs btn-soft-warning" href="<?= Url::to(['team/index']) ?>">查看团队信息</a>
              <a class="btn btn-xs btn-soft-success" href="<?= Url::to(['taskboard/index']) ?>">查看任务分工板</a>
            <?php else: ?>
              <a class="btn btn-xs btn-soft-warning" href="<?= Url::to(['team/index']) ?>">查看团队信息</a>
              <a class="btn btn-xs btn-soft-primary" href="<?= Url::to(['team-member-apply/create']) ?>">申请成为团队成员</a>
            <?php endif; ?>                
          </div> -->
        </div>
      </div>
    </div>
  </div>

  <div class="row" style="margin-top:14px;">
    <div class="col-md-6">
      <div class="adm-card">
        <div class="adm-card-head">
          <h3 class="adm-card-title">角色与权限模型</h3>
        </div>
        <div class="adm-card-body">
          <p class="adm-hint" style="margin-bottom:12px;">用角色而非登录态区分能力范围，后续可接入 RBAC。</p>
          <div class="table-responsive">
            <table class="table table-condensed">
              <thead>
                <tr>
                  <th>角色</th>
                  <th>可做什么</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($roleMatrix as $role): ?>
                  <tr>
                    <td>
                      <span class="adm-badge adm-badge-<?= 
                         $role['color'] === 'danger' ? 'pending' : 
                        ($role['color'] === 'primary' ? 'active' : 
                        ($role['color'] === 'info' ? 'info' : 'inactive')) ?>">
                        <?= Html::encode($role['name']) ?>
                      </span>
                    </td>
                    <td class="adm-muted"><?= Html::encode(implode(' / ', $role['abilities'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="adm-card">
        <div class="adm-card-head">
          <h3 class="adm-card-title">成员注册/管理链路</h3>
        </div>
        <div class="adm-card-body">
          <p class="adm-hint" style="margin-bottom:12px;">优先打通"申请 → 审批 → 角色分配"闭环。</p>
          <ol style="padding-left: 18px;margin-bottom:16px;">
            <?php foreach ($applySteps as $step => $desc): ?>
              <li style="margin-bottom: 8px;">
                <strong><?= Html::encode($step) ?>：</strong>
                <span class="adm-muted"><?= Html::encode($desc) ?></span>
              </li>
            <?php endforeach; ?>
          </ol>
          <div class="adm-section" style="margin-bottom:0;">
            <?php if ($isRoot): ?>
              管理员入口：
              <a href="<?= Url::to(['team-member-apply/index']) ?>">成员申请审批</a>
              <span class="adm-muted">（root 可审批并授予 member）</span>
            <?php elseif (!$isMember): ?>
              申请入口：
              <a href="<?= Url::to(['team-member-apply/create']) ?>">提交成员申请</a>
              <span class="adm-muted">（申请后等待管理员审批）</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
