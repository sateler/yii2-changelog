<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Changelog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Changelogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="changelog-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'change_uuid',
            'change_type_name',
            'created_at:datetime',
            'user_id',
            'table_name',
            'column_name',
            'row_id',
            'old_value',
            'new_value',
        ],
    ]) ?>

</div>
