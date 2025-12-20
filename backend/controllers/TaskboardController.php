<?php

/**
 * Ding 2310724
 * 任务分工版控制器
 */

namespace backend\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;

/**
 * 任务分工板（占位）控制器
 */
class TaskboardController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $user = Yii::$app->user->getUser();
                            if (!$user) {
                                return false;
                            }
                            return $user->isMember();
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * 简易任务分工列表
     */
    public function actionIndex()
    {
        $lanes = [
            [
                'title' => '待办',
                'color' => 'warning',
                'items' => [
                    ['name' => '作业上传流程设计', 'owner' => '管理员', 'note' => '确定是否采用数据库存储'],
                    ['name' => '成员申请表单', 'owner' => 'member', 'note' => '新增状态：待审批/已拒绝'],
                    ['name' => '权限模型评审', 'owner' => 'root', 'note' => '定义各角色 CRUD 范围'],
                ],
            ],
            [
                'title' => '进行中',
                'color' => 'info',
                'items' => [
                    ['name' => '团队首页信息梳理', 'owner' => 'member', 'note' => '补充角色提示与导航'],
                    ['name' => '任务分工板原型', 'owner' => 'member', 'note' => '当前为静态列表示例'],
                ],
            ],
            [
                'title' => '已完成',
                'color' => 'success',
                'items' => [
                    ['name' => '目录扫描展示作业', 'owner' => 'member', 'note' => '团队/个人作业列表可用'],
                ],
            ],
        ];

        return $this->render('index', [
            'lanes' => $lanes,
        ]);
    }
}
