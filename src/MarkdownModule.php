<?php
namespace curder\markdown;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/**
 * Class MarkdownModule
 * @package curder\markdown
 */
class MarkdownModule extends Module
{
    public $controllerNamespace = 'curder\markdown\controllers';
    public $defaultRoute = 'upload';
    public $uploadDir = '@frontend/web/uploads';
    public $uploadUrl = '@frontend/web/uploads';
    public $deleteUrl = '/markdown/attachment/delete-file';
    public $imageAttachmentRoute = '/markdown/attachment/image-modal';
    public $fileAttachmentRoute = '/markdown/attachment/file-modal';
    public $imageUploadRoute = '/markdown/upload/image';
    public $fileUploadRoute = '/markdown/upload/file';
    public $imageManagerJsonRoute = '/markdown/upload/image-json';
    public $fileManagerJsonRoute = '/markdown/upload/file-json';
    public $imageAllowExtensions = ['jpg','png', 'gif', 'bmp', 'svg'];
    public $fileAllowExtensions = ['html','md','txt','doc','pdf','jpg'];
    public $widgetOptions = [];
    public $widgetClientOptions = [];
    public $useUpload = true;

    public function getOwnerPath()
    {
        return Yii::$app->user->isGuest ? 'guest' : Yii::$app->user->id;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function getSaveDir()
    {
        $path = Yii::getAlias($this->uploadDir);
        if (!file_exists($path)) {
            throw new InvalidConfigException('Invalid config $uploadDir');
        }
        if (FileHelper::createDirectory($path . DIRECTORY_SEPARATOR . $this->getOwnerPath(), 0777)) {
            return $path . DIRECTORY_SEPARATOR . $this->getOwnerPath();
        }
    }

    /**
     * @param $fileName
     * @return string
     * @throws InvalidConfigException
     */
    public function getFilePath($fileName)
    {
        return $this->getSaveDir() . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param $fileName
     * @return string
     */
    public function getUrl($fileName)
    {
        return Url::to($this->uploadUrl . '/' . $this->getOwnerPath() . '/' . $fileName);
    }

    /**
     * @param $fileName
     * @return string
     */
    public function getDeleteUrl($fileName)
    {
        return Url::to([$this->deleteUrl, 'file' => $fileName]);
    }
}