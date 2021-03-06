<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;


?>

<div class="row">
    <div class="col-sm-12 col-md-7 col-lg-8">
        <?php
        $form = ActiveForm::begin();
        ?>
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <div class="card-body">
                <?= $form->field($model, 'name'); ?>
                <?= $form->field($model, 'slug'); ?>
                <?= $form->field($model, 'description')->widget(\panix\ext\tinymce\TinyMce::class, ['options' => ['rows' => 6]]); ?>
                <?= $form->field($model, 'tagValues')->widget(\panix\ext\taginput\TagInput::class, []); ?>

            </div>
            <div class="card-footer text-center">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app/default', 'CREATE') : Yii::t('app/default', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-sm-12 col-md-5 col-lg-4">
        <?php echo $this->render('_categories', ['model' => $model]); ?>
    </div>
</div>
