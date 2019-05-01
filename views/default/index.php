<?php
use panix\mod\docs\widgets\categories\CategoriesWidget;
use yii\widgets\ListView;

?>
<?php echo CategoriesWidget::widget([]) ?>
<?php
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_list',
    'layout' => '{summary}{items}{pager}',
    'emptyText' => 'Empty',
    'options' => ['class' => 'row list-view'],
    'itemOptions' => ['class' => 'item'],
    'emptyTextOptions' => ['class' => 'alert alert-info']
]);
?>
