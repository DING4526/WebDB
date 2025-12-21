<?php

/**
 * Ding 2310724
 * 抗战人物后台 CRUD
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\WarPerson;
use common\models\WarEvent;
use common\models\WarEventPerson;
use common\models\WarMedia;
use backend\models\WarPersonSearch;

class WarPersonController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index', 'view', 'create', 'update', 'delete',
                            'publish', 'offline',
                            'attach-event', 'detach-event',
                            'add-media', 'delete-media',
                        ],
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
                    'publish' => ['POST'],
                    'offline' => ['POST'],
                    'attach-event' => ['POST'],
                    'detach-event' => ['POST'],
                    'add-media' => ['POST'],
                    'delete-media' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new WarPersonSearch();
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
            'eventOptions' => $this->getEventList(),
            'relationForm' => $this->buildRelationForm($id),
            'mediaForm' => $this->buildMediaForm($id),
            'mediaList' => $this->getMediaList($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new WarPerson();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '人物已创建');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '人物已更新');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionPublish($id)
    {
        $model = $this->findModel($id);
        $model->status = 1;
        $model->save(false);
        Yii::$app->session->setFlash('success', '人物已发布');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionOffline($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save(false);
        Yii::$app->session->setFlash('success', '人物已下线');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAttachEvent($id)
    {
        $relation = new WarEventPerson();
        $relation->person_id = $id;
        if ($relation->load(Yii::$app->request->post())) {
            try {
                $saved = $relation->save();
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), __METHOD__);
                $saved = false;
            }
            Yii::$app->session->setFlash($saved ? 'success' : 'error', $saved ? '事件已绑定' : '绑定失败，请检查选择');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDetachEvent($id)
    {
        $eventId = (int)Yii::$app->request->post('event_id');
        WarEventPerson::deleteAll(['event_id' => $eventId, 'person_id' => $id]);
        Yii::$app->session->setFlash('success', '已移除绑定');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAddMedia($id)
    {
        $media = new WarMedia();
        $media->person_id = $id;
        if ($media->load(Yii::$app->request->post())) {
            $media->uploaded_at = time();
            if ($media->save()) {
                Yii::$app->session->setFlash('success', '媒资已添加');
            } else {
                Yii::$app->session->setFlash('error', '媒资保存失败');
            }
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDeleteMedia($id)
    {
        $mediaId = (int)Yii::$app->request->post('media_id');
        WarMedia::deleteAll(['id' => $mediaId, 'person_id' => $id]);
        Yii::$app->session->setFlash('success', '媒资已删除');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', '人物已删除');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = WarPerson::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('未找到该人物');
    }

    protected function getEventList(): array
    {
        return WarEvent::find()
            ->select('title')
            ->where(['status' => [0, 1]])
            ->orderBy(['event_date' => SORT_ASC, 'id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    protected function buildRelationForm(int $personId): WarEventPerson
    {
        $form = new WarEventPerson();
        $form->person_id = $personId;
        return $form;
    }

    protected function buildMediaForm(int $personId): WarMedia
    {
        $form = new WarMedia();
        $form->person_id = $personId;
        $form->type = 'image';
        return $form;
    }

    protected function getMediaList(int $personId): array
    {
        return WarMedia::find()
            ->where(['person_id' => $personId])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }
}
