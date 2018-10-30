<?php

namespace sateler\changelog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use DateTimeImmutable;
use DateTimeZone;
use DateInterval;

/**
 * ChangelogSearch represents the model behind the search form about `common\models\Changelog`.
 */
class ChangelogSearch extends Changelog
{
    public $date_range;
    public $date_start;
    public $date_end;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'user_id', 'row_id'], 'integer'],
            [['change_uuid', 'change_type', 'table_name', 'column_name', 'old_value', 'new_value'], 'safe'],
            [['date_range', 'date_start', 'date_end'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
    public function search($params, $grouped = false)
    {
        $query = Changelog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    $grouped?'created_at':'id' => SORT_DESC,
                ],
                'attributes' => [
                    $grouped?'':'id',
                    'change_uuid',
                    'change_type',
                    'created_at',
                    'user_id',
                    'table_name',
                    $grouped?'':'column_name',
                    'row_id',
                ]
            ],
        ]);

        if ($this->load($params) && !$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'change_uuid' => $this->change_uuid,
            'change_type' => $this->change_type,
            'table_name' => $this->table_name,
            'column_name' => $this->column_name,
            'user_id' => $this->user_id,
            'row_id' => $this->row_id,
        ]);

        $query->andFilterWhere(['like', 'old_value', $this->old_value])
            ->andFilterWhere(['like', 'new_value', $this->new_value]);
        
        $this->filterDates($query);
        
        if($grouped) {
            $columns = [
                'change_uuid',
                'change_type',
                'created_at',
                'user_id',
                'table_name',
                'row_id',
            ];
            $query->groupBy($columns);
            $query->select($columns);
        }

        return $dataProvider;
    }
    
    private function filterDates(\yii\db\ActiveQuery $query)
    {
        $date_start = $date_end = null;
        if($this->date_start) {
            $date_start = new DateTimeImmutable($this->date_start." 00:00:00", new DateTimeZone("America/Santiago"));
            $query->andWhere(['>=', 'created_at', $date_start->getTimestamp()]);
        }
        if($this->date_end) {
            $date_end = new DateTimeImmutable($this->date_end." 23:59:59", new DateTimeZone("America/Santiago"));
            $query->andWhere(['<=', 'created_at', $date_end->getTimestamp()]);
        }
    }
}
