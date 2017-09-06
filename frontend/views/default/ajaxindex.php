<?php foreach ($model['list'] as $k=>$v):?>
<ul class="pd_list_mod lazyimg">
    <li class="pd_list_pic_area fl">
        <div class="pd_list_pic_info">
            <?php if ($v['is_order'] == 1):?>
                <label class="icon_brought fr"></label>
            <?php endif;?>
            <span class="num_pd_list fl"><?php echo $v['serial_num'];?>.</span>
        </div>
        <a href="#inline_<?php echo $v['serial_num'];?>" class="fancybox_img">
            <img class="pd_list_pic" src="<?php if($v['is_down']=='0'){  echo Yii::$app->params['img_url'].$v['img_url']; }else{
                        echo "/images/default.jpg";
                     }?>">
        </a>
        <div id="inline_<?php echo $v['serial_num'];?>" style="width:300px;display: none;">
            <?php if($v['is_down']=='0'){ ?> <img src="<?php echo Yii::$app->params['img_url'].$v['img_url']; ?>"><?php }else{ ?>
                <img src="/images/default.jpg">
            <?php }?>
        </div>
    </li>
    <li class="pd_list_dt pd_type_<?php echo sprintf("%02d", $v['type_id']); ?>">
        <?php $js=isset($res[0]['status'])?$res[0]['status']:0;  if($js=='active'){ ?>  <a href="#inline1" data="<?php echo $v['model_sn'];?>" class=" <?php if($v['is_down']==0){ ?> fancybox  bt_send_to_cart <?php }else{ ?> bt_send_false <?php } ?>"><label class="icon_send_to_cart"></label>预订</a><?php }?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pd_list_std">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_s">款号:</td>
                            <td><?php echo $v['model_sn'];?></td>
                        </tr>
                    </table>
                </td>
                <td class="pd_list_std">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_s">编号:</td>
                            <td><?php echo $v['style_sn'];?></td>
                        </tr>
                    </table>
                </td>
                <td class="pd_list_std">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_s">价格:</td>
                            <td><?php echo $v['cost_price'];?>元</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="pd_list_std">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_s">颜色:</td>
                            <td><?php echo $this->context->color[$v['color_id']];?></td>
                        </tr>
                    </table>
                </td>
                <td class="pd_list_std">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_s">尺码:</td>
                            <td><?php krsort($v['size']); echo implode(',',array_slice(array_reverse($v['size']),0,3));?><?php if (count($v['size']) > 3):?>......<?php endif;?></td>
                        </tr>
                    </table>
                </td>
                <td class="pd_list_std">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_s">波段:</td>
                            <td><?php echo $this->context->wave[$v['wave_id']];?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_m">商品名称：</td>
                            <td class="pd_list_content_m"><?php echo $v['name'];?></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tbody><tr>
                            <td class="pd_list_title_s">款式:</td>
                            <td><?php echo $this->context->level[$v['level_id']];?></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_m">商品描述：</td>
                            <td class="pd_list_content_m"><?php echo $v['memo'];?></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pd_list_title_s">类型:</td>
                            <td><?php echo $this->context->type[$v['type_id']]; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </li>
</ul>
<?php endforeach;?>

<?php if ($model['list']):?>
<div class="load-div">
    <!--滚动加载-->
    <p class="y_loading_easy" id="J_loadmore"><i class="dot"></i>努力加载中...</p>
    <input type="hidden" value="<?php echo $this->context->urlParams(array('next'=>1));?>" id="url">
    <!--滚动加载-->
</div>
<?php endif;?>