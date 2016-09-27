<?php
namespace curder\markdown;

use Yii;
use yii\base\Action;
use yii\web\Controller;
use yii\widgets\InputWidget;

class UploadImage extends Action
{
    public function init()
    {
        // parent::init();
        echo Yii::$app->view->render('/views/image.php');
         // return $this->renderContent('image');
    }
    
}

