Yii2 behaviour for storing model/table changelogs
===========================


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist sateler/yii2-changelog "^1.0"
```

or add

```
"sateler/yii2-changelog": "^1.0"
```

to the require section of your `composer.json` file.

Once the extension is installed, add namespace to console config and run the required migration:

```php
return [
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'sateler\changelog\migrations',
            ],
        ],
    ],
];
```

`./yii2 migrate`


Usage
-----

Once installed, enable changelog for selected models by adding the following config to the model:
```php
public function behaviors()
{
    return [
        [
            'class' => \sateler\changelog\ChangeLogBehavior::className(),
            'ignore' => [], // ignore changes on listed columns
        ],
        ...
    ];
}
```

You can review the model changelog at `http://hostname/changelog/`, or you can code your own controller
by using `sateler\changelog\models\Changelog`.
If you want to 'see' the changelogs, add the following configuration and go to `http://hostname/changelog/`:

```php
return [
    'controllerMap' => [
        'changelog' => [
            'class' => 'sateler\changelog\controllers\ChangelogController',
            'viewPath' => '@vendor/sateler/yii2-changelog/views/changelog',
            // Optional: if set, it's used in the views to create the html link for the record.
            'urlCreator' => function ($table_name, $row_id) {
                $table_name = \yii\helpers\Html::encode(str_replace('_', '-', $table_name));
                return yii\helpers\Url::to(["$table_name/view", 'id' => $row_id]);
            },
        ]
    ],
];
```

Or you can code your own controller by using the `sateler\changelog\models\Changelog` model.
