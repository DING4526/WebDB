<?php
namespace backend\controllers;

/**
 * Ding 2310724
 * 团队项目后台管理入口（抗战80周年专题，占位）
 */

use yii\web\Controller;
use yii\filters\AccessControl;

class ProjectController extends Controller
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
        return $this->render('index');
    }
}
