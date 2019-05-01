<?php

namespace panix\mod\docs\models;

use panix\engine\traits\query\DefaultQueryTrait;
use yii\db\ActiveQuery;

class DocsQuery extends ActiveQuery
{

    use DefaultQueryTrait;

    public function excludeRoot()
    {
        // $this->addWhere(['condition' => 'id != 1']);
        $this->andWhere(['!=', 'id', 1]);
        return $this;
    }
}
