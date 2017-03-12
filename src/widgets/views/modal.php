<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
?>
<?php
Modal::begin([
    'id' => $id . 'Modal',
    'header' => '<h3>上传图片</h3>',
    'footer' => Html::button('插入', ['class' => 'btn btn-success', 'data-dismiss' => 'modal'])
]) ?>

<?php // $this->render('image.php---',['imageUpload' => $imageUpload]) ?>

<?php Modal::end() ?>