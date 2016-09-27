<?php
use dosamigos\fileupload\FileUploadUI;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use backend\widgets\ActiveForm;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <div class="modal-title"></div>
</div>

<div class="modal-body">
    <?= Tabs::widget([
        'renderTabContent' => false,
        'items' => [
            ['label' => '链接地址', 'options' => ['id' => 'url']],
            ['label' => '上传文件', 'options' => ['id' => 'upload']],
            ['label' => '文件历史记录', 'options' => ['id' => 'history']],
        ],
    ]) ?>

    <div class="tab-content" style="margin-top: 10px;">
        <div id="url" class="tab-pane active">
            <div class="form-group">
                <label for="image-url">链接URL</label>
                <input type="text" class="form-control" id="link-url" placeholder="文件URL">
            </div>
            <div class="form-group">
                <label for="image-title">链接描述</label>
                <input type="text" class="form-control" id="link-title" placeholder="文件描述">
            </div>
        </div>

        <div id="upload" class="tab-pane">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <?= FileUploadUI::widget([
                    'name'=>'file',
                    'url' => ['/upload/file'],
                    'gallery' => false,
                    'fieldOptions' => [
                        'accept' => '*.zip,*.rar,*.tar.gz'
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


        <div id="history" class="tab-pane">
            <ul>
                <li>11111</li>
                <li>22222</li>
            </ul>
        </div>
    </div>
</div>
<div class="modal-footer"></div>