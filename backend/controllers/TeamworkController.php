<?php
namespace backend\controllers;

/**
 * Ding 2310724
 * 扫描展示团队作业文件列表（/data/team）
 */

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use common\models\User;
use yii\filters\VerbFilter;

class TeamworkController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'upload' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->getUser();
        $isRoot = $user && $user->isRoot();
        $canUpload = $user && $user->isMember();

        $basePath = Yii::getAlias('@data/team');
        $files = [];
        $meta = $this->loadMeta($basePath);
        $ownerIds = [];

        if (is_dir($basePath)) {
            foreach (scandir($basePath) as $f) {
                if ($f === '.' || $f === '..') continue;
                if ($f === '.meta.json') continue;
                $full = $basePath . '/' . $f;
                if (is_file($full)) {
                    $ownerId = $meta[$f]['uploader_id'] ?? null;
                    $ownerIds[] = $ownerId;
                    $files[] = [
                        'name' => $f,
                        'display' => $meta[$f]['original_name'] ?? $f,
                        'owner_id' => $ownerId,
                        'size' => filesize($full),
                        'mtime' => filemtime($full),
                    ];
                }
            }
        }

        usort($files, fn($a,$b) => $b['mtime'] <=> $a['mtime']);

        $ownerMap = [];
        $ownerIds = array_filter(array_unique($ownerIds));
        if ($ownerIds) {
            $ownerMap = User::find()->select(['username', 'id'])->where(['id' => $ownerIds])->indexBy('id')->column();
        }

        return $this->render('index', [
            'files' => $files,
            'canUpload' => $canUpload,
            'isRoot' => $isRoot,
            'ownerMap' => $ownerMap,
        ]);
    }

    public function actionUpload()
    {
        $user = Yii::$app->user->getUser();
        if (!$user || !$user->isMember()) {
            throw new ForbiddenHttpException('仅 member/root 可上传团队作业');
        }

        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            Yii::$app->session->setFlash('error', '请选择要上传的文件。');
            return $this->redirect(['index']);
        }

        $basePath = Yii::getAlias('@data/team');
        FileHelper::createDirectory($basePath);

        $safeBase = preg_replace('/[^A-Za-z0-9_\\-\\.]/', '_', $file->baseName);
        if ($safeBase === '') {
            $safeBase = 'file';
        }
        $safeExt = $file->extension ? preg_replace('/[^A-Za-z0-9]/', '', $file->extension) : '';
        $fileName = $safeExt ? $safeBase . '.' . $safeExt : $safeBase;
        $target = $basePath . '/' . $fileName;
        $suffix = 1;
        while (file_exists($target)) {
            $fileName = $safeBase . '_' . $suffix . ($safeExt ? '.' . $safeExt : '');
            $target = $basePath . '/' . $fileName;
            $suffix++;
        }

        if ($file->saveAs($target, false)) {
            $meta = $this->loadMeta($basePath);
            $meta[$fileName] = [
                'uploader_id' => $user->id,
                'original_name' => $file->name,
                'uploaded_at' => time(),
            ];
            $this->saveMeta($basePath, $meta);
            Yii::$app->session->setFlash('success', '上传成功：' . $file->name);
        } else {
            Yii::$app->session->setFlash('error', '上传失败，请重试。');
        }

        return $this->redirect(['index']);
    }

    public function actionDelete($name)
    {
        $user = Yii::$app->user->getUser();
        if (!$user || !$user->isMember()) {
            throw new ForbiddenHttpException('仅 member/root 可删除团队作业');
        }

        $fileName = basename($name);
        if ($fileName === '.meta.json') {
            throw new ForbiddenHttpException('非法文件名');
        }

        $basePath = Yii::getAlias('@data/team');
        $full = realpath($basePath . '/' . $fileName);
        if (!$full || strpos($full, realpath($basePath)) !== 0 || !is_file($full)) {
            throw new NotFoundHttpException('文件不存在');
        }

        $meta = $this->loadMeta($basePath);
        $ownerId = $meta[$fileName]['uploader_id'] ?? null;
        if (!$user->isRoot() && (!$ownerId || (int)$ownerId !== (int)$user->id)) {
            throw new ForbiddenHttpException('只能删除自己上传的文件');
        }

        @unlink($full);
        unset($meta[$fileName]);
        $this->saveMeta($basePath, $meta);
        Yii::$app->session->setFlash('success', '文件已删除');
        return $this->redirect(['index']);
    }

    protected function loadMeta($basePath)
    {
        $file = $basePath . '/.meta.json';
        if (!is_file($file)) {
            return [];
        }
        $content = file_get_contents($file);
        if ($content === false) {
            return [];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    protected function saveMeta($basePath, array $meta)
    {
        FileHelper::createDirectory($basePath);
        $file = $basePath . '/.meta.json';
        file_put_contents($file, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}
