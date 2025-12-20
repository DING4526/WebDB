<?php

/**
 * Ding 2310724
 * 团队成员表搜索模型
 */

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TeamMember;

/**
 * TeamMemberSearch represents the model behind the search form of `common\models\TeamMember`.
 */
class TeamMemberSearch extends TeamMember
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'team_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'student_no', 'role', 'work_scope'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TeamMember::find()->andWhere(['team_id' => Yii::$app->teamProvider->getId()]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'student_no', $this->student_no])
            ->andFilterWhere(['like', 'role', $this->role])
            ->andFilterWhere(['like', 'work_scope', $this->work_scope]);

        return $dataProvider;
    }
}
