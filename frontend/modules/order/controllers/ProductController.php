<?php
namespace frontend\modules\order\controllers;

use Yii;
use yii\db\Query;
use frontend\controllers\base\BaseController;
use frontend\models\CustomerModel;
use frontend\models\ProductModel;
use frontend\models\PublicModel;
use frontend\models\ColorModel;
use PHPExcel;
use PHPExcel_IOFactory;
use frontend\helpers\IoXls;
use frontend\config\ParamsClass;
/**
 * 商品管理
 * @author        ding
 */
class ProductController extends BaseController
{
	public function actionIndex()
	{
		$request = Yii::$app->request;
		$pageIndex = $request->get('page', 1);
		$param = $request->get('param', []);
		$productModel = new ProductModel;
        //获取筛选条件数据 下拉框
		$selectFilter = $productModel->getIndexFilter($param);
		//查询结果
		$resultData = $productModel->productSearch($param);

		//检查是否有错误  
		// $error = $productModel->isHaveError();

		return $this->render('index', [
				'param' => $param,
				'selectFilter' => $selectFilter,      //下拉框自带参数
	            'select_option' => $resultData['result'],        //显示搜索的结果
	            'pagination' => $resultData['pagination'],
	            // 'is_error' => $error
			]);
	}

	 /**
     * 单个商品添加
     */
    public function actionAdd()
    {
        $productModel = new ProductModel();
        $customerModel = new CustomerModel();

        $param = Yii::$app->request->post('param', []);
        if (!empty($param)) {
            $res = $productModel->addProductOperation($param);
            if (!isset($res['code'])) {
                // 清除所有缓存
                PublicModel::flushCacheAll();
                //使用setFlash
                Yii::$app->session->setFlash('info', '添加成功');
                $this->redirect("/order/product/index");
            } else {
                Yii::$app->session->setFlash('error', '添加失败' . $res['msg']);
            }

        }
        $result = $productModel->getProductFilter();
        return $this->render('add', [
            'selectFilter' => $result
        ]);
    }
    /**
     * 商品更新
     * @return [type] [description]
     */
    public function actionUpdate()
    {
        $productModel = new ProductModel;
        $request = Yii::$app->request;
        $serialNum = $request->get("serial_num");
        $purchaseId = $request->get('purchase_id');
        if (empty($serialNum)) {
            echo "没有流水号";
            die;
        }

        //该流水号的商品信息
        $param = (new Query)->select(['p.*', 's.group_id AS sizeGroup'])
            ->from('meet_product as p')
            ->leftJoin('meet_size as s', 's.size_id = p.size_id')
            ->where(['p.serial_num' => $serialNum])
            ->andWhere(['p.disabled' => 'false'])
            ->andWhere(['p.purchase_id' => $purchaseId])
            ->one();


        //size 已选尺码
        $param['size'] = [];
        //该商品存在的尺码
        $paramSize = (new Query)->select(['size_id', 'product_sn'])
            ->from('meet_product')
            ->where(['serial_num' => $serialNum])
            ->andWhere(['disabled' => 'false'])
            ->andWhere(['purchase_id' => $purchaseId])
            ->groupBy('size_id')
            ->all();
        foreach ($paramSize as $val) {
            $param['size'][] = $val['size_id'];
        }

        //自带下拉列表 每个字段所有可选的值
        $result = $productModel->getProductFilter($param);

        //post数据
        $postParam = Yii::$app->request->post("param", []);
        if (!empty($postParam)) {
            //新多出的size数据
            $moreData = array_diff($postParam['size'], $param['size']);
            //少了的size数据
            $lessData = array_diff($param['size'], $postParam['size']); 
            $res = $productModel->updateProductOperation($postParam, $moreData, $lessData, $serialNum, $purchaseId);
            
            //清除缓存  
            PublicModel::flushCacheAll();
            if ($res) {
                //跳转到首页
                Yii::$app->session->setFlash('info', '修改成功');
                $this->redirect(['/order/product/index']);
            } else {
                //跳转到首页
                Yii::$app->session->setFlash('info', '此款号出现多个货号，禁止修改');//提示错了
                $this->redirect(['/order/product/update', 'serial_num' => $serialNum]);
            }
        }
        return $this->render('update', [
            'selectFilter' => $result,
            'param' => $param,
        ]);

    }

    /**
     * 以款号添加
     */
    public function actionChange($modelSn)
    {
        $productModel = new ProductModel();
        $guestModel = new CustomerModel();
        if (empty($modelSn)) {
            $this->redirect('/order/product/add');
        }
        $results = (new Query)->select(['p.purchase_id', 'p.brand_id', 'p.model_sn', 'p.name', 'p.cat_b', 'p.cat_m', 'p.cat_s', 'p.season_id', 'p.level_id', 'p.wave_id', 'p.scheme_id', 'p.cost_price', 'p.memo', 'p.is_down', 'p.size_id', 'p.type_id', 's.group_id AS sizeGroup'])
            ->from('meet_product as p')
            ->leftJoin('meet_size as s', 's.size_id=p.size_id')
            ->where(['p.model_sn' => $modelSn])
            ->andWhere(['p.disabled' => 'false'])
            ->one();

        $param = Yii::$app->request->post('param');
        if (!empty($param)) {
            $param['type'] = $results['type_id'];
            $param['purchase'] = $results['purchase_id'];
            $param['brand'] = $results['brand_id'];
            $param['catBig'] = $results['cat_b'];
            $param['catMiddle'] = $results['cat_m'];
            $param['catSmall'] = $results['cat_s'];
            $param['season'] = $results['season_id'];
            $param['scheme'] = $results['scheme_id'];
            $param['level'] = $results['level_id'];
            $param['wave'] = $results['wave_id'];
            $param['costPrice'] = $results['cost_price'];
            $res = $productModel->addProductOperation($param);
            
            PublicModel::flushCacheAll();
            if ($res) {
                //跳转到首页
                Yii::$app->session->setFlash('info', '修改成功');
                $this->redirect(['/order/product/index']);
            } else {
                //跳转到首页
                Yii::$app->session->setFlash('info', '此款号出现多个货号，禁止修改');//提示错了
                $this->redirect(['/order/product/update', 'serial_num' => $serialNum]);
            }
        }
        $result = $productModel->getProductFilter($results);
        $this->render('change', array(
            'selectFilter' => $result,
            'param' => $results,
        ));
    }


    public function actionExport()
    {
        $filename = '商品列表' . date('Y_m_d', time());
        $keys = array('样品代码', '样品名称', '助记符', '颜色明细', '尺码明细', '单位名称', '推荐度', '备注', '商品类别', '大类', '大类名称',
            '季节', '季节名称', '品牌', '品牌名称', '中类', '中类名称', '服装用品小类', '服装用品小类名称', '面料', '面料名称', '护肤辅料小类',
            '护肤辅料小类名称', '款式划分', '款式划分名称', '波段划分', '波段划分名称', '设计师', '设计师名称', '商品属性8', '商品属性8名称',
            '商品属性9', '商品属性9名称', '商品属性10', '商品属性10名称', '商品属性11', '商品属性11名称', '商品属性12', '商品属性12名称',
            '商品属性13', '商品属性13名称', '商品属性14', '商品属性14名称', '标签商品名称', '标签商品名称名称', '执行标准', '执行标准名称',
            '标准售价', '成本价', '关联订货会', '尺码档', '建档日期', '修档日期', '默认装箱数', '默认配码范围标识', '原货号', '摘要', '停止使用',
            '订货会特价品', '订货会默认交货日期', '必订款', '保留款', '商品代码', '规格分配', '供货商代码', '供货商名称', '主面料', '面料名称',
            '面料单耗', '幅宽', '面料成份', '款号', '款式', '明细异价');
        $productModel = new ProductModel();
        $result = $productModel->getListModel();
        if(empty($result)){
            echo "<script>alert('暂无数据');location.href='/admin.php?r=order/product/index'</script>";
            die;
        }
        $export = new IoXls();
        foreach ($result as $v) {
            $v['color_str'] = array_unique($v['color_str']);
            $v['size_str'] = array_unique($v['size_str']);
            $item[] = $v['model_sn'];                                   //样品代码
            $item[] = $v['name'];                                       //样品名称
            $item[] = $v['serial_num'];                                 //助记符
            $item[] = implode(',', $v['color_str']);                     //颜色明细
            $item[] = implode(',', $v['size_str']);                      //尺码明细
            $item[] = '';                                               //单位名称
            $item[] = '0';                                                //推荐度
            $item[] = '';                                               //备注
            $item[] = $v['type_name'];                                             //年份
            $item[] = $v['cat_b_id'];                                   //大类
            $item[] = $v['cat_b'];                                      //大类名称
            $item[] = $v['season_id'];                                  //季节
            $item[] = $v['season_name'];                                //季节名称
            $item[] = $v['brand_id'];                                   //品牌
            $item[] = $v['brand_name'];                                 //品牌名称
            $item[] = $v['cat_m_id'];                                   //中类
            $item[] = $v['cat_m'];                                      //中类名称
            $item[] = $v['cat_s_id'];                                   //服装用品小类
            $item[] = $v['cat_s'];                                      //服装用品小类名称
            $item[] = '000';                                            //面料
            $item[] = '未定义';                                         //面料名称
            $item[] = '000';                                            //护肤辅料小类
            $item[] = '未定义';                                          //护肤辅料小类名称
            $item[] = '000';                                            //款式划分
            $item[] = '未定义';                                          //款式划分名称
            $item[] = $v['wave_no'];                                    //波段划分
            $item[] = $v['wave_name'];                                  //波段划分名称
            $item[] = '000';                                      //设计师
            $item[] = '未定义';                                      //设计师名称
            $item[] = '000';                                      //商品属性8
            $item[] = '未定义';                                      //商品属性8名称
            $item[] = '000';                                      //商品属性9
            $item[] = '未定义';                                      //商品属性9名称
            $item[] = '000';                                      //商品属性10
            $item[] = '未定义';                                      //商品属性10名称
            $item[] = '000';                                      //商品属性11
            $item[] = '未定义';                                      //商品属性11名称
            $item[] = '000';                                      //商品属性12
            $item[] = '未定义';                                      //商品属性12名称
            $item[] = '000';                                      //商品属性13
            $item[] = '未定义';                                      //商品属性13名称
            $item[] = '000';                                      //商品属性14
            $item[] = '未定义';                                      //商品属性14名称
            $item[] = '000';                                      //标签商品名称
            $item[] = '未定义';                                      //标签商品名称名称
            $item[] = '000';                                      //执行标准
            $item[] = '未定义';                                      //执行标准名称
            $item[] = $v['cost_price'];                                      //标准售价
            $item[] = '0';                                      //成本价
            $item[] = $v['purchase_id'] == 1 ? '036' : '';                                      //关联订货会
            $item[] = '';                                      //尺码档
            $item[] = date('Y/m/d', time());                                      //建档日期
            $item[] = date('Y/m/d', time());;                                      //修档日期
            $item[] = '0';                                      //默认装箱数
            $item[] = '';                                      //默认配码范围标识
            $item[] = '';                                      //原货号
            $item[] = '';                                      //摘要
            $item[] = '0';                                      //停止使用
            $item[] = '0';                                      //订货会特价品
            $item[] = '1900-01-01';                                      //订货会默认交货日期
            $item[] = '0';                                      //必订款
            $item[] = '0';                                      //保留款
            $item[] = $v['model_sn'];                                      //商品代码
            $item[] = '0';                                      //规格分配
            $item[] = '';                                      //供货商代码
            $item[] = '';                                      //供货商名称
            $item[] = '';                                      //主面料
            $item[] = '';                                      //面料名称
            $item[] = '0';                                      //面料单耗
            $item[] = '0';                                      //幅宽
            $item[] = '';                                      //面料成份
            $item[] = '';                                      //款号
            $item[] = '';                                      //款式
            $item[] = '0';                                      //明细异价

            $data[] = $item;
            unset($item);
        }
        $export->export_begin($keys, $filename, count($data));
        $export->export_rows($data);
        $export->export_finish();
    }


    /**
     * 检查该款号是否存在
     */
    public function actionAjaxCheckModelsnExist($modelSn)
    {
        if (empty($modelSn)) {
            die;
        }
        $productModel = new ProductModel();
        $result = (new Query)->from('meet_product')->where(['model_sn' => $modelSn])->count();
        if ($result > 0) {
            echo json_encode(array('code' => 200, 'data' => $modelSn));
        }
    }

    /**
     * 检查此款号与色号是否存在
     */
    public function actionAjaxCheckModelsnAndColor($model_sn, $color)
    {
        if (empty($model_sn) || empty($color)) {
            die;
        }
        $result = (new Query)->select(['serial_num'])
            ->from('meet_product')
            ->where(['model_sn' => $model_sn])
            ->andWhere(['color_id' => $color])
            ->one();
        if (!empty($result)) {
            echo json_encode(['code' => 200, 'data' => $result['serial_num']]);
        }
    }

     /**
     * 判断此色系与色号是否选择对应
     */
    public function actionAjaxCheckSchemeColorExist($color, $scheme)
    {
        $color_id = $color;
        if ($scheme == "" || $color_id == "") {
            echo json_encode(array('code' => 400, 'msg' => '请先选择色系在选择色号'));
            die;
        }
        $model = new ColorModel();
        $res = $model->transColorAll();
        $scheme_id = $res[$color_id]['scheme_id'];
        if ($scheme_id != $scheme) {
            echo json_encode(array('code' => 400, 'msg' => '色系与色号不匹配，请重试'));
            die;
        }
    }


    /**
     * 根据尺码组获得尺码
     */
    public function actionAjaxSizeGroupGetsize($sizeGroup)
    {
        if (empty($sizeGroup)) {
            echo json_encode(array('code' => 400, 'msg' => '出错'));
            die;
        }
        $result = (new Query)->select(['size_id' , 'size_name'])
            ->from('meet_size')
            ->where(['group_id' => $sizeGroup])
            ->orderBy(['size_id' => SORT_ASC])
            ->all();
        echo json_encode(array('code' => 200, 'data' => $result));
    }

    /**
     * 根据大类获取中类,小类，季节
     */
    public function actionAjaxCatMiddle($catBig)
    {
        if (empty($catBig)) {
            echo json_encode(array('code' => 400, 'msg' => '出错'));
            die;
        }
        $result['middle'] = (new Query)->select(['middle_id', 'cat_name'])
            ->from('meet_cat_middle')
            ->all();
        $result['small'] = (new Query)->select(['small_id', 'small_cat_name AS cat_name'])
            ->from('meet_cat_big_small')
            ->where(['big_id' => $catBig])
            ->all();
        $result['season'] = (new Query)->select(['season_id', 'season_name'])
            ->from('meet_season_big')
            ->where(['big_id' => $catBig])
            ->all();
        echo json_encode(array('code' => 200, 'data' => $result));
    }
    /**
     * 根据大类获取小类
     */
    public function actionAjaxCatBigSmall($bigCatSmall)
    {
        $catBig = $bigCatSmall;
        if (empty($catBig)) {
            echo json_encode(array('code' => 400, 'msg' => '出错'));
            die;
        }
        $result = (new Query)->select(['small_id', 'small_cat_name AS cat_name'])
            ->from('meet_cat_big_small')
            ->where(['big_id' => $catBig])
            ->all();

        echo json_encode(array('code' => 200, 'data' => $result));
    }

    /**
     * 商品复制控制器(根据流水号添加商品)
     */
    public function actionCopy()
    {
        $productModel = new ProductModel();
        $serialNum = Yii::$app->request->get("serial_num");
        if (empty($serialNum)) {
            Yii::$app->session->setFlash('info', '流水号为空');
            $this->redirect('/order/product/index');
        }
        //数据库的显示数据
        $results = (new Query)->select(['p.purchase_id', 'p.brand_id', 'p.model_sn', 'p.name', 'p.cat_b', 'p.cat_m', 'p.cat_s', 'p.season_id', 'p.level_id', 'p.wave_id', 'p.scheme_id', 'p.cost_price', 'p.memo', 'p.is_down', 'p.size_id', 'p.type_id', 's.group_id AS sizeGroup'])
            ->from('meet_product as p')
            ->leftJoin('meet_size as s', 's.size_id = p.size_id')
            ->where(['p.serial_num' => $serialNum])
            ->andWhere(['p.disabled' => 'false'])
            ->one();
        $param = Yii::$app->request->post('param');
        if (!empty($param)) {
            $param['brand'] = $results['brand_id'];
            $param['catBig'] = $results['cat_b'];
            $param['catMiddle'] = $results['cat_m'];
            $param['catSmall'] = $results['cat_s'];
            $param['season'] = $results['season_id'];
            $param['scheme'] = $results['scheme_id'];
            $param['level'] = $results['level_id'];
            $param['wave'] = $results['wave_id'];
            $param['purchase'] = $results['purchase_id'];
            $param['type'] = $results['type_id'];
            $param['costPrice'] = $results['cost_price'];
            $res = $productModel->addProductOperation($param);
            PublicModel::flushCacheAll();
            if ($res) {
                //跳转到首页
                Yii::$app->session->setFlash('info', '添加成功');
                $this->redirect(['/order/product/index']);
            } else {
                //跳转到首页
                Yii::$app->session->setFlash('info', '添加失败');
                $this->redirect(['/order/product/update', 'serial_num' => $serialNum]);
            }
        }
        $result = $productModel->getProductFilter($results);
        return $this->render('copy', array(
            'selectFilter' => $result,
            'param' => $results,
        ));
    }

    /**
     * 导入界面
     */
    public function actionImport()
    {
        if (!Yii::$app->params['product_include']) {
            echo "502 forbidden";
            die;
        }
        return $this->render('import');
    }

    /**
     * 上传商品
     */
    public function actionImportFiles()
    {

        $postFile = isset($_FILES["file"]) ? $_FILES['file'] : exit("请上传文件");

        $postFileType = pathinfo($postFile['name'], PATHINFO_EXTENSION);
        $allowExt = array('xls', 'xlsx');
        if (empty($postFile)) {
            exit("请上传文件");
        }

        if (!in_array($postFileType, $allowExt)) {
            exit("上传文件不支持类型，仅限传xls后缀名文件,请先下载导入模板再执行操作");
        }
        // var_dump($postFile);exit;
        if (!is_uploaded_file($postFile['tmp_name'])) {
            exit("不是通过HTP POST上传的文件");
        }
        $productModel = new ProductModel();
        $nowTime = time();
        $newFileName = $nowTime . "." . $postFileType;
        $newFolder = date("Ymd", time());
        $transData = $newFolder . "/" . $newFileName;   //上传文件地址
        $newFolderPath = "upload/" . $newFolder . "/"; //新地址
        if (!file_exists($newFolderPath)){
            mkdir($newFolderPath, 0777);
        }

        $newFile = Yii::$app->basePath . "/" . $newFolderPath . "/" . $newFileName;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $newFile)) {

            //xls
            $objPHPExcel = new PHPExcel();
            $objPHPExcel = PHPExcel_IOFactory::load($newFile);
            $result = $objPHPExcel->getActiveSheet()->toArray();

            $len_result = count($result);
            if ($len_result <= 1) {
                echo "<script>alert('表中没有相关数据，请检查');</script>";
                die;
            }

            $purchase = array(
                Yii::$app->params['purchase_oct'],
                Yii::$app->params['purchase_uki'],
            );
            $res_str = "";
            $schemeIdToName = [];
            //色系id对应色系记录
            $schemeIdToName = (new SchemeModel)->getList('scheme_id');

$brandArr = (new BrandModel)->transBrandName();
$colorArr = (new ColorModel)->transColorNo();
$sizeArr = (new PublicModel)->sizeList();
$catBigArr = (new CatBigModel)->getList('cat_name');
$catMidArr = (new CatMiddleModel)->getList('cat_name');
$catSmallArr = (new CatSmallModel)->getList('cat_name');
$seasonArr = (new SeasonModel)->getList('scheme_name');
$waveArr = (new WaveModel)->getList('wave_name');
$levelArr = (new LevelModel)->getList('level_name');
$schemeArr = (new SchemeModel)->getList('scheme_name');
$typeArr = (new TypeModel)->getList('type_name');
$groupSize = (new PublicModel)->getGroupSize();


            for ($i = 1; $i < $len_result; $i++) {

                $warning = "";
                //判断款号
                if (empty($result[$i][0])) {
                    $warning .= "<span><b>款号为空</b></span>";
                } elseif (strlen($result[$i][0]) < 7) {
                    $warning .= "<span><b>款号小于7位数</b></span>";
                } else {
                    $model_sn = $result[$i][0];
                }
                //判断订货会
                if (!in_array($result[$i][1], $purchase)) {
                    $warning .= "<span>订货会<b>" . $result[$i][1] . "</b>错误</span>";
                }
                // 判断品牌
                if (!isset($brandArr[$result[$i][2]]['brand_id'])) {
                    $warning .= "<span>品牌<b>" . $result[$i][2] . "</b>有错误,错误</span>";
                }
                //品名
                if (empty($result[$i][3])) {
                    $warning .= "<span><b>品名为空</b></span>";
                }
                //流水号
                if (empty($result[$i][4])) {
                    $warning .= "<span><b>流水号为空</b></span>";
                }

                $color = 2;
                //判断色号

                if (!isset($colorArr[$result[$i][5]]['color_id'])) {
                    $warning .= "<span>颜色:色号<b>" . $result[$i][5] . "</b>有错误</span>";
                    $color--;
                }
                // 尺码
                
                if (!isset($sizeArr[$result[$i][6]]['size_id'])) {
                    $warning .= "<span>尺码<b>" . $result[$i][6] . "</b>有错误</span>";
                }

                $size = 3;
                // 大类
                
                if (!isset($catBigArr[$result[$i][7]]['big_id'])) {
                    $warning .= "<span>大类<b>" . $result[$i][7] . "</b>有错误</span>";
                    $size--;
                }
                //中类
                
                if (!isset($catMidArr[$result[$i][8]]['middle_id'])) {
                    $warning .= "<span>中类<b>" . $result[$i][8] . "</b>有错误</span>";
                    $size--;
                }
                // 小类
                
                if (!isset($catSmallArr[$result[$i][9]]['small_id'])) {
                    $warning .= "<span>小类<b>" . $result[$i][9] . "</b>错误</span>";
                    $size--;
                }
                // 大小类匹配
                if($size == '3'){
                    $catBigSmall = (new Query)->from('meet_cat_big_small')
                        ->where(['big_cat_name' => $result[$i][7]])
                        ->andWhere(['small_cat_name' => $result[$i][9]])
                        ->one();

                    if (empty($catBigSmall)) {
                        $warning .= "<span><b>大类 {$result[$i][7]} , 小类 {$result[$i][9]} 不匹配</b></span>";
                    }
                }
                //季节
                
                if (!isset($seasonArr[$result[$i][10]]['season_id'])) {
                    $warning .= "<span>季节<b>" . $result[$i][10] . "</b>有错误</span>";
                }
                // 波段
                
                if (!isset($waveArr[$result[$i][11]]['wave_id'])) {
                    $warning .= "<span>波段<b>" . $result[$i][11] . "</b>有错误</span>";
                }
                // 等级
                
                if (!isset($levelArr[$result[$i][12]]['level_id'])) {
                    $warning .= "<span>等级<b>" . $result[$i][12] . "</b>有错误</span>";
                }
                // 色系
                
                if (!isset($schemeArr[$result[$i][13]]['scheme_id'])) {
                    $warning .= "<span>色系<b>" . $result[$i][13] . "</b>有错误</span>";
                    $color--;
                } else {
                    $scheme_id = $schemeArr[$result[$i][13]]['scheme_id'];
                }
                // 色系和颜色是否匹配
                if ($color == 2) {
                    $color_id = $colorArr[$result[$i][5]]['scheme_id'];
                    if ($color_id != $scheme_id) {
                        $warning .= "<span><b>色系与颜色不对应，excel表色系 {$result[$i][13]} 色号{$result[$i][5]} ；数据库色系 {$schemeIdToName[$colorArr[$result[$i][5]]['scheme_id']]['scheme_name']} 色号 {$colorArr[$result[$i][5]]['color_no']} </b></span>";
                    }
                }

                $price_isset = 0;
                // 价格带
                if (!isset(ParamsClass::$levelPrice[$result[$i][14]])) {
                    $warning .= "<span>价格带<b>" . $result[$i][14] . "</b>数据有误</span>";
                    $price_isset++;
                }

                if ($result[$i][15] < 0 || empty($result[$i][15])) {
                    $warning .= "<span><b>吊牌价小于0</b></span>";
                    $price_isset++;
                }

                if (empty($price_isset)) {
                    if ($productModel->_transCostPriceToLevel($result[$i][15]) != ParamsClass::$levelPrice[$result[$i][14]]) {
                        $warning .= "<span><b>吊牌价与价格带不匹配</b></span>";
                    }
                }

                if (empty($result[$i][16])) {
                    $warning .= "<span><b>描述为空</b></span>";
                }

                
                if(!isset($typeArr[$result[$i][17]])){
                    $warning .= "<span>商品类型<b>{$result[$i][17]}</b>有错误</span>";
                }

                //检查同款号下的数据是否相同
                if ($color == 2) {
                    // $checkCatRepeat[$result[$i][0]]['purchase'][] = $result[$i][1];
                    $checkCatRepeat[$result[$i][0]]['brand'][] = $result[$i][2];
                    $checkCatRepeat[$result[$i][0]]['cat_big'][] = $result[$i][7];
                    $checkCatRepeat[$result[$i][0]]['cat_middle'][] = $result[$i][8];
                    $checkCatRepeat[$result[$i][0]]['cat_small'][] = $result[$i][9];
                    $checkCatRepeat[$result[$i][0]]['season'][] = $result[$i][10];
                    $checkCatRepeat[$result[$i][0]]['cost_price'][] = $result[$i][15];
                    $checkCatRepeat[$result[$i][0]]['price_lv'][] = $result[$i][14];
                    $checkCatRepeat[$result[$i][0]]['type_id'][] = $result[$i][17];
                }

                //检查流水号下的颜色是否重复
                $checkSerialRepeat[$result[$i][4]][$result[$i][5]] = $result[$i][5];

                //检查该款号下的颜色是否有多个流水号
                $checkModelRepeat[$result[$i][0]][$result[$i][5]][] = $result[$i][4];

                //判断一个颜色只能对应一个流水号
                $checkSerialModelRepeat[$result[$i][4]][] = $result[$i][0];
//xiugai2017-08-02
//检查此流水号下的尺码是否重复
$checkPurchaseSizeRepeat[$result[$i][4]][$result[$i][1]][] = $result[$i][6];
                //检查此流水号下的尺码是否重复
                $checkSerialSizeRepeat[$result[$i][4]][] = $result[$i][6];

                //检查同一流水号下的商品名是否统一
                $checkSerialName[$result[$i][4]][] = $result[$i][3];

                //检查款号下的大类是否对应的季节
                $checkCatBigSeason[$result[$i][0]][] = $result[$i][7]."_".$result[$i][10];

                if (empty($warning)) {
                    // 检查款号或流水号是否存在
                    $result = $productModel->find()
                        ->where(['model_sn' => $model_sn])
                        ->orWhere(['serial_num' => $result[$i][4]])
                        ->count();
                    if (empty($result)) {
                        $warning .= "<span><b>此产品流水号/款号已存在,请到商品管理添加修改</b></span>";
                    }
                }

                if (!empty($warning)) {
                    $res_str .= "<p>第" . ($i + 1) . "行 &nbsp;&nbsp;&nbsp;&nbsp;".$result[$i][3] .'&nbsp;&nbsp;&nbsp;&nbsp;'. $warning . "</p>";
                }
            }

            //检查流水号下有多种尺码，请检查！
            if (empty($res_str)) {
                
                foreach ($checkSerialSizeRepeat as $serialNum => $val) {
                    $group_id = (new Query)->select(['group_id'])
                        ->from('meet_size')
                        ->where(['size_name' => $val[0]])
                        ->one();
                    $countSize = count($val);
                    $i = 0;
                    foreach ($val as $v) {
                        if (in_array($v, $groupSize[$group_id['group_id']])) {
                            $i++;
                        }
                    }
                    if ($countSize != $i) {
                        if ($i != 0) {
                            $res_str .= "<p><span><b>{$serialNum}流水号下</b>有多种尺码，请检查！</span></p>";
                        }
                    }
                }
            }
            //检查同一流水号下的商品名是否统一
            foreach ($checkSerialName as $serialModel => $val) {
                if (count(array_unique($val)) >= 2) {
                    $res_str .= "<p><span><b>{$serialModel}流水号下</b>有不同的商品名</span></p>";
                }
            }
// var_dump($checkCatRepeat);exit;
            //检查同款号下的数据是否相同
            if (empty($res_str)) {
                foreach ($checkCatRepeat as $modelSnKey => $modelValue) {
                    foreach ($modelValue as $nameKey => $resValue) {
                        if (count(array_unique($resValue)) >= 2) {
                            $error = implode(',', array_unique($resValue));
                            $res_str .= "<p><span><b>{$modelSnKey}款号下</b>有不同的商品数据 {$error}</span></p>";
                        }
                    }
                }
            }

            //检查流水号下的颜色是否重复
            if (empty($res_str)) {
                foreach ($checkSerialRepeat as $re => $peat) {
                    if (count($peat) >= 2) {
                        $res_str .= "<p><span><b>{$re}流水号</b>下有多种颜色</span></p>";
                    }
                }
            }

            //检查该款号下的颜色是否有多个流水号
            if (empty($res_str)) {
                foreach ($checkModelRepeat as $model => $serial) {
                    foreach ($serial as $val) {
                        if (count(array_unique($val)) >= 2) {
                            $res_str .= "<p><span><b>{$model}款号</b>下的一种颜色有多个流水号</span></p>";
                        }
                    }
                }
            }

            //判断一个颜色只能对应一个流水号
            if (empty($res_str)) {
                foreach ($checkSerialModelRepeat as $key => $val) {
                    if (count(array_unique($val)) >= 2) {
                        $res_str .= "<p><span><b>{$key}流水号</b>下有多个款号</span></p>";
                    }
                }
            }

            //检查此流水号下的尺码是否重复
            if (empty($res_str)) {
                foreach ($checkPurchaseSizeRepeat as $sizeKey => $sizeValue) {
                    foreach ($sizeValue as $key => $value) {
                        if (count($value) != count(array_unique($value))) {
                            $res_str .= "<p><span><b>{$sizeKey}流水号</b>下有重复的尺码</span></p>";
                        }
                    }
                }
            }

            //检查此款号下的季节是否相同、对应
            if(empty($res_str)){
                $seasonBigArr = (new Query)->select(['big_name', 'season_name'])
                    ->from('meet_season_big')
                    ->all();
                foreach ($seasonBigArr as $val) {
                    $arr[$val['big_name'] . "_" . $val['season_name']][] = $val['big_name'] . "_" . $val['season_name'];
                }
                $season_big = $arr;
                foreach($checkCatBigSeason as $modelCat => $modelBig){
                    if(count(array_unique($modelBig)) != 1){
                        $res_str .= "<p><span><b>{$modelCat}款号</b>下的有多的大类与季节</span></p>";
                    }else{
                        if(!in_array(array_unique($modelBig), $season_big)){
                            $res_str .= "<p><span><b>{$modelCat}款号</b>下的大类与季节不对应</span></p>";
                        }
                    }
                }
            }

            if (empty($res_str)) {
                $result_upload = $this->uploadThisFile($newFile);
                if ($result_upload) {
                    echo "<script>alert('上传成功');location.href='/order/product/index'</script>";
                } else {
                    echo "<script>alert('上传失败');location.href='/order/product/import'</script>";
                }
            } else {
                return $this->render('importdetail', array(
                    'warning' => $res_str,
                ));
            }
        } else {
            exit("上传失败");
        }
    }
    //导入文件数据
    public function uploadThisFile($file)
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        $result = $objPHPExcel->getActiveSheet()->toArray();
        $len_result = count($result);
        $data_values = [];
        $keys = ['purchase_id', 'product_sn', 'style_sn', 'model_sn', 'serial_num', 'name', 'img_url', 'color_id', 'size_id', 'brand_id', 'cat_b', '
                cat_m', 'cat_s', 'season_id', 'level_id', 'wave_id', 'scheme_id', 'cost_price', 'price_level_id', 'memo', 'type_id'];

$brandArr = (new BrandModel)->transBrandName();
$colorArr = (new ColorModel)->transColorNo();
$sizeArr = (new PublicModel)->sizeList();
$catBigArr = (new CatBigModel)->getList('cat_name');
$catMidArr = (new CatMiddleModel)->getList('cat_name');
$catSmallArr = (new CatSmallModel)->getList('cat_name');
$seasonArr = (new SeasonModel)->getList('scheme_name');
$waveArr = (new WaveModel)->getList('wave_name');
$levelArr = (new LevelModel)->getList('level_name');
$schemeArr = (new SchemeModel)->getList('scheme_name');
$typeArr = (new TypeModel)->getList('type_name');
$groupSize = (new PublicModel)->getGroupSize();
        for ($i = 1, $num = 1; $i < $len_result; $i++, $num++) {
            $purchase_id = $result[$i][1] == Yii::$app->params['purchase_oct'] ? 1 : 2;
            $color_id = $colorArr[$result[$i][5]]['color_id'];
            $size_id = $sizeArr[$result[$i][6]]['size_id'];
            $brand_id = $brandArr[$result[$i][2]]['brand_id'];
            $b_cat_id = $catBigArr[$result[$i][7]]['big_id'];
            $m_cat_id = $catMidArr[$result[$i][8]]['middle_id'];
            $s_cat_id = $catSmallArr[$result[$i][9]]['small_id'];
            $season_id = $seasonArr[$result[$i][10]]['season_id'];
            $wave_id = $waveArr[$result[$i][11]]['wave_id'];
            $level_id = $levelArr[$result[$i][12]]['level_id'];
            $scheme_id = $schemeArr[$result[$i][13]]['scheme_id'];
            $price_level_id = ParamsClass::$levelPrice[$result[$i][14]];
            $type_id = $typeArr[$result[$i][17]]['type_id'];
            $serial_num = $result[$i][4];
            $cost_price = $result[$i][15];
            $memo = $result[$i][16];
            $name = $result[$i][3];
            $model_sn = $result[$i][0];
            if ($i > 1 && $model_sn != $result[$i - 1][0]) {
                $num = 1;
            }
            $style_sn = $model_sn . sprintf('%04d', $export->color[$result[$i][5]]['color_no']);
            $product_sn = $style_sn . sprintf('%03d', $num);
            $img_url = Yii::$app->params['imagePath'] . $model_sn . '_' . $colorArr[$result[$i][5]]['color_no'] . '.jpg';
            $data_values[] = [$purchase_id, $product_sn, $style_sn, $model_sn, $serial_num, $name, $img_url,
                         $color_id, $size_id, $brand_id, $b_cat_id, $m_cat_id, $s_cat_id, $season_id,
                         $level_id, $wave_id, $scheme_id, $cost_price, $price_level_id, $memo, $type_id];
        }
        
        //批量导入
$result = Yii::$app->db
->createCommand()
->batchInsert(ProductModel::tableName(),
    $keys,
    $data_values
    )
->execute();

        //添加日志

    }
}