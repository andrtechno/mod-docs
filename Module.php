<?php

namespace panix\mod\docs;

use Yii;
use panix\engine\Html;
use panix\engine\WebModule;

class Module extends WebModule
{

    public $tegRoute = 'documentation/default/index';

    public $routes = [
        /*'documentation/tag/<tag:.*?>' => 'documentation/default/index',*/
        ['class' => 'panix\mod\docs\components\DocsUrlRule'],
        //'documentation' => 'documentation/default/index',
    ];


    public function getAdminMenu()
    {
        return [
            'modules' => [
                'items' => [
                    [
                        'label' => 'docs',
                        'url' => ['/admin/docs'],
                        'icon' => Html::icon('icon'),
                    ]
                ]
            ]
        ];
    }

    public function getAdminSidebar2()
    {
        $items = $this->getAdminMenu();
        return $items['modules']['items'][0]['items'];
    }

    public function getInfo()
    {
        return [
            'label' => Yii::t('docs/default', 'MODULE_NAME'),
            'author' => 'andrew.panix@gmail.com',
            'version' => '1.0',
            'icon' => 'icon-documentation',
            'description' => Yii::t('docs/default', 'MODULE_DESC'),
            'url' => ['/admin/docs'],
        ];
    }
}
