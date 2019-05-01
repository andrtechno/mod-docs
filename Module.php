<?php

namespace panix\mod\docs;

use Yii;
use panix\engine\Html;
use panix\engine\WebModule;
use yii\base\BootstrapInterface;

class Module extends WebModule implements BootstrapInterface
{

    public $tegRoute = 'documentation/default/index';

    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            ['class' => 'panix\mod\docs\components\CategoryUrlRule'],
        ], true);

    }

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
