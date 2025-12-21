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
use common\models\WarPerson;
use common\models\WarEventPerson;
use common\models\WarMedia;
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
                        'actions' => [
                            'index', 'view', 'create', 'update', 'delete',
                            'publish', 'offline',
                            'attach-person', 'detach-person',
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
                    'attach-person' => ['POST'],
                    'detach-person' => ['POST'],
                    'add-media' => ['POST'],
                    'delete-media' => ['POST'],
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
            'personOptions' => $this->getPersonList(),
            'relationForm' => $this->buildRelationForm($id),
            'mediaForm' => $this->buildMediaForm($id),
            'mediaList' => $this->getMediaList($id),
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

    public function actionPublish($id)
    {
        $model = $this->findModel($id);
        $model->status = 1;
        $model->save(false);
        Yii::$app->session->setFlash('success', '事件已发布');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionOffline($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save(false);
        Yii::$app->session->setFlash('success', '事件已下线');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAttachPerson($id)
    {
        $relation = new WarEventPerson();
        $relation->event_id = $id;
        if ($relation->load(Yii::$app->request->post())) {
            try {
                $saved = $relation->save();
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), __METHOD__);
                $saved = false;
            }
            Yii::$app->session->setFlash($saved ? 'success' : 'error', $saved ? '人物已绑定' : '绑定失败，请检查选择');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDetachPerson($id)
    {
        $personId = (int)Yii::$app->request->post('person_id');
        WarEventPerson::deleteAll(['event_id' => $id, 'person_id' => $personId]);
        Yii::$app->session->setFlash('success', '已移除绑定');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAddMedia($id)
    {
        $media = new WarMedia();
        $media->event_id = $id;
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
        WarMedia::deleteAll(['id' => $mediaId, 'event_id' => $id]);
        Yii::$app->session->setFlash('success', '媒资已删除');
        return $this->redirect(['view', 'id' => $id]);
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

    protected function getPersonList(): array
    {
        return WarPerson::find()
            ->select('name')
            ->where(['status' => [0, 1]])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    protected function buildRelationForm(int $eventId): WarEventPerson
    {
        $form = new WarEventPerson();
        $form->event_id = $eventId;
        return $form;
    }

    protected function buildMediaForm(int $eventId): WarMedia
    {
        $form = new WarMedia();
        $form->event_id = $eventId;
        $form->type = 'image';
        return $form;
    }

    protected function getMediaList(int $eventId): array
    {
        return WarMedia::find()
            ->where(['event_id' => $eventId])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }
}
