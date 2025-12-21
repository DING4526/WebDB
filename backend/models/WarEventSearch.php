<?php

/**
 * Ding 2310724
 * 抗战事件后台搜索模型
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WarEvent;

class WarEventSearch extends WarEvent
{
    public function rules()
    {
        return [
            [['id', 'stage_id', 'status'], 'integer'],
            [['title', 'event_date', 'summary', 'location'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = WarEvent::find()->with('stage');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['event_date' => SORT_ASC, 'id' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'stage_id' => $this->stage_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'summary', $this->summary])
            ->andFilterWhere(['like', 'location', $this->location]);

        if (!empty($this->event_date)) {
            $query->andWhere(['event_date' => $this->event_date]);
        }

        return $dataProvider;
    }
}
