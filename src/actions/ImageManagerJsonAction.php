<?php
namespace curder\markdown\actions;

use Yii;
use yii\web\HttpException;
use yii\helpers\FileHelper;

class ImageManagerJsonAction extends \yii\base\Action
{
    public function init()
    {
        if (!Yii::$app->request->isAjax) {
            throw new HttpException(403, 'This action allow only ajaxRequest');
        }
    }

    public function run()
    {
        $onlyExtensions = array_map(function ($ext) {
            return '*.' . $ext;
        }, Yii::$app->controller->module->imageAllowExtensions);
        $filesPath = FileHelper::findFiles(Yii::$app->controller->module->getSaveDir(), [
            'recursive' => true,
            'only' => $onlyExtensions
        ]);
        if (is_array($filesPath) && count($filesPath)) {
            $result = [];
            foreach ($filesPath as $filePath) {
                $url = Yii::$app->controller->module->getUrl(pathinfo($filePath, PATHINFO_BASENAME));
                $result[] = ['thumb' => $url, 'image' => $url, 'title' => pathinfo($filePath, PATHINFO_FILENAME)];
            }
            return $result;
        }
    }

}
