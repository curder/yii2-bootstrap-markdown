<?php
namespace curder\markdown\widgets;
use yii\web\AssetBundle;
class MarkdownAsset extends AssetBundle{
    public $sourcePath = '@vendor/curder/yii2-bootstrap-markdown/assets';
    public $language;
    public $css = [
        'css/bootstrap-markdown.min.css',
        'css/upload.css'
    ];
    public $js = [
        'js/bootstrap-markdown.js',
        'js/markdown.min.js',
        // 'js/upload.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public function init()
    {
        $this->sourcePath = __DIR__ . '/../assets';
        parent::init();
    }
}
