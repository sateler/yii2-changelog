<?php

namespace sateler\changelog\controllers;

use Yii;
use sateler\changelog\models\Changelog;
use sateler\changelog\models\ChangelogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii2tech\csvgrid\CsvGrid;

/**
 * ChangelogController implements the CRUD actions for Changelog model.
 */
class ChangelogController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Changelog models.
     * @return mixed
     */
    public function actionIndex($grouped = false)
    {
        $searchModel = new ChangelogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $grouped);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'grouped' => $grouped,
        ]);
    }

    /**
     * Lists all Changelog models.
     * @return mixed
     */
    public function actionExport($grouped = false)
    {
        $searchModel = new ChangelogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $grouped);

        $exporter = new CsvGrid([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'id',
                    'visible' => !$grouped,
                ],
                'change_uuid',
                'change_type_name',
                'created_at',
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
        ]);
        return $exporter->export()->send("changelog.csv" );
    }

    /**
     * Displays a single Changelog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Changelog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Changelog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Changelog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
