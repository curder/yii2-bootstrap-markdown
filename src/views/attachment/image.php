<?php
use dosamigos\fileupload\FileUploadUI;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;

/** @var $imageUploadUrl string */
/** @var $imageManagerJsonRoute string */
?>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <div class="modal-title"></div>
</div>

<div class="modal-body">
    <?= Tabs::widget([
        'renderTabContent' => false,
        'items' => [
            ['label' => '链接地址', 'options' => ['id' => 'image_url']],
            ['label' => '上传图片', 'options' => ['id' => 'image_upload']],
            ['label' => '图片历史记录', 'options' => ['id' => 'image_history']],
        ],
    ]) ?>

    <div class="tab-content" style="margin-top: 10px;">
        <div id="image_url" class="tab-pane active">
            <div class="form-group">
                <label for="image_-url">链接URL</label>
                <input type="text" class="form-control" id="image_url" placeholder="文件URL">
            </div>
            <div class="form-group">
                <label for="image_title">链接描述</label>
                <input type="text" class="form-control" id="image_title" placeholder="文件描述">
            </div>
        </div>

        <div id="image_upload" class="tab-pane">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'method' => 'POST', 'action' => $imageUploadUrl, 'id' => 'upload_image']); ?>
            <?= FileUploadUI::widget([
                'name' => 'file',
                'attribute' => 'file',
                'url' => $imageUploadUrl,
                'gallery' => true,
                'fieldOptions' => [
//                    'accept' => 'image/*'
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
            ]);
            ?>
            <?php ActiveForm::end(); ?>
        </div>


        <div id="image_history" class="tab-pane">
            <ul id="ownFile">


            </ul>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="modal-footer"></div>
<script>
    var urlManager = "<?= $imageManagerJsonRoute ?>";
    $("[href='#image_history']").on("click", function () {
        $.post(urlManager, function (res) {
            var str = '';
            if (res.length > 0) {
                var i, len = res.length;
                for (i = 0; i < len; i++) {
                    str += '<a href="javascript:;"><li><img src="' + res[i].thumb + '" alt="' + res[i].title + '" class="select_pic"><p>' + res[i].title + '</p></li></a>';
                }
            }
            $("#ownFile").html(str);
        }, "json")
    })
</script>