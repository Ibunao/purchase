<?php
namespace frontend\modules\order\controllers;

use Yii;
use frontend\controllers\base\BaseController;
use frontend\models\CustomerModel;
use PHPExcel;
use PHPExcel_IOFactory;
use frontend\helpers\IoXls;
/**
 *用户管理
 * @author        ding
 */
class ManageController extends BaseController
{
    public $admin;

    public function actionIndex()
    {
        $param = Yii::$app->request->get('param');

        $guestMange = new CustomerModel;
        //
        $insertOption = $guestMange->userFilter();

        $selectResult = $guestMange->selectLikeDatabaseOperation($param);//查询检索的数据

        return $this->render('index', [
            'param' => $param,
            'insert_option' => $insertOption,//显示内容
            'select_result' => $selectResult['result'],//获得内容
            'pagination' => $selectResult['pagination'],
        ]);
    }

    /**
     * 添加用户控制器
     */
    public function actionAdd()
    {
        $guestModel = new CustomerModel;
        if (!empty(Yii::$app->request->post('param'))) {
            $arr = Yii::$app->request->post('param');
            //插入
            $res = $guestModel->insertDatabaseOperation($arr);
            if ($res) {
                Yii::$app->session->setFlash('info', '添加成功');
                $this->redirect('/order/manage/index');//如果成功，跳转
            } else {
                Yii::$app->session->setFlash('info', '添加失败');

                $this->redirect('/order/manage/index');
            }
        }
        $insert_option = $guestModel->userFilter();//显示自带的结果
        return $this->render('add', array(
            'insert_option' => $insert_option,
        ));
    }

    /**
     * 用户导入界面
     */
    public function actionImport()
    {
        if (!Yii::$app->params['customer_include']) {
            echo "502 forbidden";
            die;
        }
        return $this->render('import');
    }

    /**
     * 用于导入
     */
    public function actionUserImport()
    {
        header('Content-Type:text/html;Charset=GB2312;');
        //折扣是按照百分比的显示, 百分比就是xls的折扣写的是[50%], 而不是百分比的就会写[0.5]
        $offPrize = 'percent';
        $percentTarget = false;
        $postFile = !empty($_FILES["file"]['name']) ? $_FILES['file'] : exit("请上传文件");
        //获取文件后缀
        $postFileType = pathinfo($postFile['name'], PATHINFO_EXTENSION);
        $allowExt = ['xls', 'xlsx', 'csv'];
        if (!in_array($postFileType, $allowExt)) {
            exit("上传文件不支持类型，仅限传xls后缀名文件,请先下载导入模板再执行操作");
        }

        if (!is_uploaded_file($postFile['tmp_name'])) {
            exit("不是通过HTTP POST上传的文件");
        }

        $nowTime = time();
        $newFileName = $nowTime . "." . $postFileType;
        $newFolder = date("Ymd", time());
        $newFolderPath = "upload/" . $newFolder . "/";  //新地址
        if (!file_exists($newFolderPath))
        { 
            mkdir($newFolderPath, 0777);
        }

        $newFile = Yii::$app->basePath . '/web/' . $newFolderPath . $newFileName;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $newFile)) {

            $objPHPExcel = new PHPExcel();
            $objPHPExcel = PHPExcel_IOFactory::createReaderForFile($newFile);
            $objPHPExcel = PHPExcel_IOFactory::load($newFile);
            $result = $objPHPExcel->getActiveSheet()->toArray();
            $len_result = count($result);

            if ($len_result <= 1) {
                echo "<script>alert('表中没有相关数据，请检查');</script>";
                die;
            }
            $customer = new CustomerModel();
            $list = $customer->userFilter();
            $list['purchase'] = array(
                Yii::$app->params['purchase_oct'],
                Yii::$app->params['purchase_uki'],
            );
            $agent = new AgentModel();
            $agent_code = $agent->transAgentCode();
            $res_str = '';
            $guest_list = $customer->transAllGuest();
            for ($i = 1; $i < $len_result; $i++) {

                $warning = '';

                if (empty($result[$i][0])) {
                    $warning .= "<span><b>客户代码为空</b></span>";
                } elseif (isset($guest_list[$result[$i][0]])) {
                    $warning .= "<span>客户代码<b>" . $result[$i][0] . "</b>已存在</span>";
                }

                $code[] = $result[$i][0];
                if (empty($result[$i][1])) {
                    $warning .= "<span><b>客户名称为空</b></span>";
                }

                if (empty($result[$i][2])) {
                    $warning .= "<span><b>手机为空</b></span>";
                }

                if (empty($result[$i][3])) {
                    $warning .= "<span><b>订货会为空</b></span>";
                } elseif (!in_array($result[$i][3], $list['purchase'])) {
                    $warning .= "<span>订货会<b>" . $result[$i][3] . "</b>有错误</span>";
                }

                if (empty($result[$i][4])) {
                    $warning .= "<span><b>部门类别为空</b></span>";
                } elseif (!in_array($result[$i][4], $list['department'])) {
                    $warning .= "<span>部门类别<b>" . $result[$i][4] . "</b>有错误</span>";
                }

                if (empty($result[$i][5])) {
                    $warning .= "<span><b>客户类型为空</b></span>";
                } elseif (!in_array($result[$i][5], $list['type'])) {
                    $warning .= "<span>客户类型<b>" . $result[$i][5] . "</b>有错误</span>";
                }

                if (empty($result[$i][6])) {
                    $warning .= "<span><b>区域为空</b></span>";
                } elseif (!in_array($result[$i][6], $list['area'])) {
                    $warning .= "<span>区域<b>" . $result[$i][6] . "</b>有错误</span>";
                }

                if (empty($result[$i][7])) {
                    $warning .= "<span><b>省份为空</b></span>";
                } elseif (!in_array($result[$i][7], $list['province'])) {
                    $warning .= "<span>省份<b>" . $result[$i][7] . "</b>有错误</span>";
                }

                if (empty($result[$i][8])) {
                    $warning .= "<span><b>负责人为空</b></span>";
                } elseif (!in_array($result[$i][8], $list['leader'])) {
                    $warning .= "<span>负责人<b>" . $result[$i][8] . "</b>有错误</span>";
                }

                if (empty($result[$i][9])) {
                    $warning .= "<span><b>代理名称为空</b></span>";
                }

                if (empty($result[$i][10])) {
                    $warning .= "<span><b>代理代码为空</b></span>";
                }

                if (!isset($agent_code[$result[$i][10] . '_' . $result[$i][9]])) {
                    $warning .= "<span><b>代理名称与代理代码不匹配</b></span>";
                }

                //判断指标
                if ($percentTarget) {
                    //导入指标按百分比
                    $t_c1 = rtrim($result[$i][13], '%');
                    $t_c2 = rtrim($result[$i][14], '%');
                    $t_c3 = rtrim($result[$i][15], '%');
                    $t_c4 = rtrim($result[$i][16], '%');
                    $t_c6 = rtrim($result[$i][17], '%');
                    if (!empty($result[$i][12])) {
                        if (!empty($t_c1) || !empty($t_c2) || !empty($t_c3) || !empty($t_c4) || !empty($t_c6)) {
                            $count_all = ($t_c1 + $t_c2 + $t_c3 + $t_c4 + $t_c6);
                            if ($count_all != 100) {
                                $warning .= "<span><b>各大类指标总和不等于100%</b></span>";
                            }
                        }
                    }
                } else {
                    //导入指标按照金额
                    $t_c1 = rtrim($result[$i][13]);
                    $t_c2 = rtrim($result[$i][14]);
                    $t_c3 = rtrim($result[$i][15]);
                    $t_c4 = rtrim($result[$i][16]);
                    $t_c6 = rtrim($result[$i][17]);
                    if (!empty($result[$i][12])) {
                        $count_all = ($t_c1 + $t_c2 + $t_c3 + $t_c4 + $t_c6);
                        if ($count_all != $result[$i][12]) {
                            $warning .= "<span><b>各大类指标总和不等于总指标</b></span>";
                        }
                    }
                }

                $d_c1 = rtrim($result[$i][18], '%');
                $d_c2 = rtrim($result[$i][19], '%');
                $d_c3 = rtrim($result[$i][20], '%');
                $d_c4 = rtrim($result[$i][21], '%');
                $d_c6 = rtrim($result[$i][22], '%');
                if ($offPrize == 'percent') {
                    if (!empty($d_c1)) {
                        if ($d_c1 <= 0 || $d_c1 > 100) {
                            $warning .= '<span><b>服装折扣</b>应该在0-100且不能与0之间</span>';
                        }
                    }

                    if (!empty($d_c2)) {
                        if ($d_c2 <= 0 || $d_c2 > 100) {
                            $warning .= '<span><b>家居折扣</b>应该在0-100且不能与0之间</span>';
                        }
                    }

                    if (!empty($d_c3)) {
                        if ($d_c3 <= 0 || $d_c3 > 100) {
                            $warning .= '<span><b>防辐射折扣</b>应该在0-100且不能与0之间</span>';
                        }
                    }

                    if (!empty($d_c4)) {
                        if ($d_c4 <= 0 || $d_c4 > 100) {
                            $warning .= "<span><b>备品折扣</b>应该在0-100且不能与0之间</span>";
                        }
                    }

                    if (!empty($d_c6)) {
                        if ($d_c6 <= 0 || $d_c6 > 100) {
                            $warning .= "<span><b>化妆品折扣</b>应该在0-100且不能与0之间</span>";
                        }
                    }
                } else {

                    if (!empty($d_c1)) {
                        if ($d_c1 <= 0 || $d_c1 > 1) {
                            $warning .= '<span><b>服装折扣</b>应该在0-1且不能与0之间</span>';
                        }
                    }

                    if (!empty($d_c2)) {
                        if ($d_c2 <= 0 || $d_c2 > 1) {
                            $warning .= '<span><b>家居折扣</b>应该在0-1且不能与0之间</span>';
                        }
                    }

                    if (!empty($d_c3)) {
                        if ($d_c3 <= 0 || $d_c3 > 1) {
                            $warning .= '<span><b>防辐射折扣</b>应该在0-1且不能与0之间</span>';
                        }
                    }

                    if (!empty($d_c4)) {
                        if ($d_c4 <= 0 || $d_c4 > 1) {
                            $warning .= "<span><b>备品折扣</b>应该在0-1且不能与0之间</span>";
                        }
                    }

                    if (!empty($d_c6)) {
                        if ($d_c6 <= 0 || $d_c6 > 1) {
                            $warning .= "<span><b>化妆品折扣</b>应该在0-1且不能与0之间</span>";
                        }
                    }

                }

                if (!empty($warning)) {
                    $res_str .= "<p>第" . ($i + 1) . "行 &nbsp;&nbsp;&nbsp;&nbsp;" . $warning . "</p>";
                }
            }
            if (count($code) != count(array_unique($code))) {
                $res_str .= "<p><span><b>客户代码有重复，请检查</b></span></p>";
            }
            if (empty($res_str)) {
                $res = $this->addCsvData($percentTarget, $result);
                if ($res) {
                    Yii::$app->session->setFlash('info', '上传成功');
                    $this->redirect(['/order/manage/index']);//上传成功
                } else {
                    Yii::$app->session->setFlash('info', '上传失败');
                    $this->redirect(['/order/manage/import']);//上传失败
                }
            } else {
                $this->render('errordetail', array(
                    'error' => $res_str,
                ));
            }
        }
    }

    /**
     * 
     * 添加上传的csv文件的数据  客户数据
     * @param $percentTarget
     * @return bool
     */
    public function addCsvData($percentTarget, $result)
    {
        $len_result = count($result);

        $data_values = '';
        $keys = ['purchase_id', 'code', 'name', 'password', 'mobile', 'type', 'province', 'area', 'target', 'leader', 'leader_name', 'agent', 'department', 'parent_id', 'relation_code', 'big_1', 'big_2', 'big_3', 'big_4', 'big_6', 'big_1_count', 'big_2_count', 'big_3_count', 'big_4_count', 'big_6_count'];
        for ($i = 1; $i < $len_result; $i++) {
            $code = ltrim($result[$i][0], "'");
            $name = $result[$i][1];
            $password = md5(md5(substr($result[$i][2], -4)));
            $mobile = $result[$i][2];
            $purchase_id = $result[$i][3] == Yii::$app->params['purchase_oct'] ? 1 : 2;
            $department = $result[$i][4];
            $type = $result[$i][5];
            $area = $result[$i][6];
            $province = $result[$i][7];
            $leader = $result[$i][8];
            $leader_name = $result[$i][9];
            $agent = ltrim($result[$i][10], "'");
            $relation_code = $result[$i][11];
            $target = $result[$i][12];
            if($percentTarget){
                $target_cat1 = rtrim($result[$i][13], "%") * $target / 100;
                $target_cat2 = rtrim($result[$i][14], "%") * $target / 100;
                $target_cat3 = rtrim($result[$i][15], "%") * $target / 100;
                $target_cat4 = rtrim($result[$i][16], "%") * $target / 100;
                $target_cat6 = rtrim($result[$i][17], "%") * $target / 100;
            }else{
                $target_cat1 = $result[$i][13];
                $target_cat2 = $result[$i][14];
                $target_cat3 = $result[$i][15];
                $target_cat4 = $result[$i][16];
                $target_cat6 = $result[$i][17];
            }
            $discount_cat1 = rtrim($result[$i][18], "%");
            $discount_cat2 = rtrim($result[$i][19], "%");
            $discount_cat3 = rtrim($result[$i][20], "%");
            $discount_cat4 = rtrim($result[$i][21], "%");
            $discount_cat6 = rtrim($result[$i][22], "%");
            if ($code == $agent) {
                $parent_id = 1;
            } else {
                $parent_id = 0;
            }
            if ($offPrize == 'percent') {
                if (empty($discount_cat1)) {
                    $discount_cat1 = 100;
                }
                if (empty($discount_cat2)) {
                    $discount_cat2 = 100;
                }
                if (empty($discount_cat3)) {
                    $discount_cat3 = 100;
                }
                if (empty($discount_cat4)) {
                    $discount_cat4 = 100;
                }
                if (empty($discount_cat6)) {
                    $discount_cat6 = 100;
                }
            } else {
                if (empty($discount_cat1)) {
                    $discount_cat1 = 100;
                } else {
                    $discount_cat1 *= 100;
                }
                if (empty($discount_cat2)) {
                    $discount_cat2 = 100;
                } else {
                    $discount_cat2 *= 100;
                }
                if (empty($discount_cat3)) {
                    $discount_cat3 = 100;
                } else {
                    $discount_cat3 *= 100;
                }
                if (empty($discount_cat4)) {
                    $discount_cat4 = 100;
                } else {
                    $discount_cat4 *= 100;
                }
                if (empty($discount_cat6)) {
                    $discount_cat6 = 100;
                } else {
                    $discount_cat6 *= 100;
                }
            }
            $data_values[] = [$purchase_id, $code, $name, $password, $mobile, $type, $province, $area, $target, $leader, $leader_name, $agent, $department, $parent_id, $relation_code, $target_cat1, $target_cat2, $target_cat3, $target_cat4, $target_cat6, $discount_cat1, $discount_cat2, $discount_cat3, $discount_cat4, $discount_cat6];
        }
        return Yii::$app->db->createCommand()->batchInsert(CustomerModel::tableName(), $keys, $data_values)->execute();
    }

    /**
     * 导出用户
     */
    public function actionExport()
    {
        $filename = '客户列表' . date('Y_m_d', time());
        $keys = array('客户ID', '是否为代理', '订货会', '客户代码', '客户名称', '手机号码', '客户类型', '省份', '地区', '订货目标', '部门类别', '负责人', '代理名称', '代理代码', '客户关系代码', '服装指标', '家居指标', '防辐射指标', '备品指标', '化妆品指标', '服装折扣', '家居折扣', '防辐射折扣', '备品折扣', '化妆品折扣');
        $customerModel = new CustomerModel();
        $result = $customerModel->getAllCustomers();
        $export = new IoXls();
        foreach ($result as $v) {
            $item = [];
            $item[] = $v['customer_id'];
            $item[] = $v['parent_id'] == 1 ? "是" : "否";
            $item[] = $v['purchase_id'] == 1 ? 'OCT' : 'UKI';
            $item[] = $v['code'];
            $item[] = $v['name'];
            $item[] = $v['mobile'];
            $item[] = $v['type'];
            $item[] = $v['province'];
            $item[] = $v['area'];
            $item[] = $v['target'];
            $item[] = $v['department'];
            $item[] = $v['leader'];
            $item[] = $v['leader_name'];
            $item[] = $v['agent'];
            $item[] = $v['relation_code'];
            $item[] = $v['big_1'];
            $item[] = $v['big_2'];
            $item[] = $v['big_3'];
            $item[] = $v['big_4'];
            $item[] = $v['big_6'];
            $item[] = $v['big_1_count'] . '%';
            $item[] = $v['big_2_count'] . '%';
            $item[] = $v['big_3_count'] . '%';
            $item[] = $v['big_4_count'] . '%';
            $item[] = $v['big_6_count'] . '%';
            $data[] = $item;
        }
        $export->export_begin($keys, $filename, count($data));
        $export->export_rows($data);
        $export->export_finish();
    }

    /**
     * Ajax 检查客户代码是否存在
     * @param  [type] $codes [description]
     * @return [type]        [description]
     */
    public function actionAjax($codes)
    {
        $code = trim($codes);
        $manage = new CustomerModel();
        $result = $manage->checkCode($code);
        if ($result < 1) {
            $mew = "<script>$(document).ready(function() { $('.btn-primary').show();});</script>";
        } else {
            $mew = "<b style='color:red'>对不起，客户代码重复，请重新输入！</b><script>$(document).ready(function() { $('.btn-primary').hide();});</script>";
        }
        echo json_encode($mew);
    }

    /**
     * 客户修改
     */
    public function actionUpdate()
    {
        if (!Yii::$app->params['update_customer_info']) {
            echo "502 forbidding";
            die;
        }
        $user_id = Yii::$app->request->get('id', '');
        $guestModel = new CustomerModel();//实例化一个模型
        if ($_POST) {
            $arr = $_POST['param'];
            $res = $guestModel->updateDbOperation($arr);//修改
            if ($res) {
                Yii::$app->session->setFlash('info', '修改成功');
                return $this->redirect('/order/manage/index');
            } else {
                echo "<script>alert('出错');</script>";
                die;
            }
        }
        $insert_option = $guestModel->userFilter();//显示自带的结果
        $select_result = $guestModel->selectDbOperation($user_id);//查找该用户的数据结果
        return $this->render('update', array(
            'insert_option' => $insert_option,
            'select_result' => $select_result,
        ));
    }
    /**
     * 客户复制页面
     */
    public function actionCopy()
    {
        $userId = isset($_GET['id']) ? $_GET['id'] : '';
        $guestModel = new CustomerModel();//实例化一个模型
        if ($_POST) {
            $arr = $_POST['param'];
            unset($arr['id']);
            $res = $guestModel->insertDbOperation($arr);//新增操作
            if ($res) {
                Yii::$app->session->setFlash('info', '复制成功');
                return $this->redirect('/order/manage/index');
            } else {
                Yii::$app->session->setFlash('error', '复制失败');
                echo "<script>history.go(0);</script>";
                die;
            }
        }
        $insert_option = $guestModel->userFilter();//显示自带的结果
        $select_result = $guestModel->selectDbOperation($userId);//查找该用户的数据结果
        // var_dump($select_result);exit;
        return $this->render('copy', array(
            'insert_option' => $insert_option,
            'select_result' => $select_result,
        ));
    }

}
