<?php

namespace panix\mod\docs;

use Yii;
use panix\engine\Html;
use panix\engine\WebModule;
use yii\base\BootstrapInterface;

class Module extends WebModule implements BootstrapInterface
{

    public $tegRoute = 'docs/default/index';
    public $icon = 'books';
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            ['class' => 'panix\mod\docs\components\DocsUrlRule'],
        ], true);

    }

    public function getAdminMenu()
    {
        return [
            'modules' => [
                'items' => [
                    [
                        'label' => Yii::t('docs/default', 'MODULE_NAME'),
                        'url' => ['/admin/docs'],
                        'icon' => $this->icon,
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
            'icon' => $this->icon,
            'description' => Yii::t('docs/default', 'MODULE_DESC'),
            'url' => ['/admin/docs'],
        ];
    }
}
