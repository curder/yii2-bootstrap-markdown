<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
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
            2222
        </div>
        <div id="history" class="tab-pane">33333</div>
    </div>
</div>

<?php Modal::end() ?>
