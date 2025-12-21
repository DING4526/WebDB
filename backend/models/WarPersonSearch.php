<?php

/**
 * Ding 2310724
 * 抗战人物后台搜索模型
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WarPerson;

class WarPersonSearch extends WarPerson
{
    public function rules()
    {
        return [
            [['id', 'birth_year', 'death_year', 'status'], 'integer'],
            [['name', 'role_type', 'intro'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = WarPerson::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'birth_year' => $this->birth_year,
            'death_year' => $this->death_year,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'role_type', $this->role_type])
            ->andFilterWhere(['like', 'intro', $this->intro]);

        return $dataProvider;
    }
}
