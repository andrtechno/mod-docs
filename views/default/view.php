<?php
use panix\mod\docs\models\Docs;
use panix\engine\CMS;
use panix\engine\Html;


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
<?php
echo \panix\engine\widgets\like\LikeWidget::widget([
    'model' => $this->context->model
]);
?>

<?php if ($this->context->model->description) { ?>
    <div class="manual-view">
        <h1><?= ($this->h1) ? $this->h1 : $this->context->model->name; ?></h1>
        <div class="tester content test">
            <?= $this->context->model->description; ?>
        </div>
        <div class="card-footer">
            <i class="icon-calendar"></i>
            <?php echo CMS::date($this->context->model->created_at) ?>
        </div>
    </div>
<?php } else { ?>
    <div class="alert alert-info">Информация составляется.</div>
<?php } ?>









