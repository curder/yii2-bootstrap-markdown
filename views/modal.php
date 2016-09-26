<?php
use yidashi\webuploader\Webuploader;
use yii\bootstrap\Modal;
use yii\helpers\Html;
?>
<?php
Modal::begin([
    'id' => 'imageModal',
    'header' => '<h3>上传图片</h3>',
    'footer' => Html::button('插入', ['class' => 'btn btn-success', 'data-dismiss' => 'modal'])
]) ?>
<?php // echo Webuploader::widget(['name' => 'markdown-image']) ?>

<?php Modal::end() ?>
