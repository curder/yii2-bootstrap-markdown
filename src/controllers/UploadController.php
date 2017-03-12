<?php

namespace curder\markdown\controllers;

use yii\web\Response;

class UploadController extends \yii\web\Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ]
        ];
    }

    public function actions()
    {
        return [
            'file' => 'curder\markdown\actions\FileUploadAction',
            'image' => 'curder\markdown\actions\ImageUploadAction',
            'image-json' => 'curder\markdown\actions\ImageManagerJsonAction',
            'file-json' => 'curder\markdown\actions\FileManagerJsonAction',
        ];
    }

}
