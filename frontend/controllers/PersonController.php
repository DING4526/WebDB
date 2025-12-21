<?php

/**
 * 苏奕扬 2311330
 * 前台人物列表与详情
 */

namespace frontend\controllers;

use common\models\WarPerson;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PersonController extends Controller
{
    public function actionIndex($role_type = null)
    {
        $query = WarPerson::find()->where(['status' => 1]);

        if ($role_type) {
            $query->andWhere(['role_type' => $role_type]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['id' => SORT_ASC]),
            'pagination' => ['pageSize' => 12],
        ]);

        // 共享侧边栏数据
        $sidebarData = $this->getSidebarData($role_type);

        return $this->render('index', array_merge([
            'dataProvider' => $dataProvider,
        ], $sidebarData));
    }

    public function actionView($id)
    {
        $model = WarPerson::find()
            ->where(['id' => $id, 'status' => 1])
            ->with(['events'])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('未找到人物信息');
        }

        // 共享侧边栏数据
        $sidebarData = $this->getSidebarData(null);

        return $this->render('view', array_merge([
            'model' => $model,
        ], $sidebarData));
    }

    /**
     * 获取侧边栏所需数据
     */
    protected function getSidebarData($currentRole)
    {
        $roles = WarPerson::find()
            ->select('role_type')
            ->where(['status' => 1])
            ->distinct()
            ->column();
        
        return [
            'roles' => $roles,
            'currentRole' => $currentRole,
        ];
    }
}
