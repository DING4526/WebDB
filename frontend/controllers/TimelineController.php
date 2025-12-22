<?php

/**
 * Ding 2310724
 * liyu 2311591
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
    
    public function actionView($id)
    {
        // 1. 查找事件，同时带出人物及其封面图
        $model = \common\models\WarEvent::find()
            ->where(['id' => $id, 'status' => 1])
            ->with(['people.coverImage']) // 预加载人物的封面图
            ->one();

        if (!$model) throw new \yii\web\NotFoundHttpException("事件不存在");

        // 2. 查找属于该事件的所有图片媒体
        $eventImages = \common\models\WarMedia::find()
            ->where(['event_id' => $id, 'type' => 'image'])
            ->all();

        // 3. 查找留言
        $messages = \common\models\WarMessage::find()
            ->where(['target_type' => 'event', 'target_id' => $id, 'status' => 1])
            ->orderBy('id DESC')
            ->all();

        return $this->render('view', [
            'model' => $model,
            'eventImages' => $eventImages, // 传给视图
            'messages' => $messages,
        ]);
    }
}
