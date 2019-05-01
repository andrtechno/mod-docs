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

class m190501_042437_docs extends Migration {

    // Use up()/down() to run migration code without a transaction.
    public function up() {
        $this->createTable(Docs::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'tree' => $this->integer()->null(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'seo_alias' => $this->string(255)->null()->defaultValue(null),
            'full_path' => $this->string(255)->null(),
            'image' => $this->string(50),
            'switch' => $this->boolean()->defaultValue(1),
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
    }

    public function down() {
        echo "m190501_042437_docs cannot be reverted.\n";
        $this->dropTable(Docs::tableName());
        $this->dropTable(DocsTranslate::tableName());
        return false;
    }

}
