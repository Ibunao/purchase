<?php
namespace frontend\modules\order\controllers;

use Yii;
use yii\web\UploadedFile;
use frontend\controllers\base\BaseController;
use frontend\models\UploadForm;
use frontend\models\AgentModel;
use PHPExcel;
use PHPExcel_IOFactory;
/**
 * 批量读取
 * 只能使用xls文件
 * @author        ding
 */
class InsertController extends BaseController
{
	public function actionRelation()
	{
		$model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->xlsFile = UploadedFile::getInstance($model, 'xlsFile');
            if ($model->upload()) {
            	$objPHPExcel = new PHPExcel();
            	$objPHPExcel = PHPExcel_IOFactory::createReaderForFile($model->path);
            	$objPHPExcel = PHPExcel_IOFactory::load($model->path);
            	$result = $objPHPExcel->getActiveSheet()->toArray();
            	$length = count($result);
            	$rows = [];
                for ($i=1; $i < $length; $i++) { 
                	$row = [$result[$i][0], $result[$i][1]];
                	$rows[] = $row;
                }
                Yii::$app->db->createCommand()->batchInsert(AgentModel::tableName(), ['agent_code','agent_name'], $rows)->execute();
                var_dump('添加成功');exit;
            }else{
            	var_dump($model);exit;
            }
        }

        return $this->render('upload', ['model' => $model]);
	}

}