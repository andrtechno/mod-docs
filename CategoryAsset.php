<?php
/**
 *
 * @author Pixelion CMS <dev@pixelion.com.ua>
 * @link http://www.pixelion.com.ua/
 */
namespace panix\mod\docs;



use panix\engine\web\AssetBundle;

class CategoryAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/../../assets/admin';
    public $js = [
        'js/category.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
