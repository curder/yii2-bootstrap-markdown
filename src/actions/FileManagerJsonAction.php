<?php

namespace curder\markdown\actions;

use yii\base\Action;
use yii\helpers\FileHelper;
use yii\web\HttpException;
use Yii;

class FileManagerJsonAction extends Action
{

    public function init()
    {
        if (!Yii::$app->request->isAjax) {
            throw new HttpException(403, 'This action allow only ajaxRequest');
        }
    }

    public function run()
    {
        $config = ['recursive' => true];
        if (!is_null(Yii::$app->controller->module->fileAllowExtensions)) {
            $onlyExtensions = array_map(function ($ext) {
                return '*.' . $ext;
            }, Yii::$app->controller->module->fileAllowExtensions);
            $config['only'] = $onlyExtensions;
        }
        $filesPath = FileHelper::findFiles(Yii::$app->controller->module->getSaveDir(), $config);
        if (is_array($filesPath) && count($filesPath)) {
            $result = [];
            foreach ($filesPath as $filePath) {
                $url = Yii::$app->controller->module->getUrl(pathinfo($filePath, PATHINFO_BASENAME));
                $fileName = pathinfo($filePath, PATHINFO_FILENAME);
                $result[] = ['title' => $fileName, 'name' => $fileName, 'link' => $url, 'size' => Yii::$app->formatter->asShortSize(filesize($filePath)), 2];
            }
            return $result;
        }
    }
}