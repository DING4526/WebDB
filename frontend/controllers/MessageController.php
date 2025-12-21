<?php

/**
 * Ding 2310724
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
}
