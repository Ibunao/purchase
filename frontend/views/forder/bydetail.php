<!--主导航-->
<div id="nav_shade" class="none"></div>
<?php echo $this->render('/layouts/_nav', array('sel' => 'bydetail')); ?>
<!--主导航-->
<!--右侧主区域-->
<div class="order_rt_main">
    <!--商品list-->
    <div class="pd_list mb50">
        <?php foreach ($list as $v):?>
        <ul class="pd_list_mod lazyimg">
            <li class="pd_list_pic_area fl">
                <div class="pd_list_pic_info">
                    <label class="icon_brought fr"></label>
                    <span class="num_pd_list fl"><?php echo $v['serial_num'];?>.</span>
                </div>
                <a href="#inline_<?php echo $v['serial_num'];?>" class="fancybox_img">
                    <img class="pd_list_pic" src="<?php echo Yii::$app->params['img_url'].$v['img_url']; ?>">
                </a>
                <div id="inline_<?php echo $v['serial_num'];?>" style="width:300px;display: none;">
                    <img src="<?php echo Yii::$app->params['img_url'].$v['img_url']; ?>">
                </div>
            </li>
            <li class="pd_list_dt">
                <!--右侧按钮合计区域-->
                <?php if ($this->context->orderState == 'active'):?>
                    <a href="#inline1" class="order_bt_modify fancybox" data="<?php echo $v['model_sn'];?>">修改</a>
                <?php endif;?>

                <?php if ($this->context->orderState == 'confirm'):?>
                    <a href="javascript:;" class="order_bt_modify disabled">修改</a>
                    <!--
                    <a href="javascript:;" class="order_bt_del disabled">删除</a>
                    -->
                <?php endif;?>

                <div class="order_dt_num_all">
                    合计:<span class="order_dt_num word_dark_green"><?php echo isset($model_items[$v['model_sn']])?$model_items[$v['model_sn']]:0;?></span>件
                </div>
                <!--右侧按钮合计区域-->
                <ul class="order_dt_all">
                    <li class="order_dt_info fl">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pd_list_title_m">款号：</td>
                                <td class="pd_list_content_m"><?php echo $v['model_sn'];?></td>
                            </tr>
                            <tr>
                                <td class="pd_list_title_m">价格：</td>
                                <td class="pd_list_content_m"><?php echo $v['cost_price'];?></td>
                            </tr>
                            <tr>
                                <td class="pd_list_title_m">波段：</td>
                                <td class="pd_list_content_m"><?php echo $this->context->wave[$v['wave_id']];?></td>
                            </tr>
                            <tr>
                                <td class="pd_list_title_m">商品名称：</td>
                                <td class="pd_list_content_m"><?php echo $v['name'];?></td>
                            </tr>
                        </table>
                    </li>
                    <li class="order_dt_flow fl">
                        <!--溢出表格宽度-->
                        <div class="pop_order_div" style="width: 250px;">
                            <ul class="dark">
                                <li></li>
                                <?php ksort($v['size_list']);foreach ($v['size_list'] as $vv):?>
                                    <li><?php echo $vv['size_name'];?></li>
                                <?php endforeach;?>
                            </ul>
                            <input type="hidden" value="<?php echo $v['name'];?>" name="Product[Name]">
                            <?php foreach ($v['color_list'] as $vv):?>
                                <ul>
                                    <li><?php echo $vv['color_name'];?></li>
                                    <?php ksort($v['size_list']);foreach ($v['size_list'] as $vvv):?>
                                        <li>
                                        <?php if (isset($v['product_list'][$vvv['size_id'].'_'.$vv['color_id']])):?>
                                            <?php echo isset($v['product_list'][$vvv['size_id'].'_'.$vv['color_id']])&&isset($product_num[$v['product_list'][$vvv['size_id'].'_'.$vv['color_id']]['product_sn']])?
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

        <div class="load-div">
            <!--滚动加载-->
            <p class="y_loading_easy" id="J_loadmore"><i class="dot"></i>努力加载中...</p>
            <input type="hidden" value="<?php echo $next;?>" id="page">
            <!--滚动加载-->
        </div>
        <!--滚动加载-->
        <!--滚动加载-->
    </div>
<!--商品list-->
</div>
<?php echo $this->render('/common/_footer_order',array());?>
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
        /*滚动加载*/
        var stop=true;//防止重复加载
        $(window).scroll(function(){
            var page = $('#page').val() * 1;
            totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
            if($(document).height() <= totalheight + 120){
                if(stop==true){
                    stop=false;
                    $('#J_loadmore').addClass('enabled');
                    $.get("/forder/detail", {'page':page},function(txt){
                        if (txt) {
                            $(".load-div").append(txt);
                            $('#page').val(page + 1);

                        }
                        stop=true;
                        $('#J_loadmore').removeClass('enabled');
                    },"text");
                }
            }
        });
        /*滚动加载*/
    });
</script>