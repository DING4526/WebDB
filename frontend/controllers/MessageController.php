<?php

/**
 * Ding 2310724
 * liyu 2311591
 * 前台抗战专题留言板
 */

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\WarMessage;

class MessageController extends Controller
{
    public function actionIndex()
    {
        // 展示：只显示审核通过(status=1)
        $messages = WarMessage::find()
            ->where(['status' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        // 提交表单用的模型（提交后默认 status=0 待审核）
        $model = new WarMessage();
        $model->status = 0;

        if ($model->load(Yii::$app->request->post())) {
            // 最简字段兜底（你可以后面写 rules 做校验）
            $model->nickname = trim((string)$model->nickname);
            $model->content  = trim((string)$model->content);

            $now = time();
            $model->created_at = $now;
            $model->updated_at = $now;

            // target 可先不填（如果你的表结构要求必填，就按你表改）
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', '留言已提交，等待审核后展示。');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', '提交失败，请重试。');
        }

        return $this->render('index', [
            'messages' => $messages,
            'model' => $model,
        ]);
    }
    public function actionCreate($target_type = null, $target_id = null)
    {
        $model = new WarMessage();
        
        // 1. 接收从详情页传过来的关联信息
        $model->target_type = $target_type;
        $model->target_id = (int)$target_id;
        $model->status = 0; // 默认待审核

        if ($model->load(Yii::$app->request->post())) {
            // 设置时间戳
            $model->created_at = time();
            $model->updated_at = time();

            if ($model->save()) {
                Yii::$app->session->setFlash('success', '感言提交成功，请等待审核。');
                
                // 2. 提交成功后，如果是针对事件的留言，跳回事件详情页
                if ($model->target_type === 'event' && $model->target_id) {
                    return $this->redirect(['timeline/view', 'id' => $model->target_id]);
                }
                // 否则跳到留言墙
                return $this->redirect(['index']);
            }
        }

        // 3. 渲染专门的提交页面
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
