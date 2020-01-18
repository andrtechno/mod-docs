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
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $model
         */
        $model = Docs::findModel(Yii::$app->request->get('id'));

        $this->pageName = Yii::t('docs/default', 'MODULE_NAME');
        $this->breadcrumbs = array(
            $this->pageName
        );

        $this->buttons[] = [
            'label' => 'Просмотр модуля',
            'url' => ['/docs'],
            'options'=>['target'=>'_blank']
        ];

        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
       //   print_r($model->tagValues);die;
            if ($model->getIsNewRecord()) {
                if (Yii::$app->request->get('parent_id')) {
                    $parent_id = Docs::findModel(Yii::$app->request->get('parent_id'));
                    $model->appendTo($parent_id);
                } else {
                    $model->saveNode();
                }

                return $this->redirect(['index']);
            } else {
                $model->saveNode();
            }

        }
        $this->pageName = ($model->isNewRecord) ? $model::t('PAGE_CREATE') :
            $model::t('PAGE_UPDATE');

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
            $model->slug = CMS::slug($model->name);
            if ($model->validate()) {
                $model->saveNode();
                $message = Yii::t('docs/default', 'TREE_RENAME');
            } else {
                $message = $model->getError('slug');
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
        $model->slug = CMS::slug($model->name);
        if ($model->validate()) {
            $model->appendTo($parent);
            $message = Yii::t('app/default', 'TREE_CREATE');
        } else {
            $message = $model->getError('slug');
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
        //    $test->rebuildFullPath()->saveNode(false);


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
        /** @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Docs $model */
        $model = new Docs();
        $model::getDb()->createCommand()->truncateTable(Docs::tableName())->execute();
        $model::getDb()->createCommand()->truncateTable(DocsTranslate::tableName())->execute();
        $model->name = 'Документация';
        $model->tree = 1;
        $model->lft = 1;
        $model->rgt = 2;
        $model->depth = 0;
        $model->slug = 'root';
        $model->full_path = '';
        $model->saveNode();
        return $this->redirect(['index']);
    }

}
