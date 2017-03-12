<?php

namespace curder\markdown\actions;

use Yii;
use yii\base\Action;
use curder\markdown\models\FileUploadModel;


class FileUploadAction extends Action
{
    function run()
    {
        if (isset($_FILES)) {
            $model = new FileUploadModel();
            if ($model->upload()) {
                return $model->getResponse();
            } else {
                return ['error' => 'Unable to save file'];
            }
        }
    }

}
