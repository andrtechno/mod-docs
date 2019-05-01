<?php

namespace panix\mod\docs\models;

use yii\db\ActiveRecord;

class DocsTranslate extends ActiveRecord
{


    public static function tableName()
    {
        return '{{%docs_translate}}';
    }


}