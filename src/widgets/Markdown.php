<?php
namespace curder\markdown\widgets;

use curder\markdown\widgets\MarkdownAsset;
use Yii;
use yii\base\InvalidConfigException;
use curder\markdown\MarkdownModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\widgets\InputWidget;

/**
 * Class Markdown
 * @package curder\markdown\widgets
 * @property MarkdownModule $module
 */
class Markdown extends InputWidget
{
    /**
     * @var string Module Id already configured for Application Module
     */
    public $moduleId = 'markdown';

    /**
     * @var string the model attribute that this widget is associated with.
     */

    public $attribute;

    /**
     * @var string the input name. This must be set if [[model]] and [[attribute]] are not set.
     */
    public $name;

    /**
     * @var string the input value.
     */
    public $value;

    /**
     * @var array the HTML attributes for the widget container tag.
     */
    public $options = [];

    /**
     * editor options
     */
    public $clientOptions = [];

    private $_assetBundle;
    private $_html;

    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init()
    {
        $this->defaultOptions();
        $this->registerAssetBundle();
        $this->registerRegional();
        $this->registerPlugins();
        $this->registerScript();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {

    }

    protected function defaultOptions()
    {
        $this->options = ArrayHelper::merge($this->options, $this->module->widgetOptions);
        $this->clientOptions = ArrayHelper::merge($this->clientOptions, $this->module->widgetClientOptions);
        if (!isset($this->options['id'])) {
            if ($this->hasModel()) {
                $this->options['id'] = Html::getInputId($this->model, $this->attribute);
                $this->_html = Html::activeTextarea($this->model, $this->attribute, $this->options);
            } else {
                $this->options['id'] = $this->getId();
                $this->_html = Html::textarea($this->name, $this->value, $this->options);
            }
        }
        $row = isset($this->clientOptions['row']) ? $this->clientOptions['row'] : 12;
        $this->setOptionsKey('row', $row);
    }

    protected function registerPlugins()
    {

    }

    /**
     * Register language for Editor
     */
    protected function registerRegional()
    {
        $this->clientOptions['language'] = ArrayHelper::getValue($this->clientOptions, 'language', Yii::$app->language);
        $langAsset = 'locale/bootstrap-markdown.' . $this->clientOptions['language'] . '.js';
        if (file_exists($this->sourcePath . DIRECTORY_SEPARATOR . $langAsset)) {
            $this->assetBundle->js[] = $langAsset;
        } else {
            ArrayHelper::remove($this->clientOptions, 'language');
        }
    }

    protected function registerScript()
    {
        $clientOptions = (count($this->clientOptions)) ? Json::encode($this->clientOptions) : '';

        $js = "$(\"#{$this->options['id']}\").markdown($clientOptions);";
        $this->view->registerJs("$(\"#{$this->options['id']}\").attr('rows',{$this->clientOptions['row']})");

        if ($this->module->useUpload) { // 如果使用自定义上传
            $this->registerClientJs();
            $this->_html .= $this->render('modal',['id'=>$this->options['id']]);
        } else {
            $this->view->registerJs($js);
        }
        echo $this->_html;
    }

    /**
     * @return AssetBundle
     */
    public function getAssetBundle()
    {
        if (!($this->_assetBundle instanceof AssetBundle)) {
            $this->registerAssetBundle();
        }
        return $this->_assetBundle;
    }

    /**
     * Register assetBundle
     */
    protected function registerAssetBundle()
    {
        $this->_assetBundle = MarkdownAsset::register($this->getView());
    }

    /**
     * @return bool|string The path of assetBundle
     */
    public function getSourcePath()
    {
        return Yii::getAlias($this->getAssetBundle()->sourcePath);
    }

    /**
     * @return MarkdownModule
     * @throws InvalidConfigException
     */
    public function getModule()
    {
        if (is_null(Yii::$app->getModule($this->moduleId))) {
            throw new InvalidConfigException('Invalid config Redactor module with "$moduleId"');
        }
        return Yii::$app->getModule($this->moduleId);
    }

    /**
     * @param $key
     * @param mixed $defaultValue
     */
    protected function setOptionsKey($key, $defaultValue = null)
    {
        $this->clientOptions[$key] = Url::to(ArrayHelper::getValue($this->clientOptions, $key, $defaultValue));
    }


    private function registerClientJs()
    {
        $js = <<<JS
jQuery.extend({
    modalLoad: function(url, data, callback) {
        $('.modal-content').load(url, data, callback);
        $('.modal').modal();
    }
});

jQuery(document).ready(function () {
    $("#{$this->options['id']}").markdown({
        language: "{$this->clientOptions['language']}",
        buttons: [
            [{},{
                name: "groupLink",
                data: [{
                    name: "cmdUrl",
                    callback: function(e) {
                        $.modalLoad("{$this->module->fileAttachmentRoute}",function(){ // 上传文件
                            $('.modal-title').text('插入链接');
                            $('.modal-footer').html('<button type="button" class="btn btn-success" data-dismiss="modal">插入</button> <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>');
                            var chunk, cursor, selected = e.getSelection(), content = e.getContent(), link;
                            if (selected.length > 0) {
                                chunk = selected.text;
                                $('#file_title').val(chunk);
                            }
                            $('.modal-footer .btn-success').on('click', function(){
                                if($('#file_url').hasClass('active')) { // 插入链接
                                    chunk = $('#link_title').val();
                                    link =  $('#link_url').val();
                                   
                                    var urlRegex = new RegExp('^((http|https)://|(mailto:)|(//))[a-z0-9]', 'i');
                                    if(link !== null && link !== '' && link !== 'http://' && urlRegex.test(link)) {
                                        var sanitizedLink = $('<div>'+link+'</div>').text();
                                        e.replaceSelection('['+chunk+']('+sanitizedLink+')');
                                        cursor = selected.start+1;
                                        e.setSelection(cursor,cursor+chunk.length);
                                    }
                                } else if($('#upload_file').hasClass('active')) {
                                    $('#file-upload').fileupload();
                                    
                                    var links = '';
                                    $('#file_upload .name a').each(function(){
                                        chunk = $(this).attr('file_title');
                                        link = $(this).attr('file_href');
                                        var urlRegex = new RegExp('^.*(/uploads/)', 'i');
                                        if(link !== null && link !== '' && link !== 'http://' && urlRegex.test(link)) {
                                            var sanitizedLink = $('<div>'+link+'</div>').text();
                                            links+= '['+chunk+']('+sanitizedLink+')';
                                        }
                                    });
                                    if(links !== '') {
                                        e.replaceSelection(links);
                                        cursor = selected.start+1;
                                        e.setSelection(cursor,cursor+chunk.length);
                                    }
                                } else {}
                            });
                        });
                    }
                },{
                    name: "cmdImage",
                    callback: function(e) {
                        $.modalLoad("{$this->module->imageAttachmentRoute}", function(){ // 上传图片
                            $('.modal-title').text('插入图片');
                            $('.modal-footer').html('<button type="button" class="btn btn-success" data-dismiss="modal">插入</button> <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>');
                            var chunk, cursor, selected = e.getSelection(), content = e.getContent(), link;
                            if (selected.length > 0) {
                                chunk = selected.text;
                                $('#image_title').val(chunk);
                            }
                            $('.modal-footer .btn-success').on('click', function(){
                                if($('#image_url').hasClass('active')) { // 输入图片地址
                                    chunk = $('#image_title').val();
                                    link = $('#image_url').val(); 
                                    var urlRegex = new RegExp('^((http|https)://|(//))[a-z0-9]', 'i');
                                    if (link !== null && link !== '' && link !== 'http://' && urlRegex.test(link)) {
                                        var sanitizedLink = $('<div>'+link+'</div>').text();
                                        e.replaceSelection('!['+chunk+']('+sanitizedLink+' "'+chunk+'")');
                                        cursor = selected.start+2;
                                        e.setSelection(cursor,cursor+chunk.length);
                                    }
                                } else if($('#image_upload').hasClass('active')) { // 上传文件
                                    $('#upload_image').fileupload();
                                    var images = '';
                                    $('#image_upload .preview').each(function(){
                                        chunk = $(this).find('a').attr('title');
                                        link = $(this).find('img').attr('src');
                                        var urlRegex = new RegExp('^.*(/uploads/)', 'i');

                                        if(link !== null && link !== '' && link !== 'http://' && urlRegex.test(link)) {
                                            var sanitizedLink = $('<div>'+link+'</div>').text();
                                            images+= '!['+chunk+']('+sanitizedLink+' "'+chunk+'")';
                                        }
                                    });

                                    if(images !== '') {
                                        e.replaceSelection(images);
                                        cursor = selected.start+2;
                                        e.setSelection(cursor,cursor+chunk.length);
                                    }
                                } else if($("#image_history").hasClass('active')){ // 选择文件
                                    
                                    
                                }
                            });
                        });
                    }
                }]
            }]
        ],
        footer: "本站编辑器使用了 GFM (GitHub Flavored Markdown) 语法，关于此语法的说明，请 <a href=\"https://help.github.com/articles/github-flavored-markdown\" target=\"_blank\">点击此处</a> 获得更多帮助。"
    });
});
JS;
        $this->view->registerJs($js);
        return true;
    }
}
