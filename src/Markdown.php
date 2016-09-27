<?php
namespace curder\markdown;

use yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class Markdown extends yii\widgets\InputWidget
{
    public $language = 'zh';
    public $uploadImageUrl = '';
    public $model;

    public function init()
    {
        $this->options['data-provide'] = 'markdown-editor';

        parent::init();
    }

    public function run()
    {
        MarkdownAsset::register($this->view)->js[] = 'locale/bootstrap-markdown.' . $this->language . '.js';
        $options = [
            'autofocus' => true,
            'language' => $this->language,
        ];
        $clientOptions = Json::htmlEncode($options);

        $js = "$(\"[data-provide=markdown-textarea]\").markdown($clientOptions)";
        $this->view->registerJs($js);
        if ($this->hasModel()) {
            $html = Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            $html = Html::textarea($this->name, $this->value, $this->options);
        }

         $html .= $this->render('modal', ['model' => $this->model]);

        return $html;
    }
}

