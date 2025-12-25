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
    /**
     * 列表页及地图数据接口
     * @param string|null $location 按地点筛选
     * @param string|null $action 特殊动作 (get-active-locations)
     */
    public function actionIndex($location = null, $action = null)
    {
        // 1. AJAX 接口：返回所有有数据的地点及其完整事件信息
        if ($action === 'get-active-locations') {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            // 修改：使用子查询获取第一张图片
            $events = WarEvent::find()
                ->alias('e')
                ->select([
                    'e.id', 
                    'e.title', 
                    'e.location', 
                    'e.event_date', 
                    'e.summary',
                    // 使用子查询获取第一张图片的完整路径
                    '(SELECT m.path FROM {{%war_media}} m 
                      WHERE m.event_id = e.id 
                      AND m.type = "image" 
                      ORDER BY m.uploaded_at ASC, m.id ASC 
                      LIMIT 1) as image_path'
                ])
                ->where(['e.status' => 1])
                ->andWhere(['not', ['e.location' => null]])
                ->andWhere(['!=', 'e.location', ''])
                ->asArray()
                ->all();
            
            // 按地点分组数据
            $result = [];
            foreach ($events as $event) {
                $loc = $event['location'];
                if (!isset($result[$loc])) {
                    $result[$loc] = [];
                }
                
                // 简化图片路径处理 - 图片直接在 /uploads/ 目录下
                $imagePath = null;
                if (!empty($event['image_path'])) {
                    $rawPath = $event['image_path'];
                    
                    // 如果是绝对 URL，直接使用
                    if (strpos($rawPath, 'http') === 0) {
                        $imagePath = $rawPath;
                    } else {
                        // 移除可能的前导斜杠和反斜杠，然后统一添加 /uploads/ 前缀
                        $cleanPath = ltrim($rawPath, '\\/');
                        
                        // 如果路径不包含 uploads，添加它
                        if (strpos($cleanPath, 'uploads') === false) {
                            $imagePath = '/uploads/' . $cleanPath;
                        } else {
                            // 如果已经包含 uploads，确保前面有 /
                            $imagePath = '/' . $cleanPath;
                        }
                        
                        // 统一使用正斜杠
                        $imagePath = str_replace('\\', '/', $imagePath);
                    }
                }

                $result[$loc][] = [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'event_date' => $event['event_date'],
                    'location' => $event['location'],
                    'summary' => $event['summary'] ?: '暂无摘要',
                    'image_path' => $imagePath
                ];
            }
            
            // 调试日志：记录最终返回的数据
            Yii::info('返回的地点事件数据: ' . json_encode($result, JSON_UNESCAPED_UNICODE), __METHOD__);
            
            return $result;
        }

        // 2. 页面渲染逻辑
        $query = WarEvent::find()->where(['status' => 1]);

        if (!empty($location)) {
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
     */
    public function actionView($id)
    {
        return $this->redirect(['/timeline/view', 'id' => $id]);
    }
}