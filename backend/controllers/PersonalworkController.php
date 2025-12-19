<?php
namespace backend\controllers;

/**
 * Ding 2310724
 * 扫描展示个人作业文件列表（/data/personal）
 */

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

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
        ];
    }

    public function actionIndex()
    {
        $basePath = Yii::getAlias('@data/personal');
        $members = [];

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
        ]);
    }
}
