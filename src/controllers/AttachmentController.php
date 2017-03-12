<?php
namespace curder\markdown\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\Controller;

class AttachmentController extends Controller
{
    public function actionImageModal()
    {
        $imageUploadUrl = Yii::$app->controller->module->imageUploadRoute;
        $imageManagerJsonRoute = Yii::$app->controller->module->imageManagerJsonRoute;
        return $this->renderAjax('image', ['imageUploadUrl' => $imageUploadUrl, 'imageManagerJsonRoute' => $imageManagerJsonRoute]);
    }

    public function actionFileModal()
    {
        $fileUploadUrl = Yii::$app->controller->module->fileUploadRoute;
        $fileManagerJsonRoute = Yii::$app->controller->module->fileManagerJsonRoute;
        return $this->renderAjax('file', ['fileUploadUrl' => $fileUploadUrl, 'fileManagerJsonRoute' => $fileManagerJsonRoute]);
    }

    /**
     * @param $file
     * @return string
     */
    public function actionDeleteFile($file)
    {
        $filePath = Yii::$app->controller->module->getFilePath($file);
        $fileName = basename($file);
        $output['files'][] = [
            'name' => $fileName,
            'size' => filesize($filePath),
            'url' => $filePath,
            'thumbnailUrl' => $filePath,
            'deleteUrl' => Yii::$app->controller->module->getDeleteUrl($file),
            'deleteType' => 'POST',
        ];

        if (is_file($filePath)) unlink($filePath);

        return Json::encode($output);
    }

}