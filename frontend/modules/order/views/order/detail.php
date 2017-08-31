<?php

$this->params['breadcrumbs'] = [
    ['label' => '订单'],
    ['label' => '订单详细汇总'],
    ['label' => '订单明细'],
];

?>



<div class="row">
    <div class="col-xs-12">
        <div class="table-header">
            <!--S 汇总-->
            <label class="col-sm-3"><?php echo $order_info['purchase_name']?></label>
            <label class="col-sm-3">客户名:<?php echo $order_info['customer_name'];?></label>
            <label class="col-sm-3">客户代码:<?php echo $order_info['customer_id'];?> </label>
            <input type="button" class="btn btn-sm btn-primary Dialogue" onclick="window.location.href='admin.php?r=order/order/detail&order_id=<?php echo $order_info['order_id'];?>'"   value="订单明细">
            <input type="button" class="btn btn-sm btn-danger Dialogue" onclick="window.location.href='admin.php?r=order/order/statistics&order_id=<?php echo $order_info['order_id'];?>'"   value="订单统计">
            <input type="button" class="btn btn-sm btn-danger print"  value="打印">
            <!--E 汇总-->
        </div>


        <?php foreach($result as $k=> $v){  ?>
        <div class="table-responsive ">
            <div role="grid "  class=" col-xs-12" style=" float: left" >
                <table class="table table-striped table-bordered table-hover dataTable" style="width: 400px;float: left">
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                    <tr class="odd">
                        <td colspan="3"> </td>
                    </tr>
                    <tr class="odd">
                        <td rowspan="5" width="120px"><img src = '<?php echo $v['img_url']?>' height="100px"/></td>
                        <td width="100px">款色号： </td>
                        <td width="180px"><?php echo $v['model_sn'];?> </td>
                    </tr>
                    <tr class="odd">
                        <td> 价格：</td>
                        <td> <?php echo $v['cost_price'];?>  </td>
                    </tr>
                    <tr class="odd">
                        <td>波段： </td>
                        <td> <?php echo $v['wave_name'];?> </td>
                    </tr>
                    <tr class="odd">
                        <td> 商品名称：</td>
                        <td><?php echo $v['name'];?>  </td>
                    </tr>
                    <tr class="odd">
                        <td> &nbsp;</td>
                        <td> </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table table-striped table-bordered table-hover dataTable" style="width: auto;border-left: 0px">
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                    <tr class="odd">
                        <td colspan="<?php echo count($v['size_name'])+2?>"> </td>
                    </tr>
                    <tr>
                        <td width="100px">  </td>
                        <?php foreach ($v['size_name'] as $sv){?>
                            <td width="40px"><?php echo $sv; ?></td>
                        <?php } ?>
                        <td>合计 </td>
                    </tr>
                    <?php  //狗屎需求引出的狗屎代码！！！！！
                    $total =0;
                    foreach($v['norm'] as $nk => $nv){?>
                    <tr>
                        <?php $amount = 0;?>

                        <td> <?php  echo $nk;?> </td>
                        <?php foreach ($v['size_name'] as $sv){?>
                            <td class="<?php echo ($nv[$sv]>0)?'red':'';?>"> <?php
                                echo $nv[$sv];
                                $amount+= $nv[$sv];
                                ?> </td>
                        <?php } ?>
                        <td class="<?php echo ($amount>0)?'red':'';?>" > <?php echo $amount;
                            $total+=$amount;
                            ?> </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td > </td>
                        <?php foreach ($v['size_name'] as $sv){?>
                            <td > </td>
                        <?php } ?>
                        <td class="<?php echo ($total>0)?'red':'';?>" ><?php echo $total;?> </td>
                    </tr>
                </tbody>
            </table>
            </div>
            </div>
            <?php } ?>

            <div class="table-responsive">
                <div role="grid" class="dataTables_wrapper  col-xs-12" id="sample-table-2_wrapper">
                    <table class="table table-striped table-bordered table-hover dataTable"  >
                        <tbody role="alert" aria-live="polite" aria-relevant="all">
                        <tr class="odd">
                            <td colspan="4">
                                <span  class="btn-group">
                                <a  class="btn btn-sm btn-info"  href="javascript:history.go(-1)" >返回</a>
                                </span>
                            </td>

                            <td>订货指标：  <span class="red"><?php echo ($order_info['target']==0)?'':number_format($order_info['target'],2);?></span> </td>
                            <td>已订货：   <span class="red"><?php echo number_format($count, 2);?> </span>  </td>
                            <td>达成率： <span class="red"><?php echo ($order_info['target']==0)?'':number_format($count/$order_info['target']*100,2)."%";?></span> </td>
                            <td>订货总数： <span class="red"><?php echo  $order_info['nums'] ;?></span> </td>
                            <td>
                                <span  class="btn-group col-sm-3" style=" display: <?php if($order_info['status'] =='finish' ){ echo 'block';}else{ echo 'none';}?>" >
                                <input type="button" class="btn btn-sm btn-danger checkOrder" data-status="confirm" data-val="<?php echo $order_info['order_id'];?>"  value="取消审核">
                                </span>
                                <span  class="btn-group col-sm-3" style=" display: <?php if($order_info['status'] =='confirm'){ echo 'block';}else{ echo 'none';}?>" >
                                <input type="button" class="btn btn-sm btn-warning checkOrder"  data-status="finish" data-val="<?php echo $order_info['order_id'];?>" value="订单审核">
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="row" id="print_area" style="display: none">
    <div class="col-xs-12">

<!--        class="table table-striped table-bordered table-hover dataTable"-->
        <style>
            td{border:1px solid #000000; text-align: center;font-size: 14px }
        </style>
        <div class="table-responsive ">
            <div role="grid "  class=" col-xs-12" style=" float: left" >


                <table  style="width: 1000px;border:1px solid #000000;" cellspacing="0" cellpadding="0">
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                    <tr>
                        <td>客户名称</td>
                        <td colspan="21"  style="text-align: left"><?php echo $order_info['customer_name'];?></td>
                    </tr>
                    <tr >
                        <td rowspan="2">款号:</td>
                        <td rowspan="2">商品名称:</td>
                        <td rowspan="2">颜色:</td>

                    <?php

                    $size1 =  array('S',    'M',    'L',    'XL',   'XXL',  'F');
                    $size2 =  array('75B',  '75C',  '75D',  '75E',  '80B',  '80C',  '80D',  '80E',  '85B',  '85C',  '85D',  '85E',  '90B',  '90C',  '90D' , '90E');

                    foreach($size1 as $s){?>
                        <td><?php echo $s;?></td>
                    <?php } ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td rowspan="2">数量 </td>
                        <td rowspan="2">标准价 </td>
                        <td rowspan="2">金额 </td>
                    </tr>
                    <tr>
                        <?php foreach($size2 as $s){?>
                        <td><?php echo $s;?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    $all=0;
                    $all_amount = 0;
                    foreach($orderlist as $i){

                    ?>
                        <tr class="odd">
                            <td><?php echo $i['model_sn'];?></td>
                            <td><?php echo $i['name'];?></td>
                            <td><?php echo $i['color_name'];?></td>
                            <?php
                            $data=array();
                            $flag = '';
                            $sum = 0;
                            foreach($i[$i['color_name']]['size_name'] as $s_n){

                                if(in_array($s_n['size_name'],$size1)){

                                    $flag = 'size1';
                                    foreach($size1 as $s){
                                        if($s == $s_n['size_name']){

                                           $data[$s]=$s_n['nums'];
                                        }
                                    }
                                }

                                if(in_array($s_n['size_name'],$size2)){
                                    $flag = 'size2';
                                    foreach($size2 as $s){
                                        if($s == $s_n['size_name']){
                                            $data[$s]=$s_n['nums'];
                                        }
                                    }
                                }

                            }   ?>
                            <?php

                            if($flag=='size1'){
                                foreach($size1 as $s){
                                    if(array_key_exists($s,$data)){
                                        $sum+=$data[$s];
                                        echo "<td>".$data[$s]."</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                }
                                for($space=0; $space<(count($size2)-count($size1)) ;$space++){
                                    echo "<td>  </td>";
                                }
                            }
                            if($flag=='size2'){
                                foreach($size2 as $s){
                                    if(array_key_exists($s,$data)){
                                        $sum+=$data[$s];
                                        echo "<td>".$data[$s]."</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                }

                            }
                            ?>
                            <td><?php echo $sum;?></td>
                            <td><?php echo $i['price']?></td>
                            <td><?php echo number_format($i['price']*$sum,2);?></td>
                        </tr>
                    <?php   } ?>
                    <tr>
                        <td colspan="3" style="text-align: center">合计</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?php echo  $order_info['nums'] ;?></td>
                        <td></td>
                        <td><?php echo number_format($count,2);?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>



<script>
    $(function($){
        $(".checkOrder").click(function(){
            if(confirm('确认订单审核通过吗？')){
                var btn = $(this);

                var order_id = $(this).attr('data-val');
                var status = $(this).attr('data-status');
                $.post('admin.php?r=order/order/check',{'order_id':order_id,'status':status},function(data){
                    if(data.code!==400){
                        alert('操作成功');
                        btn.parent().hide();
                        btn.parent().siblings().show();
                    }else{
                        alert('操作失败');
                    }

                },'json');
            }

        });


        $(".print").click(function(){
            $("#print_area").printArea( {
                    mode:'popup'
                });
        });
    });

</script>
