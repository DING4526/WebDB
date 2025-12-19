<?php
namespace backend\controllers;

/**
 * Ding 2310724
 * 扫描展示团队作业文件列表（/data/team）
 */

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

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
        ];
    }

    public function actionIndex()
    {
        $basePath = Yii::getAlias('@data/team');
        $files = [];

        if (is_dir($basePath)) {
            foreach (scandir($basePath) as $f) {
                if ($f === '.' || $f === '..') continue;
                $full = $basePath . '/' . $f;
                if (is_file($full)) {
                    $files[] = [
                        'name' => $f,
                        'size' => filesize($full),
                        'mtime' => filemtime($full),
                    ];
                }
            }
        }

        usort($files, fn($a,$b) => $b['mtime'] <=> $a['mtime']);

        return $this->render('index', [
            'files' => $files,
        ]);
    }
}
