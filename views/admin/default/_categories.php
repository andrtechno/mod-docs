<?php

use panix\mod\docs\models\Docs;

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
                       onkeyup='$("#DocsTree").jstree(true).search($(this).val())'/>
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
            'allOpen' => true,
            'data' => Docs::find()->dataTree(),
            'core' => [
                "multiple" => false,
                'force_text' => true,
                'animation' => 0,
                'strings' => [
                    'Loading ...' => Yii::t('app/default', 'LOADING')
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
            var tree = $("#DocsTree").jstree(true);
            return {
                "Switch": {
                    "icon":"icon-eye",
                    "label": "' . Yii::t('app/default', 'Скрыть показать') . '",
                    "action": function (obj) {
                        $node = tree.get_node($node);
                        categorySwitch($node);
                    }
                }, 
                "Add": {
                        "icon":"icon-add",
                        "label": "' . Yii::t('app/default', 'CREATE') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                            console.log($node);
                            window.location = common.language_path+"/admin/docs/default/index?parent_id="+$node.id.replace("node_", "");
                        }
                    }, 
                    "Edit": {
                        "icon":"icon-edit",
                        "label": "' . Yii::t('app/default', 'UPDATE') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                           window.location = common.language_path+"/admin/docs/default/index?id="+$node.id.replace("node_", "");
                        }
                    },  
                    "Rename": {
                        "icon":"icon-rename",
                        "label": "' . Yii::t('app/default', 'RENAME') . '",
                        "action": function (obj) {
                            console.log($node);
                            tree.edit($node);
                        }
                    },                         
                    "Remove": {
                        "icon":"icon-trashcan",
                        "label": "' . Yii::t('app/default', 'DELETE') . '",
                        "action": function (obj) {
                            if (confirm("' . Yii::t('app/default', 'DELETE_CONFIRM') . '")) {
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



