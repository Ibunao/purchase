<div class="popup_order_info mb10">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="pd_list_title_m">款       号:</td>
            <td><?php echo $list['model_sn'];?></td>
            <td class="pd_list_title_m">价       格:</td>
            <td><?php echo $list['cost_price'];?>元</td>
        </tr>
        <tr>
            <td class="pd_list_title_m">商品名称:</td>
            <td><?php echo $this->context->globalSubstr($list['name'],8);?></td>
            <td class="pd_list_title_m">波       段:</td>
            <td><?php echo $this->context->wave[$list['wave_id']];?></td>
        </tr>
    </table>
</div>
<div class="pop_order_dt">
        <div class="pop_order_div">
            <ul class="dark">
                <li></li>
                <?php ksort($list['size_list']);foreach ($list['size_list'] as $v):?>
                <li><?php echo $v['size_name'];?></li>
                <?php endforeach;?>
            </ul>
            <input type="hidden" value="<?php echo $list['name'];?>" name="Product[Name]">
            <?php foreach ($list['color_list'] as $v):?>
            <ul>
                <li><?php echo $v['color_name'];?></li>
                <?php ksort($list['size_list']); foreach ($list['size_list'] as $vv):?>
                <li>
                   <?php if (isset( $list['product_list'][$vv['size_id'].'_'.$v['color_id']])):?>
                   <input type="number"  placeholder="0" class="number-size <?php $js=isset($list['product_list'][$vv['size_id'].'_'.$v['color_id']]['is_down'])?$list['product_list'][$vv['size_id'].'_'.$v['color_id']]['is_down']:1;  if($js==1){ ?> is_down <?php } ?>" value="<?php echo isset($order_items[$list['product_list'][$vv['size_id'].'_'.$v['color_id']]['product_id']])?$order_items[$list['product_list'][$vv['size_id'].'_'.$v['color_id']]['product_id']]['nums']:'';?>" onKeyUp="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" name="Product[Num][]">
                   <input type="hidden" placeholder="0" value="<?php echo $list['product_list'][$vv['size_id'].'_'.$v['color_id']]['product_id'];?>" name="Product[Id][]" id="getInfoMaTion">
                   <?php endif;?>
                </li>
                <?php endforeach;?>
            </ul>
            <?php endforeach;?>
        </div>
        <!--溢出表格宽度-->
</div>

<div class="pop_order_num_all">
    合计:<span class="pop_order_num word_dark_green"><?php echo $total;?></span>件
</div>

<div class="pop_order_bt_area  sub">
    <a href="javascript:;" class="bt_send_to_cart"><label class="icon_send_to_cart"></label>预订</a>
</div>
<script type="text/javascript" src="/js/detail.js"></script>