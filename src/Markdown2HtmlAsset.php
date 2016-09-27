<?php

namespace curder\markdown;
use yii\web\AssetBundle;
class Markdown2HtmlAsset extends AssetBundle{
    public $js = [
        'js/markdown.js',
    ];
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
}