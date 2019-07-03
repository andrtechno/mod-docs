<?php

namespace panix\mod\docs\models;

use yii\db\ActiveRecord;

/**
 * Class DocsTranslate
 * @package panix\mod\docs\models
 */
class DocsTranslate extends ActiveRecord
{

    public static $translationAttributes = ['name', 'description'];

    public static function tableName()
    {
        return '{{%docs_translate}}';
    }


}