<?php

namespace panix\mod\docs\widgets\categories;

use Yii;
use panix\engine\Html;
use panix\mod\docs\models\Docs;
use panix\engine\data\Widget;

class CategoriesWidget extends Widget
{

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $model = Docs::find()->dataTree();
        return $this->render($this->skin, ['model' => $model]);
    }

    public function recursive($data, $i = 0)
    {
        $html = '';
        if (isset($data)) {
            foreach ($data as $obj) {

                $i++;
                $iconClass = (isset($obj['folder'])) ? 'folder-open' : '';
                if (Yii::$app->request->get('slug') && stripos(Yii::$app->request->get('slug'), $obj['url']) !== false) {
                    $ariaExpanded = 'true';
                    $collapseClass = 'collapse in ';
                } else {
                    $ariaExpanded = 'false';
                    $collapseClass = 'collapse ';
                }

                if (Yii::$app->request->get('slug')) {
                    $activeClass = ($obj['url'] === '/docs/' . Yii::$app->request->get('slug')) ? 'active' : '';
                } else {
                    $activeClass = '';
                }

                if (isset($obj['children'])) {
                    $html .= Html::a($obj['title'], '#collapse' . $obj['key'], array(
                        'data-toggle' => 'collapse',
                        'aria-expanded' => $ariaExpanded,
                        'aria-controls' => 'collapse' . $obj['key'],
                        'class' => "nav-link collapsed {$activeClass} {$iconClass}"
                    ));
                    $html .= Html::beginTag('div', ['class' => $collapseClass, 'id' => 'collapse' . $obj['key']]);
                    $html .= $this->recursive($obj['children'], $i);

                    $html .= Html::endTag('div');
                } else {
                    $html .= Html::a($obj['title'], Yii::$app->urlManager->createUrl($obj['url']), ['class' => "nav-link {$activeClass} {$iconClass}"]);
                }
            }
        }
        return $html;
    }


}
