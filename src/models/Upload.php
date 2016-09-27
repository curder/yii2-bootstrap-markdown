<?php
/**
 * Created by PhpStorm.
 * User: luo
 * Date: 16/9/27
 * Time: 10:19
 */

namespace curder\markdown\models;


use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class Upload extends Model
{

    const SCENARIO_UPLOAD_IMAGE = 'upload-images';
    const SCENARIO_UPLOAD_FILE = 'upload-files';

    const  UPLOAD_IMAGE_DIR = 'uploads/images/markdown';
    const  UPLOAD_FILE_DIR = 'uploads/files/markdown';

    /**
     * @var UploadedFile Uploaded image
     */
    public $image;

    /**
     * @var string
     */
    public $file;

    /**
     * @var string Web accessible path to the uploaded image
     */
    public $url;

    /**
     * @var string save fileName
     */
    public $fileName;

    /**
     * @var string file size
     */
    public $size;

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $domain;

    /**
     * initialize action
     */
    public function init()
    {
        $this->domain = 'http://www.blog.com';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['image', 'image', 'extensions' => ['png', 'jpg', 'gif'], 'maxWidth' => 1000, 'maxHeight' => 1000, 'maxSize' => 2 * 1024 * 1024, 'on' => self::SCENARIO_UPLOAD_IMAGE],
            ['file', 'file', 'extensions' => ['pdf', 'zip', 'gz', 'txt', 'doc'], 'maxSize' => 12 * 1024 * 1024, 'on' => self::SCENARIO_UPLOAD_FILE]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPLOAD_IMAGE] = ['image'];
        $scenarios[self::SCENARIO_UPLOAD_FILE] = ['file'];
        return $scenarios;
    }

    /**
     * Validates and saves the image.
     * Creates the folder to store images if necessary.
     * @return boolean
     */
    public function upload($type = 'image')
    {
        $uid = uniqid(time(), true);
        if ($type == 'image') {
            $this->scenario = self::SCENARIO_UPLOAD_IMAGE;
            $dir = self::UPLOAD_IMAGE_DIR;
            $this->fileName = $uid . '.' . $this->image->extension;
            $this->name = $this->image->baseName;
        } else {
            $this->scenario = self::SCENARIO_UPLOAD_FILE;
            $dir = self::UPLOAD_FILE_DIR;
            $this->fileName = $uid . '.' . $this->file->extension;
            $this->name = $this->file->baseName;
        }

        try {
            if ($this->validate()) {
                $save_path = FileHelper::normalizePath(Yii::getAlias('@frontend/web/' . $dir));

                FileHelper::createDirectory($save_path);

                $this->url = Yii::getAlias('@web/' . $dir . '/' . $this->fileName);
                if ($type == 'image') {
                    $this->image->saveAs(FileHelper::normalizePath($save_path . '/' . $this->fileName));
                } else {
                    $this->file->saveAs(FileHelper::normalizePath($save_path . '/' . $this->fileName));
                }
                return true;
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }
        return false;
    }

    public function delete($imageName, $type = 'image')
    {
        if ($type == 'image') {
            $dir = self::UPLOAD_IMAGE_DIR;
        } else {
            $dir = self::UPLOAD_FILE_DIR;
        }
        $directory = FileHelper::normalizePath(Yii::getAlias('@frontend/web/' . $dir));

        if (is_file($directory . DIRECTORY_SEPARATOR . $imageName)) {
            unlink($directory . DIRECTORY_SEPARATOR . $imageName);
        }

        $files = FileHelper::findFiles($directory);
        $output = [];
        foreach ($files as $file) {
            $this->url = Yii::getAlias('@web/' . $dir . DIRECTORY_SEPARATOR . basename($file));
            $output['files'][] = [
                'name' => basename($file),
                'size' => filesize($file),
                "url" => $this->url,
                "thumbnailUrl" => $this->url,
                "deleteUrl" => 'image-delete?imageName=' . basename($file),
                "deleteType" => "POST"
            ];
        }
        return Json::encode($output);
    }
}