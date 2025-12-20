<?php

/**
 * Ding 2310724
 * 团队成员表控制模块
 */

namespace backend\controllers;

use Yii;
use common\models\TeamMember;
use backend\models\TeamMemberSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\User;     

/**
 * TeamMemberController implements the CRUD actions for TeamMember model.
 */
class TeamMemberController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,    
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'my'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'matchCallback' => function () {
                            $user = Yii::$app->user->getUser();
                            return $user && $user->isRoot();
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

    /**
     * Lists all TeamMember models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeamMemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 成员自助查看/修改学号
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionMy()
    {
        $user = Yii::$app->user->getUser();
        if (!$user) {
            throw new \yii\web\ForbiddenHttpException('请先登录');
        }
        $teamId = Yii::$app->teamProvider ? Yii::$app->teamProvider->getId() : null;
        if (!$teamId) {
            throw new NotFoundHttpException('未配置团队信息');
        }

        $model = TeamMember::find()->andWhere(['team_id' => $teamId, 'user_id' => $user->id])->one();
        if (!$model) {
            throw new NotFoundHttpException('未找到您的成员信息');
        }

        $model->scenario = TeamMember::SCENARIO_SELF_UPDATE;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '学号已更新');
            return $this->redirect(['site/index']);
        }

        return $this->render('my', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single TeamMember model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TeamMember model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->requireRoot();
        $model = new TeamMember();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TeamMember model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->requireRoot();
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TeamMember model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->requireRoot();
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function requireRoot()
    {
        $user = Yii::$app->user->getUser();
        if (!$user || !$user->isRoot()) {
            throw new \yii\web\ForbiddenHttpException('仅 root 可执行此操作');
        }
    }

    /**
     * Finds the TeamMember model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TeamMember the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeamMember::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
