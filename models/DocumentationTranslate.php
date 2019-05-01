<?php

namespace panix\mod\docs\models;

use yii\db\ActiveRecord;

class DocumentationTranslate extends ActiveRecord
{


    public static function tableName()
    {
        return '{{%documentation_translate}}';
    }


}