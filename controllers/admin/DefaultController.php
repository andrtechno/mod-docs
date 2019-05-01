<?php

namespace panix\mod\docs\controllers\admin;

use panix\engine\CMS;
use panix\mod\docs\models\DocsTranslate;
use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\docs\models\Docs;
use yii\web\Response;

class DefaultController extends AdminController
{


    public function actionIndex()
    {
        $this->pageName = Yii::t('docs/default', 'MODULE_NAME');
        $this->breadcrumbs = array(

            $this->pageName
        );
        $this->actionUpdate(true);
    }

    public function actionUpdate($id = false)
    {
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $model
         */
        $model = Docs::findModel($id);

        $post = Yii::$app->request->post();


        if ($model->load($post) && $model->validate()) {

            if (Yii::$app->request->get('parent_id')) {
                $parent_id = Docs::findModel(Yii::$app->request->get('parent_id'));
            } else {
                $parent_id = Docs::findModel(1);
            }
            if ($model->getIsNewRecord()) {
                $model->appendTo($parent_id);
                return $this->redirect(['create']);
            } else {
                $model->saveNode();
            }

        }
        $this->pageName = ($model->isNewRecord) ? Yii::t('app', 'Создание категории') :
            Yii::t('app', 'Редактирование категории');

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    public function actionRenameNode()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (strpos(Yii::$app->request->get('id'), 'j1_') === false) {
            $id = Yii::$app->request->get('id');
        } else {
            $id = str_replace('j1_', '', Yii::$app->request->get('id'));
        }
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $model
         */
        $model = Docs::findOne((int)$id);
        if ($model) {
            $model->name = Yii::$app->request->get('text');
            $model->seo_alias = CMS::slug($model->name);
            if ($model->validate()) {
                $model->saveNode();
                $message = Yii::t('docs/default', 'TREE_RENAME');
            } else {
                $message = $model->getError('seo_alias');
            }


        }
        return [
            'message' => $message
        ];
    }


    public function actionCreateNode()
    {
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $model
         */
        $model = new Docs;
        $parent = Docs::findOne($_GET['parent_id']);

        $model->name = $_GET['text'];
        $model->seo_alias = CMS::slug($model->name);
        if ($model->validate()) {
            $model->appendTo($parent);
            $message = Yii::t('app', 'TREE_CREATE');
        } else {
            $message = $model->getError('seo_alias');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => $message
        ];
    }

    /**
     * Drag-n-drop nodes
     */
    public function actionMoveNode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $node
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $target
         */
        $node = Docs::findModel(Yii::$app->request->get('id'));
        $target = Docs::findModel(Yii::$app->request->get('ref'));
        $test = $node;
        $status = false;
        $message = '';
        if ((int)$_GET['position'] > 0) {
            $pos = (int)$_GET['position'];
            $childs = $target->children()->all();

            if (isset($childs[$pos - 1]) && $childs[$pos - 1]->id != $node->id) { // && $childs[$pos - 1]->id != $node->id
                $node->moveAfter($childs[$pos - 1]);
                $status = true;
                $message = 'moveAfter';
            } else {
                $message = 'err';
               // echo count($childs);
            }
        } else {
            $message = 'moveAsFirst';
            $status = true;
            $node->moveAsLast($target);
        }
        //if($status)
        $message.=$node->rebuildFullPath()->full_path;

                //->saveNode(false);


        return [
            'status' => $status,
            'message' => $message
        ];



    }

    /**
     * Redirect to category front.
     */
    public function actionRedirect()
    {
        $node = Docs::findOne(Yii::$app->request->get('id'));
        return $this->redirect($node->getViewUrl());
    }

    public function actionSwitchNode()
    {
        //$switch = $_GET['switch'];
        /** @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $node */
        $node = Docs::findOne(Yii::$app->request->get('id'));
        $node->switch = ($node->switch == 1) ? 0 : 1;
        $node->saveNode();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'switch' => $node->switch,
            'message' => Yii::t('docs/default', ($node->switch) ? 'TREE_SWITCH_ON' : 'TREE_SWITCH_OFF')
        ];

    }

    /**
     * @param $id
     */
    public function actionDelete($id)
    {
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $model
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $subCategory
         */
        $model = Docs::findModel($id);

        //Delete if not root node
        if ($model && $model->id != 1) {
            foreach (array_reverse($model->descendants()->all()) as $subCategory) {
                $subCategory->deleteNode();
            }
            $model->deleteNode();
        }
    }

    //TODO need multi language add and test
    public function actionCreateRoot()
    {
        $model = new Docs();
        $model::getDb()->createCommand()->truncateTable(Docs::tableName())->execute();
        $model::getDb()->createCommand()->truncateTable(DocsTranslate::tableName())->execute();
        $model->name = 'Документация';
        $model->lft = 1;
        $model->rgt = 2;
        $model->depth = 1;
        $model->seo_alias = 'root';
        $model->full_path = '';
        $model->switch = 1;
        $model->saveNode();
        return $this->redirect(['create']);
    }

}
