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

    // public $aliasPathImage = 'uploads.shop.categories';

    public $tags;
    public static function find() {
        return new DocsQuery(get_called_class());
    }

    public function title()
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

    public function text()
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

    public function getForm()
    {
        Yii::import('ext.TagInput');
        Yii::import('ext.tinymce.TinymceArea');
        return array(
            'attributes' => array(
                'id' => __CLASS__,
                'class' => 'form-horizontal',
                'enctype' => 'multipart/form-data',
            ),
            'elements' => array(
                'content' => array(
                    'type' => 'form',
                    'title' => Yii::t('admin', 'Общая информация'),
                    'elements' => array(
                        'name' => array(
                            'type' => 'text',
                            'id' => 'title',
                        ),
                        'seo_alias' => array(
                            'type' => 'text',
                            'id' => 'alias',
                            'visible' => (Yii::app()->settings->get('app', 'translate_object_url')) ? false : true
                        ),
                        'description' => array(
                            'type' => 'TinymceArea',
                        ),
                        'tags' => array(
                            'type' => 'TagInput',
                            'options' => array()
                        ),
                    ),
                ),
            ),
            'buttons' => array(
                'submit' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-success',
                    'label' => ($this->isNewRecord) ? Yii::t('app', 'CREATE', 0) : Yii::t('app', 'SAVE')
                )
            )
        );
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

    public function getTranslations()
    {
        return $this->hasMany(DocsTranslate::class, ['object_id' => 'id']);
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return '{{%docs}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE,
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge([
            'translate' => [
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
                // 'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                //'levelAttribute' => 'level',
            ],
        ], parent::behaviors());
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules2()
    {
        return array(
            array('name', 'required'),
            array('name', 'StripTagsValidator'),
            array('seo_alias', 'TranslitValidator', 'translitAttribute' => 'name'),
            array('name, seo_alias, image', 'length', 'max' => 255),
            array('description, tags', 'type', 'type' => 'string'),
            // Search
            array('id, name, seo_alias', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors2()
    {
        return array(
            'tags' => array(
                'class' => 'app.behaviors.TagsBehavior',
                'router' => '/docs/default/index'
            ),
            'seo' => array(
                'class' => 'mod.seo.components.SeoBehavior',
                'url' => $this->getUrl()
            ),
            'NestedSetBehavior' => array(
                'class' => 'app.behaviors.NestedSetBehavior',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'levelAttribute' => 'level',
            ),
            'MenuArrayBehavior' => array(
                'class' => 'mod.docs.components.MenuArrayBehavior',
                'labelAttr' => 'name',
                // 'countProduct'=>false,
                'urlExpression' => 'array("/docs/default/view", "seo_alias"=>$model->full_path)',
            ),
            'TranslateBehavior' => array(
                'class' => 'app.behaviors.TranslateBehavior',
                'relationName' => 'cat_translate',
                'translateAttributes' => array(
                    'name',
                    'description',
                ),
            ),
        );
    }

    /**
     * Find category by url.
     * Scope.
     *
     * @param string $url
     * @param string $alias
     * @return docs
     */
    public function withUrl($url, $alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias . '.seo_alias=:url',
            'params' => array(':url' => $url)
        ));
        return $this;
    }

    /**
     * Find category by url.
     * Scope.
     *
     * @param string $url
     * @param string $alias
     * @return Product
     */
    public function withFullPath($url, $alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias . '.full_path=:url',
            'params' => array(':url' => $url)
        ));
        return $this;
    }

    /**
     * @param $alias
     * @return documentation
     */
    public function excludeRoot($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias . '.id != 1',
        ));
        return $this;
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

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels2()
    {
        $this->_attrLabels = array(
            'name' => Yii::t('app', 'Название'),
            'full_path' => Yii::t('app', 'Полный путь'),
            'description' => Yii::t('app', 'Описание'),
        );
        return CMap::mergeArray($this->_attrLabels, parent::attributeLabels());
    }

    public function beforeSave2()
    {
        if (empty($this->seo_alias)) {
            // Create slug
            // Yii::import('ext.SlugHelper.SlugHelper');
            // $this->seo_alias = SlugHelper::run($this->name);
        }

        // Check if url available
        if ($this->isNewRecord) {
            $test = Docs::model()
                ->withUrl($this->seo_alias)
                ->count();
        } else {
            $test = Docs::model()
                ->withUrl($this->seo_alias)
                ->count('id!=:id', array(':id' => $this->id));
        }

        // Create unique url
        if ($test > 0)
            $this->seo_alias .= '-' . date('YmdHis');

        $this->rebuildFullPath();

        return parent::beforeSave();
    }

    public function afterDelete2()
    {

        $this->clearRouteCache();

        return parent::afterDelete();
    }

    public function afterSave2()
    {
        $this->clearRouteCache();

        return parent::afterSave();
    }

    /**
     * Rebuild category full_path
     */
    public function rebuildFullPath()
    {
        // Create category full path.
        $ancestors = $this->ancestors()->language(Yii::app()->languageManager->active->code)->findAll();
        if (sizeof($ancestors)) {
            // Remove root category from path
            unset($ancestors[0]);

            $parts = array();
            foreach ($ancestors as $ancestor)
                $parts[] = $ancestor->seo_alias;

            $parts[] = $this->seo_alias;
            $this->full_path = implode('/', array_filter($parts));
        }

        return $this;
    }

    /**
     * @return array
     */
    public static function flatTree()
    {
        $result = array();
        $categories = Docs::model()
            ->published()
            ->language(Yii::app()->languageManager->active->code)
            ->findAll(array('order' => 'lft'));
        array_shift($categories);

        foreach ($categories as $c) {

            if ($c->level > 2) {
                $result[$c->id] = str_repeat('--', $c->level - 1) . ' ' . $c->name;
            } else {
                $result[$c->id] = ' ' . $c->name;
            }
        }

        return $result;
    }



    /**
     * @return string
     */
    public function getUrl()
    {
        $url = ['/docs/default/view', 'seo_alias' => $this->full_path];
        return $url;
    }

    public function clearRouteCache()
    {
        Yii::app()->cache->delete('DocsUrlRule');
    }

}
