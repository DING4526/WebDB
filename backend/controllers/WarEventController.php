<?php

/**
 * Ding 2310724
 * 抗战事件后台 CRUD
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\WarEvent;
use common\models\WarStage;
use backend\models\WarEventSearch;

class WarEventController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new WarEventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new WarEvent();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '事件已创建');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'stageList' => $this->getStageList(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '事件已更新');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'stageList' => $this->getStageList(),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', '事件已删除');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = WarEvent::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('未找到该事件');
    }

    protected function getStageList(): array
    {
        return WarStage::find()
            ->select('name')
            ->where(['status' => 1])
            ->orderBy(['sort_order' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }
}
