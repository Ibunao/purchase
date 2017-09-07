<?php foreach ($list as $v):?>
<ul class="pd_list_mod lazyimg">
    <li class="pd_list_pic_area fl">
        <div class="pd_list_pic_info">
            <label class="icon_brought fr"></label>
            <span class="num_pd_list fl"><?= $v['serial_num'];?>.</span>
        </div>
        <a href="#inline_<?= $v['serial_num'];?>" class="fancybox_img">
            <img class="pd_list_pic" src="<?= Yii::$app->params['img_url'].$v['img_url']; ?>">
        </a>
        <div id="inline_<?= $v['serial_num'];?>" style="width:300px;display: none;">
            <img src="<?= Yii::$app->params['img_url'].$v['img_url']; ?>">
        </div>
    </li>
    <li class="pd_list_dt">
        <!--右侧按钮合计区域-->
        <?php if ($this->order_state == 'active'):?>
            <a href="#inline1" class="order_bt_modify fancybox" data="<?= $v['model_sn'];?>">修改</a>
        <!--
            <a href="#inline3" class="order_bt_del fancybox3" data="<?= '/order/delete?order_id='.$order_row['order_id'].'&model_sn='.$v['model_sn'];?>">删除</a>
            -->
        <?php endif;?>
        <?php if ($this->order_state == 'confirm'):?>
            <a href="javascript:;" class="order_bt_modify disabled">修改</a>
            <!--
            <a href="javascript:;" class="order_bt_del disabled">删除</a>
            -->
        <?php endif;?>
        <div class="order_dt_num_all">
            合计:<span class="order_dt_num word_dark_green"><?= isset($model_items[$v['model_sn']])?$model_items[$v['model_sn']]:0;?></span>件
        </div>
        <!--右侧按钮合计区域-->
        <ul class="order_dt_all">
            <li class="order_dt_info fl">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="pd_list_title_m">款号：</td>
                        <td class="pd_list_content_m"><?= $v['model_sn'];?></td>
                    </tr>
                    <tr>
                        <td class="pd_list_title_m">价格：</td>
                        <td class="pd_list_content_m"><?= $v['cost_price'];?></td>
                    </tr>
                    <tr>
                        <td class="pd_list_title_m">波段：</td>
                        <td class="pd_list_content_m"><?= $this->wave[$v['wave_id']];?></td>
                    </tr>
                    <tr>
                        <td class="pd_list_title_m">商品名称：</td>
                        <td class="pd_list_content_m"><?= $v['name'];?></td>
                    </tr>
                </table>
            </li>
            <li class="order_dt_flow fl">
                <!--溢出表格宽度-->
                <div class="pop_order_div" style="width: 642px;">
                    <ul class="dark">
                        <li></li>
                        <?php ksort($v['size_list']);foreach ($v['size_list'] as $vv):?>
                            <li><?= $vv['size_name'];?></li>
                        <?php endforeach;?>
                    </ul>
                    <input type="hidden" value="<?= $v['name'];?>" name="Product[Name]">
                    <?php foreach ($v['color_list'] as $vv):?>
                        <ul>
                            <li><?= $vv['color_name'];?></li>
                            <?php ksort($v['size_list']);foreach ($v['size_list'] as $vvv):?>
                                <li>
                                <?php if (isset($v['product_list'][$vvv['size_id'].'_'.$vv['color_id']])):?>
                                    <?= isset($v['product_list'][$vvv['size_id'].'_'.$vv['color_id']])&&isset($product_num[$v['product_list'][$vvv['size_id'].'_'.$vv['color_id']]['product_sn']])?
                                        $product_num[$v['product_list'][$vvv['size_id'].'_'.$vv['color_id']]['product_sn']]:0;?>
                                <?php endif;?>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    <?php endforeach;?>
                </div>
            </li>
        </ul>
    </li>
</ul>
<?php endforeach;?>
<?php if ($list):?>
<script>
    $(function(){
        $('.pop_order_div').each(function(){
            var sumWidth =0;
            var sumHeight =0;
            $(this).find("ul").each(function(){
                sumHeight += 1;
            });
            $(this).find("li").each(function(i,v){
                sumWidth += $(v).width();
            });
            var liWidth = sumWidth / sumHeight;
            $(this).css("width",liWidth);
//            if (liWidth > 535)
//                $(this).parent().css("width",431)
//            else $(this).parent().css("width",431);
        });
    });
</script>
<?php endif;?>