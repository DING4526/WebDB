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
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

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
                            'add-media', 'delete-media', 'upload-media',
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
                    'upload-media' => ['POST'],
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
        if ($media = WarMedia::findOne(['id' => $mediaId, 'person_id' => $id])) {
            $this->removePhysicalFile($media->path);
            $media->delete();
            Yii::$app->session->setFlash('success', '媒资已删除');
        } else {
            Yii::$app->session->setFlash('error', '媒资不存在');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionUploadMedia($id)
    {
        $file = UploadedFile::getInstanceByName('file');

        if (!$file) {
            Yii::$app->session->setFlash('error', '请选择要上传的文件');
            return $this->redirect(['view', 'id' => $id]);
        }

        $type = $this->detectType($file);
        if (!$this->validateFile($file, $type)) {
            Yii::$app->session->setFlash('error', '文件类型或大小不符合要求');
            return $this->redirect(['view', 'id' => $id]);
        }

        $subDir = $type === 'document' ? 'docs' : "people/{$id}";
        $relativePath = $this->storeFile($file, $subDir);
        if ($relativePath === null) {
            Yii::$app->session->setFlash('error', '文件保存失败');
            return $this->redirect(['view', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', '文件已上传，表单已填充，可修改标题后保存');
        return $this->redirect([
            'view',
            'id' => $id,
            'm_title' => $file->baseName,
            'm_type' => $type,
            'm_path' => $relativePath,
        ]);
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
        $form->type = Yii::$app->request->get('m_type', 'image');
        $form->path = Yii::$app->request->get('m_path');
        $form->title = Yii::$app->request->get('m_title');
        return $form;
    }

    protected function getMediaList(int $personId): array
    {
        return WarMedia::find()
            ->where(['person_id' => $personId])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    protected function detectType(UploadedFile $file): string
    {
        $ext = strtolower($file->extension);
        $images = ['jpg', 'jpeg', 'png', 'webp'];
        return in_array($ext, $images, true) ? 'image' : 'document';
    }

    protected function validateFile(UploadedFile $file, string $type): bool
    {
        $allowedImages = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedDocs = ['pdf', 'doc', 'docx'];
        $ext = strtolower($file->extension);

        $allowed = $type === 'document' ? $allowedDocs : $allowedImages;
        if (!in_array($ext, $allowed, true)) {
            return false;
        }

        // 10MB
        return $file->size <= 10 * 1024 * 1024;
    }

    protected function storeFile(UploadedFile $file, string $subDir): ?string
    {
        $basePath = Yii::getAlias('@uploadsWarRoot');
        $targetDir = $basePath . '/' . $subDir;
        FileHelper::createDirectory($targetDir, 0775, true);

        $filename = date('Ymd_His') . '_' . Yii::$app->security->generateRandomString(6) . '.' . $file->extension;
        $fullPath = $targetDir . '/' . $filename;
        if (!$file->saveAs($fullPath)) {
            return null;
        }

        return 'uploads/war/' . $subDir . '/' . $filename;
    }

    protected function removePhysicalFile(string $relativePath): void
    {
        $suffix = ltrim(str_replace('uploads/war/', '', $relativePath), '/');
        $fullPath = Yii::getAlias('@uploadsWarRoot') . '/' . $suffix;
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
