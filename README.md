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
    <?= \curder\markdown\widgets\Markdown::widget(['id'=>'title','name' => 'title', 'clientOptions'=>['language' => 'zh','row'=>12]])?>
```

Or Use ActiveForm

```
    <?php $form->field($model, 'keyword')->widget(\curder\markdown\widgets\Markdown::className(), [
        'clientOptions' => [
            'row' => 12,
            'language' => 'zh',
            'useImageUpload' => true, // use upload file Or image modal
            'deleteUrl' => '/markdown/attachment/delete-file',
            'imageAttachmentRoute' => '/markdown/attachment/image-modal',
            'fileAttachmentRoute' => '/markdown/attachment/file-modal',
            'imageUploadRoute' => '/markdown/upload/image',
            'fileUploadRoute' => '/markdown/upload/file',
            'imageManagerJsonRoute' => '/markdown/upload/image-json',
            'fileManagerJsonRoute' => '/markdown/upload/file-json',
        ]
        ...
    ]); ?>
```

## DEMO
![demo](https://raw.githubusercontent.com/curder/yii2-bootstrap-markdown/master/demo.jpg "demo")