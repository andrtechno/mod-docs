<?php

use panix\mod\docs\models\Docs;
use panix\mod\docs\components\CategoryNode;

\panix\mod\docs\CategoryAsset::register($this);


?>

<div class="card">
    <div class="card-header">
        <h5>asd</h5>
    </div>
    <div class="card-body">
        <div class="form-group mt-3">
            <div class="col-12">
                <input class="form-control" placeholder="Поиск..." type="text"
                       onkeyup='$("#jsTree_DocsTree").jstree(true).search($(this).val())'/>
            </div>
        </div>
        <div class="col-12">
            <div class="alert alert-info">
                <?= Yii::t('app/admin', "USE_DND"); ?>
            </div>
        </div>
        <?php

        echo \panix\ext\jstree\JsTree::widget([
            'id' => 'DocsTree',
            'name' => 'jstree',
            'allOpen' => true,
            'data' => CategoryNode::fromArray(Docs::find()->roots()->all(), ['switch' => true]),
            'core' => [
                "multiple" => false,
                'force_text' => true,
                'animation' => 0,
                'strings' => [
                    'Loading ...' => Yii::t('app', 'LOADING')
                ],
                "themes" => [
                    "stripes" => true,
                    'responsive' => true,
                    "variant" => "large"
                ],
                'check_callback' => true
            ],
            'plugins' => ['dnd', 'contextmenu', 'search'],
            'contextmenu' => [
            'items' => new yii\web\JsExpression('function($node) {
            var tree = $("#jsTree_DocsTree").jstree(true);
            return {
                "Switch": {
                    "icon":"icon-eye",
                    "label": "' . Yii::t('app', 'Скрыть показать') . '",
                    "action": function (obj) {
                        $node = tree.get_node($node);
                        categorySwitch($node);
                    }
                }, 
                "Add": {
                        "icon":"icon-add",
                        "label": "' . Yii::t('app', 'CREATE') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                            console.log($node);
                            window.location = "/admin/docs/default/index?parent_id="+$node.id.replace("node_", "");
                        }
                    }, 
                    "Edit": {
                        "icon":"icon-edit",
                        "label": "' . Yii::t('app', 'UPDATE') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                           window.location = "/admin/docs/default/index?id="+$node.id.replace("node_", "");
                        }
                    },  
                    "Rename": {
                        "icon":"icon-rename",
                        "label": "' . Yii::t('app', 'RENAME') . '",
                        "action": function (obj) {
                            console.log($node);
                            tree.edit($node);
                        }
                    },                         
                    "Remove": {
                        "icon":"icon-trashcan",
                        "label": "' . Yii::t('app', 'DELETE') . '",
                        "action": function (obj) {
                            if (confirm("' . Yii::t('app', 'DELETE_CONFIRM') . '")) {
                                tree.delete_node($node);
                            }
                        }
                    }
                };
      }')
            ]
        ]);
        ?>
    </div>
</div>



