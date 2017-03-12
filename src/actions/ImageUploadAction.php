<?php
namespace curder\markdown\actions;

use Yii;
use curder\markdown\models\ImageUploadModel;

class ImageUploadAction extends \yii\base\Action
{
    function run()
    {
        if (isset($_FILES)) {
            $model = new ImageUploadModel();
            if ($model->upload()) {
                return $model->getResponse();
            } else {
                return ['error' => 'Unable to save image file'];
            }
        }
    }

}
