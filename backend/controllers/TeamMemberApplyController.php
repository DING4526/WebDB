<?php

/**
 * Ding 2310724
 * 成员申请与审批控制模块
 */

namespace backend\controllers;

use Yii;
use common\models\TeamMemberApply;
use backend\models\TeamMemberApplySearch;
use common\models\TeamMember;
use common\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * 成员申请与审批
 */
class TeamMemberApplyController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve' => ['POST'],
                    'reject' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->requireRoot();
        $searchModel = new TeamMemberApplySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->getUser();
        if ($user && $user->isRoot()) {
            throw new \yii\web\ForbiddenHttpException('root 管理员 无需申请成员身份');
        }
        if ($user && $user->isMember()) {
            throw new \yii\web\ForbiddenHttpException('已是 member，无需申请成员身份');
        }

        $model = new TeamMemberApply();
        if ($user) {
            $model->user_id = $user->id;
            $model->name = $user->username;
            $model->email = $user->email;
        }

        $team = Yii::$app->teamProvider->getTeam();
        if ($team) {
            $model->team_id = $team->id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '申请已提交，请等待审核。');
            return $this->redirect(['site/index']);
        }

        return $this->render('create', [
            'model' => $model,
            'team' => $team,
        ]);
    }

    public function actionApprove($id)
    {
        $this->requireRoot();
        $model = $this->findModel($id);
        $model->status = TeamMemberApply::STATUS_APPROVED;
        $reviewer = Yii::$app->user->getUser();
        $model->reviewer_id = $reviewer ? $reviewer->id : Yii::$app->user->id;
        $model->reviewed_at = time();
        if ($model->save(false, ['status', 'reviewer_id', 'reviewed_at', 'updated_at'])) {
            // 提升为 member
            $syncOk = true;
            if ($model->user_id) {
                User::updateAll(['role' => User::ROLE_MEMBER], ['id' => $model->user_id]);
                $teamId = $model->team_id ?: (Yii::$app->teamProvider ? Yii::$app->teamProvider->getId() : null);
                if ($teamId) {
                    $tm = TeamMember::find()
                        ->andWhere(['team_id' => $teamId, 'user_id' => $model->user_id])
                        ->one();
                    if (!$tm) {
                        $tm = new TeamMember();
                        $tm->team_id = $teamId;
                        $tm->user_id = $model->user_id;
                        $tm->status = TeamMember::STATUS_ACTIVE;
                    }
                    if (!$tm->name) {
                        $tm->name = $model->name;
                    }
                    $tm->student_no = $model->student_no;
                    if (!$tm->save()) {
                        $error = $tm->firstErrors ? reset($tm->firstErrors) : '未知错误';
                        Yii::$app->session->setFlash('error', '成员信息同步失败：' . $error);
                        $syncOk = false;
                    }
                }
            }
            if ($syncOk) {
                Yii::$app->session->setFlash('success', '已通过申请，用户角色已升为 member。');
            }
        }
        return $this->redirect(['index']);
    }

    public function actionReject($id)
    {
        $this->requireRoot();
        $model = $this->findModel($id);
        $model->status = TeamMemberApply::STATUS_REJECTED;
        $reviewer = Yii::$app->user->getUser();
        $model->reviewer_id = $reviewer ? $reviewer->id : Yii::$app->user->id;
        $model->reviewed_at = time();
        $model->save(false, ['status', 'reviewer_id', 'reviewed_at', 'updated_at']);
        Yii::$app->session->setFlash('info', '已拒绝该申请。');
        return $this->redirect(['index']);
    }

    protected function requireRoot()
    {
        $user = Yii::$app->user->getUser();
        if (!$user || !$user->isRoot()) {
            throw new \yii\web\ForbiddenHttpException('仅 root 可执行此操作');
        }
    }

    protected function findModel($id)
    {
        if (($model = TeamMemberApply::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
