<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TeamMemberApply;

/**
 * TeamMemberApplySearch represents the model behind the search form of `common\models\TeamMemberApply`.
 */
class TeamMemberApplySearch extends TeamMemberApply
{
    public function rules()
    {
        return [
            [['id', 'user_id', 'team_id', 'status', 'reviewer_id'], 'integer'],
            [['name', 'student_no', 'email'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TeamMemberApply::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'status' => $this->status,
            'reviewer_id' => $this->reviewer_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'student_no', $this->student_no])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
