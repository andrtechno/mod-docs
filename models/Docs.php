<?php

namespace panix\mod\docs\models;

use panix\mod\docs\components\MenuArrayBehavior;
use panix\engine\behaviors\nestedsets\NestedSetsBehavior;
use panix\engine\behaviors\TranslateBehavior;
use panix\engine\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

class Docs extends ActiveRecord
{

    const MODULE_ID = 'docs';
    const route = '/admin/docs/default';

    public $tags;

    public static function find()
    {
        return new DocsQuery(get_called_class());
    }

    public function title2()
    {
        if (false) {//Yii::app()->user->isEditMode
            $html = '<form action="' . $this->getUpdateUrl() . '" method="POST">';
            $html .= '<span id="Docs[name]" class="edit_mode_title">' . $this->name . '</span>';
            $html .= '</form>';
            return $html;
        } else {
            return $this->name;
        }
    }

    public function text2()
    {
        if (false) {//Yii::app()->user->isEditMode
            $html = '<form action="' . $this->getUpdateUrl() . '" method="POST">';
            $html .= '<div id="Docs[description]" class="edit_mode_text">' . $this->description . '</div>';
            $html .= '</form>';
            return $html;
        } else {
            return $this->pageBreak('description');
        }
    }


    public function overviewImage()
    {
        if (!$this->isNewRecord) {
            if (file_exists(Yii::getPathOfAlias('webroot.uploads.categories') . '/' . $this->image) && !empty($this->image)) {
                Yii::app()->controller->widget('ext.fancybox.Fancybox', array('target' => '.overview-image'));
                return '<a href="/uploads/categories/' . $this->image . '" class="flaticon-image overview-image" title="' . $this->name . '"></a>';
            } else {
                return false;
            }
        }
    }

    public $translationClass = DocsTranslate::class;

    public function getTranslations()
    {
        return $this->hasMany($this->translationClass, ['object_id' => 'id']);
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return '{{%docs}}';
    }

    public function transactions222()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE,
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge([
            'TranslateBehavior' => [
                'class' => TranslateBehavior::class,
                'translationAttributes' => ['name', 'description']
            ],
            'MenuArrayBehavior' => array(
                'class' => MenuArrayBehavior::class,
                'labelAttr' => 'name',
                // 'countProduct'=>false,
                'urlExpression' => '["/docs/default/view", "seo_alias"=>$model->full_path]',
            ),
            'tree' => [
                'class' => NestedSetsBehavior::class,
            ],
        ], parent::behaviors());
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['seo_alias', '\panix\engine\validators\UrlValidator', 'attributeCompare' => 'name'],
            ['seo_alias', 'match',
                'pattern' => '/^([a-z0-9-])+$/i',
                'message' => Yii::t('app', 'PATTERN_URL')
            ],
            [['name', 'seo_alias'], 'trim'],
            [['name', 'seo_alias'], 'required'],
            [['name'], 'string', 'max' => 255],
            ['description', 'safe']
        ];
    }


    /**
     * @return array relational rules.
     */
    public function relations2()
    {
        return array(
            //  'countProducts' => array(self::STAT, 'ShopProductCategoryRef', 'category', 'condition' => '`t`.`switch`=1'),
            //  'manufacturer' => array(self::HAS_MANY, 'ShopManufacturer', 'cat_id'),
            'pages' => array(self::MANY_MANY, 'Docs', array('category_id' => 'id')),
            'pages2' => array(self::BELONGS_TO, 'Docs', 'id'),
            'cat_translate' => array(self::HAS_ONE, $this->translateModelName, 'object_id'),
        );
    }


    public function beforeSave($insert)
    {
        $this->rebuildFullPath();
        return parent::beforeSave($insert);
    }

    /*no this function!*/
    public function afterDelete()
    {
        $this->clearRouteCache();
        return parent::afterDelete();
    }

    public function beforeDelete()
    {
        $this->clearRouteCache();
        return parent::beforeDelete();
    }


    public function afterSave($insert, $changedAttributes)
    {
        $this->clearRouteCache();
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Rebuild category full_path
     */
    public function rebuildFullPath()
    {
        // Create category full path.
        $ancestors = $this->ancestors()
            //->addOrderBy($this->levelAttribute)
            ->all();
        if (sizeof($ancestors)) {
            // Remove root category from path
            unset($ancestors[0]);

            $parts = [];
            foreach ($ancestors as $ancestor)
                $parts[] = $ancestor->seo_alias;

            // $parts[] = $this->seo_alias;
            $this->full_path = implode('/', array_filter($parts));
        }

        return $this;
    }

    /**
     * @return array
     */
    public static function flatTree()
    {
        $result = [];
        $categories = Docs::find()->orderBy(['lft' => SORT_ASC])->all();
        array_shift($categories);

        foreach ($categories as $c) {

            if ($c->depth > 1) {
                $result[$c->id] = str_repeat('--', $c->depth - 1) . ' ' . $c->name;
            } else {
                $result[$c->id] = ' ' . $c->name;
            }
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getUrl()
    {
        return ['/docs/default/view', 'seo_alias' => $this->full_path];
    }

    public function clearRouteCache()
    {
        Yii::$app->cache->delete('DocsUrlRule');
    }

}
