<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use dosamigos\fileupload\FileUploadUI;
?>

<?php Modal::begin([
    'id' => 'imageModal',
    'header' => '<h3>上传图片</h3>',
    'footer' => Html::button('插入', ['class' => 'btn btn-success', 'data-dismiss' => 'modal']) . '<a href="#" class="btn btn-default" data-dismiss="modal">取消</a>',
]) ?>

<div class="row">
    <?= Tabs::widget([
        'renderTabContent' => false,
        'items' => [
            ['label' => '图片地址', 'options' => ['id' => 'address']],
            ['label' => '上传图片', 'options' => ['id' => 'images']],
            ['label' => '历史图片', 'options' => ['id' => 'history']],
        ],
    ]) ?>

    <div class="tab-content">
        <div id="address" class="tab-pane active">
            111111
        </div>
        <div id="images" class="tab-pane">
            <?= FileUploadUI::widget([
                'model' => $model,
                'attribute' => 'title',
                'url' => ['default/images-upload', 'id' => $model->id],
                'gallery' => false,
                'fieldOptions' => [
                    'accept' => 'image/*'
                ],
                'clientOptions' => [
                    'maxFileSize' => 2000000
                ],
                // ...
                'clientEvents' => [
                    'fileuploaddone' => 'function(e, data) {
                                    console.log(e);
                                    console.log(data);
                                }',
                    'fileuploadfail' => 'function(e, data) {
                                    console.log(e);
                                    console.log(data);
                                }',
                ],
            ]);?>

        </div>
        <div id="history" class="tab-pane">33333</div>
    </div>
</div>

<?php Modal::end() ?>
