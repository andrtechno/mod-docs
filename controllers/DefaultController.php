<?php

namespace panix\mod\docs\controllers;

use panix\mod\docs\models\Docs;
use panix\mod\docs\models\DocsSearch;
use panix\engine\controllers\WebController;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;

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
     * @var Docs
     */
    public $model;

    public function actionIndex()
    {

        $searchModel = new DocsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());


$data=[];
        $dirs = FileHelper::findDirectories(Yii::getAlias('@docs/views/default/guides'),['recursive'=>true]);
        foreach ($dirs as $dir){
            $data[basename($dir)]['files']=[];

            if(file_exists($dir.'/config.json')){
            $config= Json::decode(file_get_contents($dir.'/config.json'));
            $data[basename($dir)]['info']=$config;
            }

         //   echo $dir;
          //  echo '<br>';
            $files = FileHelper::findFiles($dir,['recursive'=>false,'only'=>['*.php']]);
           // print_r($files);
           // $data[basename($dir)]['files']=[];
            foreach ($files as $file){
                $data[basename($dir)]['files'][]=basename($file);
               // echo $file;
              //  echo '<br>';
            }
           // $files = FileHelper::findFiles(Yii::getAlias("@docs/views/default/guides"),['recursive'=>true]);
           // print_r($files);die;
        }



        VarDumper::dump($data,10,true);
        $files = FileHelper::findFiles(Yii::getAlias("@docs/views/default/guides/manual"),['recursive'=>true]);
        // print_r($files);die;


        die;
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($seo_alias)
    {
        $this->findModel($seo_alias);

        $this->pageName = Yii::t('docs/default', 'MODULE_NAME');
        $this->view->title = $this->model->name;
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
