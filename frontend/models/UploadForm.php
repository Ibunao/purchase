<?php

namespace frontend\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $xlsFile;
    public $path;
    public function rules()
    {
        return [
            [['xlsFile'], 'file', 'skipOnEmpty' => false],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
        	$this->path = './upload/' . time() . '.' . $this->xlsFile->extension;
            $this->xlsFile->saveAs($this->path);
            return true;
        } else {
            return false;
        }
    }
}