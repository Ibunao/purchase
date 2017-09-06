<?= $this->render('/common/_nav', ['model'=>$model,'c_id'=>$c_id,'b_id'=>$b_id]);?>
<!--右侧主区域-->
<div class="hd_rt_main">
    <?php if(isset($_GET['sd'])||isset($_GET['wv'])||isset($_GET['lv'])||isset($_GET['plv'])||isset($_GET['or'])){?>
    <div class="pd_brdcbs">
        <label>已选条件：</label>
        <?php if(isset($_GET['sd'])){
            echo "<span>".  mb_substr($_GET['sd'],2,10,"UTF-8")."</span><i></i>";
        }else{} ?>
        <?php if(isset($_GET['wv'])){
            echo "<span>".mb_substr($_GET['wv'],2,4,"UTF-8")."</span><i></i>";
        }else{} ?>
        <?php if(isset($_GET['lv'])){
            echo "<span>".  mb_substr($_GET['lv'],2,10,"UTF-8")."</span><i></i>";
        }else{} ?>
        <?php if(isset($_GET['plv'])){
           echo "<span>".  mb_substr($_GET['plv'],2,10,"UTF-8")."</span><i></i>";
        }else{} ?>
        <?php if(isset($_GET['or'])){if($_GET['or']==1){echo "<span>已订</span><i></i>";}else{ echo "<span>未订</span><i></i>";}}else{} ?>
    </div>
    <?php } ?>
    <?php if (!$model['list']):?>
        <div class="hd_rt_main pd_list">
            <!--搜索无结果-->
            <div class="no_results_all">
                <div class="no_results_content">
                    <div class="ico_no_results"></div>
                    <div class="words_no_results">您搜索的商品不存在！</div>
                </div>
            </div>
            <!--搜索无结果-->
        </div>
    <?php else:?>
    <!--商品list-->
    <div class="pd_list mb50 load-ul">
        <?php foreach ($model['list'] as $k=>$v):?>

        <ul class="pd_list_mod lazyimg">
            <li class="pd_list_pic_area fl">
                <div class="pd_list_pic_info">
                    <?php if ($v['is_order'] == 1):?>
                    <label class="icon_brought fr"></label>
                    <?php endif;?>
                    <span class="num_pd_list fl"><?= $v['serial_num'];?>.</span>
                </div>
                <a href="#inline_<?= $v['serial_num'];?>" class="fancybox_img">
                    <img class="pd_list_pic" src="<?php if($v['is_down']=='0'){  echo Yii::$app->params['img_url'].$v['img_url']; }else{
                        echo "/images/default.jpg";
                     }?>">
                </a>
                <div id="inline_<?= $v['serial_num'];?>" style="width:300px;display: none;">
                   <?php if($v['is_down']=='0'){ ?> <img src="<?= Yii::$app->params['img_url'].$v['img_url']; ?>"><?php }else{ ?>
                       <img src="/images/default.jpg">
                    <?php }?>
                </div>
            </li>
            <li class="pd_list_dt pd_type_<?= sprintf("%02d", $v['type_id']); ?>">
<?php $js=isset($res[0]['status'])?$res[0]['status']:0;  
//如果提交了按钮不显示
    if($js=='active'){ 
?>  
    <a href="<?php 
        //如果商品下架了
        if($v['is_down']==0){ 
    ?>#inline1
    <?php }else{?>
    ##inlineSS
    <?php } ?>" data="<?= $v['model_sn'];?>" class=" <?php if($v['is_down']==0){ ?> fancybox  bt_send_to_cart <?php }else{ ?> bt_send_false <?php } ?>"><label class="icon_send_to_cart"></label>预订</a>
<?php }?>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>

                        <td class="pd_list_std">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pd_list_title_s">款号:</td>
                                    <td><?= $v['model_sn'];?></td>
                                </tr>
                            </table>
                        </td>
                        <td class="pd_list_std">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pd_list_title_s">编号:</td>
                                    <td><?= $v['style_sn'];?></td>
                                </tr>
                            </table>
                        </td>
                        <td class="pd_list_std">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pd_list_title_s">价格:</td>
                                    <td><?= $v['cost_price'];?>元</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="pd_list_std">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pd_list_title_s">颜色:</td>
                                    <td><?= $this->context->color[$v['color_id']];?></td>
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
                                    <td><?= $this->context->wave[$v['wave_id']];?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pd_list_title_m">商品名称：</td>
                                    <td class="pd_list_content_m"><?= $v['name'];?></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tbody><tr>
                                    <td class="pd_list_title_s">款式:</td>
                                    <td><?= $this->context->level[$v['level_id']];?></td>
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
                                    <td class="pd_list_content_m"><?= $v['memo'];?></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pd_list_title_s">类型:</td>
                                    <td><?= $this->context->type[$v['type_id']]; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </li>
        </ul>
        <?php endforeach;?>
        <div class="load-div">
            <!--滚动加载-->
            <p class="y_loading_easy" id="J_loadmore"><i class="dot"></i>努力加载中...</p>
            <input type="hidden" value="<?= $this->context->urlParams(array('next'=>1, 'serial_num' => $serial_num));?>" id="url">
            <!--滚动加载-->
        </div>
    </div>
<!--商品list-->
    <?php endif;?>
</div>
<?= $this->render('/common/_footer',[]);?>