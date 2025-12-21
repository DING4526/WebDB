<?php

/**
 * 抗战留言审核
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\WarMessageSearch;
use common\models\WarMessage;

class WarMessageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'approve', 'reject', 'approve-all', 'reject-all'],
                        'matchCallback' => function () {
                            $user = Yii::$app->user->getUser();
                            return $user && $user->isMember();
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve' => ['POST'],
                    'reject' => ['POST'],
                    'approve-all' => ['POST'],
                    'reject-all' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new WarMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApprove($id)
    {
        $this->updateStatus($id, WarMessage::STATUS_APPROVED, '留言已通过');
        return $this->redirect(['index']);
    }

    public function actionReject($id)
    {
        $this->updateStatus($id, WarMessage::STATUS_REJECTED, '留言已拒绝');
        return $this->redirect(['index']);
    }

    public function actionApproveAll()
    {
        WarMessage::updateAll(['status' => WarMessage::STATUS_APPROVED], ['status' => WarMessage::STATUS_PENDING]);
        Yii::$app->session->setFlash('success', '所有待审留言已通过');
        return $this->redirect(['index']);
    }

    public function actionRejectAll()
    {
        WarMessage::updateAll(['status' => WarMessage::STATUS_REJECTED], ['status' => WarMessage::STATUS_PENDING]);
        Yii::$app->session->setFlash('success', '所有待审留言已拒绝');
        return $this->redirect(['index']);
    }

    protected function updateStatus(int $id, int $status, string $message): void
    {
        if (($model = WarMessage::findOne($id)) !== null) {
            $model->status = $status;
            $model->save(false);
            Yii::$app->session->setFlash('success', $message);
        }
    }
}
