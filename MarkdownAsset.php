<?php
namespace curder\markdown;
use yii\web\AssetBundle;
class MarkdownAsset extends AssetBundle{
    public $language;
    public $css = [
        'css/bootstrap-markdown.min.css',
        'css/upload.css'
    ];
    public $js = [
        'js/bootstrap-markdown.js',
        'js/upload.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'curder\markdown\Markdown2HtmlAsset',
    ];
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
}
