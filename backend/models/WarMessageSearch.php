<?php

/**
 * 抗战留言搜索
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WarMessage;

class WarMessageSearch extends WarMessage
{
    public function rules()
    {
        return [
            [['id', 'target_id', 'status'], 'integer'],
            [['nickname', 'target_type', 'content'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = WarMessage::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'target_id' => $this->target_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'target_type', $this->target_type])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
