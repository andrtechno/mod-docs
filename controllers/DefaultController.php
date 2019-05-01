<?php

namespace panix\mod\docs\controllers;

use panix\mod\docs\models\Docs;
use panix\mod\docs\models\DocsSearch;
use panix\engine\controllers\WebController;
use Yii;

class DefaultController extends WebController
{

    public function actionSuggestTags()
    {
        if (isset($_GET['q']) && ($keyword = trim($_GET['q'])) !== '') {
            $tags = Tag::model()->suggestTags($keyword);
            if ($tags !== array())
                echo implode("\n", $tags);
        }
    }

    /**
     * @var Product
     */
    public $query;

    /**
     * @var Docs
     */
    public $model;

    /**
     * @var ActiveDataProvider
     */
    public $provider;

    public function actionIndex()
    {

        $searchModel = new DocsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($seo_alias)
    {
        $this->findModel($seo_alias);

        $this->pageName = Yii::t('docs/default', 'MODULE_NAME');

        //$ancestors = $this->model->excludeRoot()->ancestors()->findAll();
        //$this->breadcrumbs = array(Yii::t('documentation/default', 'MODULE_NAME') => array('/documentation'));
        //foreach ($ancestors as $c)
        //    $this->breadcrumbs[$c->name] = $c->getUrl();

        //$this->breadcrumbs[] = $this->model->name;


        return $this->render('view', ['model' => $this->model]);
    }


    /**
     * @param $seo_alias
     * @return array|null|Docs|\yii\db\ActiveRecord
     */
    protected function findModel($seo_alias)
    {
        $model = new Docs;
        if (($this->model = $model::find()->where(['full_path' => $seo_alias])->one()) !== null) {
            return $this->model;
        } else {
            $this->error404();
        }
    }

}
