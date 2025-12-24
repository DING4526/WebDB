<?php

/**
 * Ding 2310724
 * liyu 2311591
 * 前台时间轴与人物展示的最小骨架
 */

namespace frontend\controllers;

use common\models\WarStage;
use yii\web\Controller;
use Yii;

class TimelineController extends Controller
{
    public function actionIndex()
    {
        $stages = \common\models\WarStage::find()
            ->where(['status' => 1])
            ->orderBy(['sort_order' => SORT_ASC])
            ->with(['events' => function ($q) {
                $q->andWhere(['status' => 1])->with(['medias']); // 预加载媒体文件
            }])
            ->all();

        return $this->render('index', ['stages' => $stages]);
    }
    
    public function actionView($id)
    {
        $model = \common\models\WarEvent::find()
            ->where(['id' => $id, 'status' => 1])
            ->with(['people.coverImage', 'medias'])
            ->one();

        if (!$model) throw new \yii\web\NotFoundHttpException("事件未找到");

        // 记录访问日志
        $log = new \common\models\WarVisitLog();
        $log->target_type = 'event';
        $log->target_id = (int)$id;
        $log->visited_at = time();
        
        $log->user_id = \Yii::$app->user->id; 
        
        $log->save(false); 

        // 统计总访问量
        $visitCount = \common\models\WarVisitLog::find()
            ->where(['target_type' => 'event', 'target_id' => $id])
            ->count();

        // 媒资分类
        $images = [];
        $articles = [];
        foreach ($model->medias as $media) {
            if ($media->type === 'image') {
                $images[] = $media;
            } elseif (in_array($media->type, ['article', 'link', 'document'])) {
                $articles[] = $media;
            }
        }

        return $this->render('view', [
            'model' => $model,
            'images' => $images,
            'articles' => $articles,
            'messages' => $this->findApprovedMessages($id), 
            'visitCount' => $visitCount,
        ]);
    }

    protected function findApprovedMessages($event_id)
    {
        return \common\models\WarMessage::find()
            ->where([
                'target_type' => 'event', 
                'target_id' => $event_id, 
                'status' => 1
            ])
            ->orderBy('id DESC')
            ->all();
    }
}
