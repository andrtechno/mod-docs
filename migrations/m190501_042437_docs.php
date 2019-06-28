<?php

namespace panix\mod\docs\migrations;

/**
 * Generation migrate by PIXELION CMS
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 *
 * Class m190501_042437_docs
 */

use panix\mod\docs\models\Docs;
use panix\engine\db\Migration;
use panix\mod\docs\models\DocsTranslate;

class m190501_042437_docs extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable(Docs::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'tree' => $this->integer()->unsigned(),
            'lft' => $this->integer()->notNull()->unsigned(),
            'rgt' => $this->integer()->notNull()->unsigned(),
            'depth' => $this->smallInteger(5)->notNull()->unsigned(),
            'slug' => $this->string(255)->null()->defaultValue(null),
            'full_path' => $this->string(255)->null(),
            'switch' => $this->boolean()->defaultValue(1),
            'created_at' => $this->integer(11)->null(),
            'updated_at' => $this->integer(11)->null(),
        ], $this->tableOptions);


        $this->createTable(DocsTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->null()->defaultValue(null),
        ], $this->tableOptions);

        $this->createIndex('lft', Docs::tableName(), 'lft');
        $this->createIndex('rgt', Docs::tableName(), 'rgt');
        $this->createIndex('depth', Docs::tableName(), 'depth');
        $this->createIndex('full_path', Docs::tableName(), 'full_path');


        $this->createIndex('object_id', DocsTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', DocsTranslate::tableName(), 'language_id');

        //create root
        $this->batchInsert(Docs::tableName(), ['lft', 'rgt', 'depth', 'slug', 'full_path', 'switch'], [
            [1, 2, 1, 'root', '', 1]
        ]);

        $this->batchInsert(DocsTranslate::tableName(), ['object_id', 'language_id', 'name', 'text'], [
            [1, 1, 'Документация', '']
        ]);

    }

    public function down()
    {
        echo "m190501_042437_docs cannot be reverted.\n";
        $this->dropTable(Docs::tableName());
        $this->dropTable(DocsTranslate::tableName());
        return false;
    }

}
