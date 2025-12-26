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
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
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
                            'publish', 'offline', 'toggle-status',
                            'attach-person', 'detach-person',
                            'add-media', 'delete-media', 'upload-media',
                            'add-link',
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
                    'toggle-status' => ['POST'],
                    'attach-person' => ['POST'],
                    'detach-person' => ['POST'],
                    'add-media' => ['POST'],
                    'delete-media' => ['POST'],
                    'upload-media' => ['POST'],
                    'add-link' => ['POST'],
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
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'mediaList' => $this->getMediaList($id),
            'relationMap' => $this->getRelationMap($id),
        ]);
    }


    public function actionCreate()
    {
        $model = new WarEvent();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '事件已创建，已进入编辑页，可继续维护关联与媒资');
            return $this->redirect(['update', 'id' => $model->id]);
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
            // 保存后返回详情页
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'mode' => 'edit',
            'model' => $model,
            'stageList' => $this->getStageList(),
            'personOptions' => $this->getPersonList(),
            'relationForm' => $this->buildRelationForm($id),
            'mediaForm' => $this->buildMediaForm($id),
            'mediaList' => $this->getMediaList($id),
            'relationMap' => $this->getRelationMap($id),
        ]);
    }


    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = ($model->status === 1) ? 0 : 1;
        $model->save(false);
        Yii::$app->session->setFlash('success', $model->status ? '事件已发布' : '事件已下线');
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
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
        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionDetachPerson($id)
    {
        $personId = (int)Yii::$app->request->post('person_id');
        WarEventPerson::deleteAll(['event_id' => $id, 'person_id' => $personId]);
        Yii::$app->session->setFlash('success', '已移除绑定');
        return $this->redirect(['update', 'id' => $id]);
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
        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionDeleteMedia($id)
    {
        $mediaId = (int)Yii::$app->request->post('media_id');
        if ($media = WarMedia::findOne(['id' => $mediaId, 'event_id' => $id])) {
            $this->removePhysicalFile($media->path);
            $media->delete();
            Yii::$app->session->setFlash('success', '媒资已删除');
        } else {
            Yii::$app->session->setFlash('error', '媒资不存在');
        }
        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionUploadMedia($id)
    {
        $file = UploadedFile::getInstanceByName('file');

        if (!$file) {
            Yii::$app->session->setFlash('error', '请选择要上传的文件');
            return $this->redirect(['update', 'id' => $id]);
        }

        $type = $this->detectType($file);
        if (!$this->validateFile($file, $type)) {
            Yii::$app->session->setFlash('error', '文件类型或大小不符合要求');
            return $this->redirect(['update', 'id' => $id]);
        }

        $subDir = $type === 'document' ? 'docs' : "events/{$id}";
        $relativePath = $this->storeFile($file, $subDir);
        if ($relativePath === null) {
            Yii::$app->session->setFlash('error', '文件保存失败');
            return $this->redirect(['update', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', '文件已上传，表单已填充，可修改标题后保存');
        return $this->redirect([
            'update',
            'id' => $id,
            'm_title' => $file->baseName,
            'm_type' => $type,
            'm_path' => $relativePath,
        ]);
    }

    public function actionAddLink($id)
    {
        $post = Yii::$app->request->post();
        $url = trim($post['link_url'] ?? '');
        $title = trim($post['link_title'] ?? '');
        $type = trim($post['link_type'] ?? 'document');

        if (empty($url)) {
            Yii::$app->session->setFlash('error', '请输入链接地址');
            return $this->redirect(['update', 'id' => $id]);
        }

        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            Yii::$app->session->setFlash('error', '请输入有效的链接地址');
            return $this->redirect(['update', 'id' => $id]);
        }

        $media = new WarMedia();
        $media->event_id = $id;
        $media->type = $type;
        $media->path = $url;
        $media->title = $title ?: $this->extractTitleFromUrl($url);
        $media->uploaded_at = time();

        if ($media->save()) {
            Yii::$app->session->setFlash('success', '链接已添加');
        } else {
            Yii::$app->session->setFlash('error', '链接保存失败');
        }
        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', '事件已删除');
        return $this->redirect(['index']);
    }

    protected function extractTitleFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $basename = basename($path);
            if ($basename && $basename !== '/') {
                return urldecode($basename);
            }
        }
        return parse_url($url, PHP_URL_HOST) ?: '外部链接';
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
        $form->type = Yii::$app->request->get('m_type', 'image');
        $form->path = Yii::$app->request->get('m_path');
        $form->title = Yii::$app->request->get('m_title');
        return $form;
    }

    protected function getMediaList(int $eventId): array
    {
        return WarMedia::find()
            ->where(['event_id' => $eventId])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    protected function getRelationMap(int $eventId): array
    {
        $map = [];
        foreach (WarEventPerson::find()->where(['event_id' => $eventId])->all() as $relation) {
            $map[$relation->person_id] = $relation->relation_type;
        }
        return $map;
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
        // Skip URLs (external links)
        if (preg_match('/^https?:\/\//i', $relativePath)) {
            return;
        }
        $suffix = ltrim(str_replace('uploads/war/', '', $relativePath), '/');
        $fullPath = Yii::getAlias('@uploadsWarRoot') . '/' . $suffix;
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
