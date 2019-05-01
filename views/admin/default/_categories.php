<?php

\panix\mod\docs\CategoryAsset::register($this);

?>
<div class="form-group">
    <div class="col-12">
        <input class="form-control" placeholder="Поиск..." type="text"
               onkeyup='$("#jsTree_DocsTree").jstree(true).search($(this).val())'/>
    </div>
</div>

<?php
echo Yii::t('app', "Используйте 'drag-and-drop' для сортировки категорий.");
?>

<?php

echo \panix\ext\jstree\JsTree::widget([
    'id' => 'DocsTree',
    'name' => 'jstree',
    'allOpen' => true,
    'data' => \panix\mod\docs\components\CategoryNode::fromArray(\panix\mod\docs\models\Docs::findOne(1)->children()->all(), ['switch' => true]),
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
                            window.location = "/admin/docs/default/create?parent_id="+$node.id.replace("node_", "");
                        }
                    }, 
                    "Edit": {
                        "icon":"icon-edit",
                        "label": "' . Yii::t('app', 'UPDATE') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                           window.location = "/admin/docs/default/update?id="+$node.id.replace("node_", "");
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




