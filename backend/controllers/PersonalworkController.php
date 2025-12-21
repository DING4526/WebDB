<?php
namespace backend\controllers;

/**
 * Ding 2310724
 * 扫描展示个人作业文件列表（/data/personal）
 */

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use common\models\TeamMember;
use yii\filters\VerbFilter;
use common\helpers\UploadHelper;

class PersonalworkController extends Controller
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
        $memberRecord = $this->findMemberRecord($user);
        $currentStudentNo = $memberRecord->student_no ?? null;
        $canUpload = $user && $user->isMember();

        $basePath = Yii::getAlias('@data/personal');
        $members = [];
        $totalFiles = 0;

        if (is_dir($basePath)) {
            foreach (scandir($basePath) as $dir) {
                if ($dir === '.' || $dir === '..') continue;
                $fullDir = $basePath . '/' . $dir;
                if (!is_dir($fullDir)) continue;

                $files = [];
                foreach (scandir($fullDir) as $f) {
                    if ($f === '.' || $f === '..') continue;
                    $full = $fullDir . '/' . $f;
                    if (is_file($full)) {
                        $files[] = [
                            'name' => $f,
                            'size' => filesize($full),
                            'mtime' => filemtime($full),
                        ];
                        $totalFiles++;
                    }
                }
                usort($files, fn($a,$b) => $b['mtime'] <=> $a['mtime']);
                $members[] = [
                    'folder' => $dir,
                    'files' => $files,
                ];
            }
        }

        return $this->render('index', [
            'members' => $members,
            'canUpload' => $canUpload,
            'isRoot' => $isRoot,
            'currentStudentNo' => $currentStudentNo,
            'memberCount' => count($members),
            'fileCount' => $totalFiles,
        ]);
    }

    public function actionUpload()
    {
        $user = Yii::$app->user->getUser();
        if (!$user || !$user->isMember()) {
            throw new ForbiddenHttpException('仅 member/root 可上传个人作业');
        }

        $memberRecord = $this->findMemberRecord($user);
        $studentNo = Yii::$app->request->post('student_no');
        if (!$user->isRoot()) {
            $studentNo = $memberRecord->student_no ?? null;
        }
        if (!$studentNo) {
            Yii::$app->session->setFlash('error', '未找到学号，请先在主页补充学号。');
            return $this->redirect(['index']);
        }
        if (!$this->isValidStudentNo($studentNo)) {
            Yii::$app->session->setFlash('error', '非法的学号目录，仅允许字母、数字、下划线和短横线。');
            return $this->redirect(['index']);
        }

        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            Yii::$app->session->setFlash('error', '请选择要上传的文件。');
            return $this->redirect(['index']);
        }

        $basePath = Yii::getAlias('@data/personal/' . $studentNo);
        FileHelper::createDirectory($basePath);
        $safeBase = UploadHelper::sanitizeBaseName($file->baseName);
        $safeExt = UploadHelper::sanitizeExtension($file->extension);
        $fileName = $safeExt ? $safeBase . '.' . $safeExt : $safeBase;
        $target = $basePath . '/' . $fileName;
        $suffix = 1;
        while (file_exists($target)) {
            $fileName = $safeBase . '_' . $suffix . ($safeExt ? '.' . $safeExt : '');
            $target = $basePath . '/' . $fileName;
            $suffix++;
        }
        if ($file->saveAs($target)) {
            Yii::$app->session->setFlash('success', '上传成功：' . $file->name);
        } else {
            Yii::$app->session->setFlash('error', '上传失败，请重试。');
        }

        return $this->redirect(['index']);
    }

    public function actionDelete($folder, $name)
    {
        $user = Yii::$app->user->getUser();
        if (!$user || !$user->isMember()) {
            throw new ForbiddenHttpException('仅 member/root 可删除个人作业');
        }

        $memberRecord = $this->findMemberRecord($user);
        $isRoot = $user->isRoot();
        $folder = basename($folder);
        $name = basename($name);

        if (!$isRoot) {
            if (!$memberRecord || $memberRecord->student_no !== $folder) {
                throw new ForbiddenHttpException('只能删除本人目录下的文件');
            }
        }

        $basePath = Yii::getAlias('@data/personal');
        $baseRoot = realpath($basePath) ?: $basePath;
        $full = realpath($basePath . '/' . $folder . '/' . $name);
        if (!$full || strpos($full, $baseRoot) !== 0 || !is_file($full)) {
            throw new NotFoundHttpException('文件不存在');
        }

        if (is_file($full)) {
            unlink($full);
        }
        // 如果目录空了，顺手删目录
        $dirPath = $basePath . '/' . $folder;
        $dirReal = realpath($dirPath);

        // 再做一次安全校验：目录必须在 baseRoot 下
        if ($dirReal && strpos($dirReal, $baseRoot) === 0 && is_dir($dirReal)) {
            $items = array_diff(scandir($dirReal), ['.', '..']);
            // 只要没有剩余文件/子目录，就删除该目录
            if (count($items) === 0) {
                @rmdir($dirReal);
            }
        }
        Yii::$app->session->setFlash('success', '文件已删除');
        return $this->redirect(['index']);
    }

    protected function findMemberRecord($user)
    {
        if (!$user || !Yii::$app->teamProvider) {
            return null;
        }
        $teamId = Yii::$app->teamProvider->getId();
        if (!$teamId) {
            return null;
        }
        return TeamMember::find()->andWhere(['team_id' => $teamId, 'user_id' => $user->id])->one();
    }

    protected function isValidStudentNo($studentNo): bool
    {
        return (bool)preg_match('/^[A-Za-z0-9_-]+$/', (string)$studentNo);
    }
}
