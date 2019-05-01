<?php

namespace panix\mod\docs;

use panix\engine\web\AssetBundle;

/**
 * Class CategoryAsset
 * @package panix\mod\docs
 */
class CategoryAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'tree.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
