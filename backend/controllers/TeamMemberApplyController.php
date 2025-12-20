<?php

/**
 * Ding 2310724
 * 成员申请与审批控制模块
 */

namespace backend\controllers;

use Yii;
use common\models\TeamMemberApply;
use backend\models\TeamMemberApplySearch;
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
        $model = new TeamMemberApply();
        $user = Yii::$app->user->getUser();
        if ($user) {
            $model->user_id = $user->id;
            $model->name = $user->username;
            $model->email = $user->email;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '申请已提交，请等待审核。');
            return $this->redirect(['site/index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionApprove($id)
    {
        $this->requireRoot();
        $model = $this->findModel($id);
        $model->status = TeamMemberApply::STATUS_APPROVED;
        $model->reviewer_id = Yii::$app->user->id;
        $model->reviewed_at = time();
        if ($model->save(false, ['status', 'reviewer_id', 'reviewed_at', 'updated_at'])) {
            // 提升为 member
            if ($model->user_id) {
                User::updateAll(['role' => User::ROLE_MEMBER], ['id' => $model->user_id]);
            }
            Yii::$app->session->setFlash('success', '已通过申请，用户角色已升为 member。');
        }
        return $this->redirect(['index']);
    }

    public function actionReject($id)
    {
        $this->requireRoot();
        $model = $this->findModel($id);
        $model->status = TeamMemberApply::STATUS_REJECTED;
        $model->reviewer_id = Yii::$app->user->id;
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
