<style>
    .check-result p{
        font-size: 14px;
    }
    .check-result b{
        padding: 0 5px;
        font-size: 16px;;
        color: #ff8482;
    }
    .table{
        width: 600px;
    }
    table span{
        font-size: 16px;
        color: #ff8482;
    }
    .customer-list b{
        color: #ff8482;
        padding: 0 5px;
    }
</style>
<div class="check-result">
    <p>检测结果如下</p>

        <?php if(empty($data)){ ?>
            <p>数据检查完成，没有问题，请再次确认数据，没有问题就上传吧</p>
            <p class='customer-list'>请再次确认上传的客户资料：
            <?php echo "客户名称：<b>".$c_info['name']."</b>客户代码：<b>".$c_info['code']."</b>订货会：<b>";
            echo ($c_info['purchase_id']==1)?'OCT':'UKI';
            echo "</b>手机号码：<b>".$c_info['mobile']."</b>类型：<b>".$c_info['type']."</b>省份：<b>".$c_info['province']."</b></p>" ;?>
            <table class="table">
            <?php
                $count_len = count($product_list);
                for($i=1; $i<$count_len; $i++){
                    echo "<tr>";
                    echo "<td>{$i}</span></td>";
                    echo "<td>款号：<span>{$product_list[$i][0]}</span></td>";
                    echo "<td>色号：<span>{$product_list[$i][1]}</span></td>";
                    echo "<td>尺码：<span>{$product_list[$i][2]}</span></td>";
                    echo "<td>数量：<span>{$product_list[$i][3]}</span></td>";
                    echo "</tr>";
                }
            ?>
            </table>
            <form action="/order/order/uploading-csv-data" method="post">
                <input type="hidden" value="<?php echo $customer; ?>" name="param[customer_id]" id="customer">
                <input type="hidden" value="<?php echo $file; ?>" name="param[file]" id="file">
                <button class="btn btn-sm btn-danger" type="submit">上传</button>
            </form>
            <script>
                $(document).ready(function () {
                    var customer =  $("#customer").val();
                    var file = $("#file").val();
                    if(file =="" || customer == ""){
                        $(".btn").hide();
                        return false;
                    }
                });
            </script>
        <?php }else {
            echo "<p>" . $data . "</p>";
            echo "<p>请按上面的错误说明修改后再次上传 <a href='/order/order/import'>点我返回</a></p>";
            } ?>
</div>
