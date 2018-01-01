<?php

use yii\helpers\Html;
use yii\grid\GridView;
use sateler\changelog\models\Changelog;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChangelogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Changelogs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="changelog-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('export', ['export', 'ChangelogSearch' => Yii::$app->request->get('ChangelogSearch'), 'grouped' => Yii::$app->request->get('grouped')], [
            'class' => 'btn btn-primary',
            'data-mehtod' => 'post',
        ]) ?>
    </p>
    <p class="pull-right">
        <?= Html::checkbox('grouped', $grouped, [
            'label' => 'Grouped',
            'onClick' => 'window.location = "'.Url::current(['grouped' => !$grouped + 0]).'";',
        ]) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'visible' => !$grouped,
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->id, ['view', 'id' => $model->id]);
                },
            ],
            [
                'attribute' => 'change_uuid',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->change_uuid, ['index', 'ChangelogSearch' => ['change_uuid' => $model->change_uuid]]);
                },
            ],
            [
                'attribute' => 'change_type',
                'filter' => Changelog::$types,
                'value' => 'change_type_name',
            ],
            [
                'attribute' => 'created_at',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_range',
                    'startAttribute' => 'date_start',
                    'endAttribute' => 'date_end',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                        ],
                    ],
                    'presetDropdown' => true,
                ]),
                'format' => 'datetime',
            ],
            'user_id',
            'table_name',
            'row_id',
            [
                'attribute' => 'column_name',
                'visible' => !$grouped,
            ],
            [
                'attribute' => 'old_value',
                'visible' => !$grouped,
            ],
            [
                'attribute' => 'new_value',
                'visible' => !$grouped,
            ],
        ],
    ]); ?>

</div>
