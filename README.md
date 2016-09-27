Yii2 Bootstrap Markdown Editor
==============================
Yii2 Bootstrap Markdown Editor

## Requirements
[2amigos/yii2-file-upload-widget](https://github.com/2amigos/yii2-file-upload-widget)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist curder/yii2-bootstrap-markdown "dev-master"
```

or add

```
"curder/yii2-bootstrap-markdown": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your views template by  :

```php
<?= \curder\markdown\Markdown::widget(['name' => 'xxx', 'language' => 'zh'])?>?>
```

Or Use ActiveForm

```
 <?php echo $form->field($model, 'content')->widget(Markdown::className(), [
    'language' => 'zh',
    'useImageUpload' => true, // use upload file Or image modal
    'uploadImageUrl' => '/upload/image',
    'deleteImageUrl' => '/upload/delete-image',
    'uploadFileUrl' => '/upload/file',
    'deleteFileUrl' => '/upload/delete-file',
]); ?>
```

## Setting

add some config in `<project>/common/params.php` file for advanced template.

```
...
'image.domain' => 'http://www.blog.com',
'image.maxSize' => 2 * 1024 * 1024,
'image.accept'=>'image/*',
'image.extensions' => 'jpg,gif,png',
'file.maxSize' => 2 * 1024 * 1024,
'file.extensions' => 'txt,docx',
...
```

## Controllers
some demo for upload Controller.

```php
<?php
namespace backend\controllers;

use curder\markdown\models\Upload;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;

class UploadController extends Controller
{
    public $domain;

    public function init()
    {
        parent::init();
        $this->domain = Yii::$app->params['image.domain'];
    }

    /**
     * upload Image file
     * @return string
     */
    public function actionImage()
    {
        if (Yii::$app->request->isPost) { // 文件上传
            $model = new Upload;
            $model->scenario = Upload::SCENARIO_UPLOAD_IMAGE;
            $model->image = UploadedFile::getInstanceByName('image');
            if($model->validate()){
                if($model->upload('image')){
                    return Json::encode([
                        'files' => [[
                            'name' => $model->name,
                            'size' => $model->size,
                            "url" => $this->domain . $model->url,
                            "thumbnailUrl" => $this->domain . $model->url,
                            "deleteUrl" => '/upload/image-delete?imageName=' . $model->fileName,
                            "deleteType" => "POST"
                        ]]
                    ]);
                }
            }else{
                $errors = [];
                $modelErrors = $model->getErrors();
                foreach ($modelErrors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $fieldError) {
                        $errors[] = $fieldError;
                    }
                }
                if (empty($errors)) {
                    $errors = ['Unknown image upload validation error!'];
                }
                return Json::encode(['errors' => $errors]);
            }
        }
        return $this->renderAjax('image');
    }

    /**
     * delete file
     * @param $imageName
     * @return string
     */
    public function actionImageDelete($imageName)
    {
        $this->actionDelete($imageName,'image');
    }

    /**
     * upload File.
     * @return string
     */
    public function actionFile()
    {
        if (Yii::$app->request->isPost) { // 文件上传
            $model = new Upload;
            $model->scenario = Upload::SCENARIO_UPLOAD_FILE;
            $model->file = UploadedFile::getInstanceByName('file');

            if($model->validate()){
                if($model->upload('file')){
                    return Json::encode([
                        'files' => [[
                            'name' => $model->name,
                            'size' => $model->size,
                            "url" => $this->domain . $model->url,
                            "thumbnailUrl" => $this->domain . $model->url,
                            "deleteUrl" => '/upload/file-delete?fileName=' . $model->fileName,
                            "deleteType" => "POST"
                        ]]
                    ]);
                }
            }else{
                $errors = [];
                $modelErrors = $model->getErrors();
                foreach ($modelErrors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $fieldError) {
                        $errors[] = $fieldError;
                    }
                }
                if (empty($errors)) {
                    $errors = ['Unknown file upload validation error!'];
                }
                return Json::encode(['errors' => $errors]);
            }
        }
        return $this->renderAjax('file');
    }

    /**
     * delete file.
     * @param $fileName
     * @return string
     */
    public function actionFileDelete($fileName)
    {
        $this->actionDelete($fileName,'file');
    }

    public function actionDelete($fileName,$type = 'image')
    {
        $model = new Upload();
        if($model->delete($fileName,$type)){
            $output =[];
            return Json::encode($output);
        }

    }
}

```

## Views
### file.php
this codes are from `<app>/views/upload/file.php`

```html
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
                        'accept' => Yii::$app->params['image.extension']
                    ],
                    'clientOptions' => [
                        'maxFileSize' => Yii::$app->params['files.maxSize']
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
```

### iamge.php

```html
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
            ['label' => '图片地址', 'options' => ['id' => 'url']],
            ['label' => '上传图片', 'options' => ['id' => 'upload']],
            ['label' => '历史记录', 'options' => ['id' => 'history']],
        ],
    ]) ?>

    <div class="tab-content" style="margin-top: 10px;">
        <div id="url" class="tab-pane active">
            <div class="form-group">
                <label for="image-url">图片URL</label>
                <input type="text" class="form-control" id="image-url" placeholder="图片URL">
            </div>
            <div class="form-group">
                <label for="image-title">图片描述</label>
                <input type="text" class="form-control" id="image-title" placeholder="图片描述">
            </div>
        </div>

        <div id="upload" class="tab-pane">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <?= FileUploadUI::widget([
                    'name'=>'image',
                    // 'attribute' => 'image',
                    'url' => ['/upload/image'],
                    'gallery' => false,
                    'fieldOptions' => [
                        'accept' => Yii::$app->params['image.accept']
                    ],
                    'clientOptions' => [
                        'maxFileSize' => Yii::$app->params['image.maxSize']
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
```

