<?php
use panix\mod\docs\models\Docs;
use panix\engine\CMS;
use panix\engine\Html;

?>

<?php
//$this->widget('ext.fancybox.Fancybox', array(
//   'target' => 'a.fancybox',
//));
?>
<?php


$subManual = Docs::findOne($this->context->model->id)->children()->all();
if (isset($subManual)) {

    foreach ($subManual as $row) {
        ?>
        <div style="margin-bottom:20px;list-style: circle">
            <?= Html::a($row->name, $row->getUrl(), ['title' => $row->name, 'class' => 'h4']) ?>

        </div>
        <?php
    }
}
?>


<?php if ($this->context->model->description) { ?>
<div class="manual-view">
    <h1><?= ($this->h1) ? $this->h1 : $this->context->model->name; ?></h1>
    <div class="tester content test">
        <?= $this->context->model->description; ?>
    </div>

    <div class="card-footer">
        <div class="date">
            <i class="icon-calendar"></i>
            <?php echo CMS::date($this->context->model->created_at) ?>
        </div>

    </div>
    <?php
    } else {

        echo 'Информация составляется.';
    }
    ?>


</div>











