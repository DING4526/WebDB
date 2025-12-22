<?php

/**
 * 苏奕扬 2311330
 * 前台人物列表与详情
 */

namespace frontend\controllers;

use common\models\WarPerson;
use common\models\WarMessage;
use Yii;
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

        // 处理留言提交
        $newMessage = new WarMessage();
        if ($newMessage->load(Yii::$app->request->post())) {
            $newMessage->target_type = 'person';
            $newMessage->target_id = $id;
            $newMessage->status = WarMessage::STATUS_PENDING; // 默认待审核
            
            if ($newMessage->save()) {
                Yii::$app->session->setFlash('success', '留言提交成功，等待审核。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', '留言提交失败。');
            }
        }

        // 获取已审核留言
        $comments = WarMessage::find()
            ->where([
                'target_type' => 'person',
                'target_id' => $id,
                'status' => WarMessage::STATUS_APPROVED
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        // 共享侧边栏数据
        $sidebarData = $this->getSidebarData(null);

        return $this->render('view', array_merge([
            'model' => $model,
            'newMessage' => $newMessage,
            'comments' => $comments,
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
