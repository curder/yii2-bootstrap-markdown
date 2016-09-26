Yii2 Bootstrap Markdown Editor
==============================
Yii2 Bootstrap Markdown Editor

## Requirements
[2amigos/yii2-file-upload-widget](https://github.com/2amigos/yii2-file-upload-widget)

[qiniu/php-sdk](https://github.com/qiniu/php-sdk)

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
<?= $form->field($model,'attributeName')->widget('curder\markdown\Markdown',['language' => 'zh']); ?>
```