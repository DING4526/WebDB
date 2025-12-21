<?php

/**
 * Ding 2310724
 * 前台人物列表与详情
 */

namespace frontend\controllers;

use common\models\WarPerson;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PersonController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => WarPerson::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC]),
            'pagination' => ['pageSize' => 12],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
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

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
