<?php

/**
 * Ding 2310724
 * 前台时间轴与人物展示的最小骨架
 */

namespace frontend\controllers;

use common\models\WarStage;
use yii\web\Controller;

class TimelineController extends Controller
{
    public function actionIndex()
    {
        $stages = WarStage::find()
            ->where(['status' => 1])
            ->orderBy(['sort_order' => SORT_ASC, 'start_year' => SORT_ASC])
            ->with(['events' => function ($q) {
                $q->andWhere(['status' => 1]);
            }])
            ->all();

        return $this->render('index', [
            'stages' => $stages,
        ]);
    }
}
