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
            ->where(['id' => $id])
            ->with(['people.coverImage', 'medias']) // 预加载关联数据
            ->one();

        if (!$model) throw new \yii\web\NotFoundHttpException("事件未找到");

        // 分类媒体文件：图片放一组，文章/文档放一组
        $images = [];
        $articles = [];
        foreach ($model->medias as $media) {
            if ($media->type === 'image') {
                $images[] = $media;
            } elseif ($media->type === 'article' || $media->type === 'link') {
                $articles[] = $media;
            }
        }

        $messages = \common\models\WarMessage::find()
            ->where(['target_type' => 'event', 'target_id' => $id, 'status' => 1])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'images' => $images,     // 传给轮播图
            'articles' => $articles, // 传给相关文章栏目
            'messages' => $messages,
        ]);
    }
}
