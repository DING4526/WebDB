<?php
namespace backend\controllers;

/**
 * Ding 2310724
 * 统一处理团队/个人作业文件下载，防止路径穿越
 */

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class DownloadController extends Controller
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
        ];
    }

    public function actionFile($type, $path)
    {
        $root = Yii::getAlias('@data');

        if ($type === 'team') {
            $base = $root . '/team';
        } elseif ($type === 'personal') {
            $base = $root . '/personal';
        } else {
            throw new NotFoundHttpException('Invalid type');
        }

        // 防止 ../ 穿越
        $path = str_replace(['..', '\\'], ['', '/'], $path);
        $full = $base . '/' . $path;

        if (!is_file($full)) {
            throw new NotFoundHttpException('File not found');
        }

        return Yii::$app->response->sendFile($full);
    }
}
