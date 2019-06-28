<?php

namespace panix\mod\docs\models;

use panix\engine\behaviors\nestedsets\NestedSetsQueryBehavior;
use panix\engine\traits\query\DefaultQueryTrait;
use panix\mod\admin\models\query\TagQueryBehavior;
use yii\db\ActiveQuery;

/**
 * Class DocsQuery
 * @package panix\mod\docs\models
 */
class DocsQuery extends ActiveQuery
{

    use DefaultQueryTrait;

    public function behaviors()
    {
        return [
            [
                'class' => NestedSetsQueryBehavior::class,
            ],
            [
                'class' => TagQueryBehavior::class,
            ]
        ];
    }

    public function excludeRoot()
    {
        // $this->addWhere(['condition' => 'id != 1']);
        $this->andWhere(['!=', 'id', 1]);
        return $this;
    }
}
