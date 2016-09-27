<?php
namespace curder\markdown;

use yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class Markdown extends yii\widgets\InputWidget
{
    public $language = 'zh';
    public $useImageUpload = false;
    public $uploadImageUrl = '/site/upload-image'; // 上传文件地址
    public $deleteImageUrl = '/site/delete-image'; // 删除文件地址
    public $uploadFileUrl = '/site/upload-file'; // 上传文件地址
    public $deleteFileUrl = '/site/delete-file'; // 删除文件地址
    public $domain = ''; // 访问资源的域名
    public $rows = 10;

    public function init()
    {
        $this->options['data-provide'] = 'markdown-editor';
        parent::init();
    }

    public function run()
    {
        MarkdownAsset::register($this->view)->js[] = 'locale/bootstrap-markdown.' . $this->language . '.js';
        $options = [
            'autofocus' => false,
            'language' => $this->language
        ];
        $clientOptions = Json::htmlEncode($options);

        $js = "$(\"[data-provide={$this->options['data-provide']}]\").markdown($clientOptions);";
        $this->view->registerJs("$(\"[data-provide={$this->options['data-provide']}]\").attr('rows',$this->rows)");

        if ($this->hasModel()) {
            $html = Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            $html = Html::textarea($this->name, $this->value, $this->options);
        }

        if($this->useImageUpload){ // 如果使用自定义上传
            $this->registerClientJs();
            $html .= $this->render('modal');
        }else{
            $this->view->registerJs($js);
        }

        return $html;
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
    $("[data-provide={$this->options['data-provide']}]").markdown({
        language: "{$this->language}",
        iconlibrary: "fa",
        buttons: [
            [{},{
                name: "groupLink",
                data: [{
                    name: "cmdUrl",
                    callback: function(e) {
                        $.modalLoad("$this->uploadFileUrl",function(){ // 上传文件
                            $('.modal-title').text('插入链接');
                            $('#fileupload').fileupload();
                            $('.modal-footer').html('<button type="button" class="btn btn-success" data-dismiss="modal">插入</button> <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>');
                            var chunk, cursor, selected = e.getSelection(), content = e.getContent(), link;
                            if (selected.length > 0) {
                                chunk = selected.text;
                                $('#link-title').val(chunk);
                            }
                            $('.modal-footer .btn-success').on('click', function(){
                                if($('#url').hasClass('active')) { // 插入链接
                                    chunk = $('#link-title').val();
                                    link =  $('#link-url').val();
                                   
                                    var urlRegex = new RegExp('^((http|https)://|(mailto:)|(//))[a-z0-9]', 'i');
                                    if(link !== null && link !== '' && link !== 'http://' && urlRegex.test(link)) {
                                        var sanitizedLink = $('<div>'+link+'</div>').text();
                                        e.replaceSelection('['+chunk+']('+sanitizedLink+')');
                                        cursor = selected.start+1;
                                        e.setSelection(cursor,cursor+chunk.length);
                                    }
                                } else if($('#upload').hasClass('active')) {
                                    var links = '';
                                    $('#upload .name a').each(function(){
                                        chunk = $(this).attr('title');
                                        link = $(this).attr('href');
                                        var urlRegex = new RegExp('^.*(/uploads/files)', 'i');
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
                        $.modalLoad("$this->uploadImageUrl", function(){ // 上传图片
                            $('.modal-title').text('插入图片');
                            $('#fileupload').fileupload();
                            $('.modal-footer').html('<button type="button" class="btn btn-success" data-dismiss="modal">插入</button> <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>');
                            var chunk, cursor, selected = e.getSelection(), content = e.getContent(), link;
                            if (selected.length > 0) {
                                chunk = selected.text;
                                $('#image-title').val(chunk);
                            }
                            $('.modal-footer .btn-success').on('click', function(){
                                if($('#url').hasClass('active')) { // 输入图片地址
                                    chunk = $('#image-title').val();
                                    link = $('#image-url').val(); 
                                    var urlRegex = new RegExp('^((http|https)://|(//))[a-z0-9]', 'i');
                                    if (link !== null && link !== '' && link !== 'http://' && urlRegex.test(link)) {
                                        var sanitizedLink = $('<div>'+link+'</div>').text();
                                        e.replaceSelection('!['+chunk+']('+sanitizedLink+' "'+chunk+'")');
                                        cursor = selected.start+2;
                                        e.setSelection(cursor,cursor+chunk.length);
                                    }
                                } else if($('#upload').hasClass('active')) { // 上传文件
                                    var images = '';
                                    $('#upload .preview').each(function(){
                                        chunk = $(this).find('a').attr('title');
                                        link = $(this).find('img').attr('src');
                                        var urlRegex = new RegExp('^.*(/uploads/images/)', 'i');

                                        if(link !== null && link !== '' && link !== 'http://' && urlRegex.test(link)) {
                                            var sanitizedLink = $('<div>'+link+'</div>').text();
                                            images+= '!['+chunk+']('+sanitizedLink+' "'+chunk+'")';
                                            console.log(images);
                                        }
                                    });

                                    if(images !== '') {
                                        e.replaceSelection(images);
                                        cursor = selected.start+2;
                                        e.setSelection(cursor,cursor+chunk.length);
                                    }
                                } else {}
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

