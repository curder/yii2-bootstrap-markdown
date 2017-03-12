<?php

namespace curder\markdown\models;

use Yii;

class ImageUploadModel extends FileUploadModel
{
    public function rules()
    {
        return [
            ['file', 'file', 'extensions' => Yii::$app->controller->module->imageAllowExtensions]
        ];
    }

}