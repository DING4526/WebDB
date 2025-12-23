<?php

/**
 * 孔祥昊 2311439
 * 地图事件控制
 */

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\WarEvent; 
use yii\data\ActiveDataProvider;
use yii\web\Response;

class EventController extends Controller
{
    // ...existing code...

    /**
     * 列表页及地图数据接口
     * @param string|null $location 按地点筛选
     * @param string|null $action 特殊动作 (get-active-locations)
     */
    public function actionIndex($location = null, $action = null)
    {
        // 1. AJAX 接口：返回所有有数据的地点及其事件列表
        if ($action === 'get-active-locations') {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            // 修改：查询 id, title, location，以便前端生成链接
            $events = WarEvent::find()
                ->select(['id', 'title', 'location'])
                ->where(['status' => 1])
                ->andWhere(['not', ['location' => null]])
                ->andWhere(['!=', 'location', ''])
                ->asArray()
                ->all();
            
            // 按地点分组数据
            // 格式: { "上海": [{id:1, title:"淞沪会战"}, ...], "陕西": [...] }
            $result = [];
            foreach ($events as $event) {
                $loc = $event['location'];
                if (!isset($result[$loc])) {
                    $result[$loc] = [];
                }
                $result[$loc][] = [
                    'id' => $event['id'],
                    'title' => $event['title']
                ];
            }
            
            return $result;
        }

        // 2. 页面渲染逻辑
        $query = WarEvent::find()->where(['status' => 1]);

        // 如果 URL 中带了 location 参数，则进行筛选
        if (!empty($location)) {
            // 使用精确匹配
            $query->andWhere(['location' => $location]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['event_date' => SORT_ASC]],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'location' => $location,
        ]);
    }

    /**
     * 查看详情
     * 解决点击列表项报 404 的问题
     * 将请求重定向到已存在的 Timeline 详情页
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionView($id)
    {
        return $this->redirect(['/timeline/view', 'id' => $id]);
    }
}