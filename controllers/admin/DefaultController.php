<?php

namespace panix\mod\docs\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\docs\models\Docs;

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


        if (strpos($_GET['id'], 'j1_') === false) {
            $id = $_GET['id'];
        } else {
            $id = str_replace('j1_', '', $_GET['id']);
        }

        $model = Docs::findOne((int)$id);
        if ($model) {
            $model->name = $_GET['text'];
            $model->seo_alias = CMS::translit($model->name);
            if ($model->validate()) {
                $model->saveNode(false, false);
                $message = Yii::t('admin', 'CATEGORY_TREE_RENAME');
            } else {
                $message = $model->getError('seo_alias');
            }
            echo CJSON::encode(array(
                'message' => $message
            ));
            Yii::app()->end();
        }
    }


    public function actionCreateNode()
    {
        $model = new Docs;
        $parent = Docs::model()->findByPk($_GET['parent_id']);

        $model->name = $_GET['text'];
        $model->seo_alias = CMS::translit($model->name);
        if ($model->validate()) {
            $model->appendTo($parent);
            $message = Yii::t('admin', 'CATEGORY_TREE_CREATE');
        } else {
            $message = $model->getError('seo_alias');
        }
        echo CJSON::encode(array(
            'message' => $message
        ));
        Yii::app()->end();

    }

    /**
     * Drag-n-drop nodes
     */
    public function actionMoveNode()
    {
        $node = Docs::model()->findByPk($_GET['id']);
        $target = Docs::model()->findByPk($_GET['ref']);

        if ((int)$_GET['position'] > 0) {
            $pos = (int)$_GET['position'];
            $childs = $target->children()->findAll();
            if (isset($childs[$pos - 1]) && $childs[$pos - 1] instanceof Docs && $childs[$pos - 1]['id'] != $node->id)
                $node->moveAfter($childs[$pos - 1]);
        } else
            $node->moveAsFirst($target);

        $node->rebuildFullPath()->saveNode(false);
    }

    /**
     * Redirect to category front.
     */
    public function actionRedirect()
    {
        $node = Docs::model()->findByPk($_GET['id']);
        $this->redirect($node->getViewUrl());
    }

    public function actionSwitchNode()
    {
        //$switch = $_GET['switch'];
        $node = Docs::model()->findByPk($_GET['id']);
        $node->switch = ($node->switch == 1) ? 0 : 1;
        $node->saveNode();
        echo CJSON::encode(array(
            'switch' => $node->switch,
            'message' => Yii::t('Module.default', 'CATEGORY_TREE_SWITCH', $node->switch)
        ));
        Yii::app()->end();
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionDelete($id)
    {
        $model = Docs::model()->findByPk($id);

        //Delete if not root node
        if ($model && $model->id != 1) {
            foreach (array_reverse($model->descendants()->findAll()) as $subCategory) {
                $subCategory->deleteNode();
            }
            $model->deleteNode();
        }
    }

    //TODO need multi language add and test
    public function actionCreateRoot()
    {
        $model = new Docs();
        $model->name = 'Документация';
        $model->lft = 1;
        $model->rgt = 2;
        $model->depth = 1;
        $model->seo_alias = 'root';
        $model->full_path = '';
        $model->image = NULL;
        $model->switch = 1;
        $model->saveNode();
        return $this->redirect(['create']);
    }

}
