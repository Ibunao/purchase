<link rel="stylesheet" href="/css/chosen.css" />
<link rel="stylesheet" href="/css/datepicker.css" />
<link rel="stylesheet" href="/css/bootstrap-timepicker.css" />
<link rel="stylesheet" href="/css/daterangepicker.css" />
<link rel="stylesheet" href="/css/colorpicker.css" />
<script type="text/javascript" src="/js/layer/layer.min.js"></script>
<script type="text/javascript" src="/js/jquery.PrintArea.js"></script>
<style>
    .form-group{margin-bottom: 5px;}
    .billContainer2 {
        border: 1px solid #cfcfcf;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        margin-bottom: 5px;
        height: auto;
        padding: 10px 20px 10px 20px;
    }
</style>
<div class="col-xs-12">
<div class="col-xs-12" style=" border: 1px solid #cfcfcf;  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);margin-bottom: 5px;padding: 10px 20px 10px 20px;" >
    <form action="" method="get">
        <input type="hidden" name="r" value="order/default/index">
        <input type="hidden"  name="param[download]" value="0">
        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right">订货会: </label>
            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[purchase]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['purchase'] as $k=>$v){?>
                        <option value="<?= $v['purchase_id'];?>" <?php if(isset( $params['purchase']) && $params['purchase']==$v['purchase_id']){?>selected <?php  } ?>><?= $v['purchase_name']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">客户类型: </label>
            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[type]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['customer'] as $k=>$v){?>
                        <option value="<?= $v['type']?>" <?php if (!empty($params['type']) && $params['type']==$v['type']){ ?>selected <?php } ?> ><?= $v['type'];?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right"> 季节: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[season]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['season'] as $k=>$v){?>
                        <option value="<?= $v['season_id']?>" <?php if (isset($params['season']) && $params['season']==$v['season_id']){ ?>selected <?php } ?> >
                            <?= $v['season_name'];?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right"> 大类: </label>
            <div class="col-sm-8 no-padding-left" >

                <select class="form-control" name="param[cat_big]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['cat_big'] as $k=>$v){?>
                        <option value="<?= $v['big_id']?>" <?php if (isset($params['cat_big']) && $params['cat_big']==$v['big_id']){ ?>selected <?php } ?> ><?= $v['cat_name'];?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right"> 中类: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[cat_middle]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['cat_middle'] as $k=>$v){?>
                        <option value="<?= $v['middle_id']?>" <?php if (isset($params['cat_middle']) && $params['cat_middle']==$v['middle_id']){ ?>selected <?php } ?> ><?= $v['cat_name'];?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right"> 小类: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[cat_small]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['cat_small'] as $k=>$v){?>
                        <option value="<?= $v['small_id']?>" <?php if (isset($params['cat_small']) && $params['cat_small']==$v['small_id']){ ?>selected <?php } ?> ><?= $v['cat_name'];?></option>
                    <?php } ?>
                </select>
            </div>
        </div>



        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right"> 波段: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[wave]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['wave'] as $k=>$v){?>
                        <option value="<?= $v['wave_id']?>" <?php if (isset($params['wave']) && $params['wave']==$v['wave_id']){ ?>selected <?php } ?> >
                            <?= $v['wave_name'];?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right"> 等级: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[level]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['level'] as $k=>$v){?>
                        <option value="<?= $v['level_id']?>" <?php if (isset($params['level']) && $params['level']==$v['level_id']){ ?>selected <?php } ?> >
                            <?= $v['level_name'];?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right"> 色系: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[scheme]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['scheme'] as $k=>$v){?>
                        <option value="<?= $v['scheme_id']?>" <?php if (isset($params['scheme']) && $params['scheme']==$v['scheme_id']){ ?>selected <?php } ?> >
                            <?= $v['scheme_name'];?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right"> 商品类型: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[ptype]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['ptype'] as $k=>$v){?>
                        <option value="<?= $v['type_id']?>" <?php if (isset($params['ptype']) && $params['ptype']==$v['type_id']){ ?>selected <?php } ?> >
                            <?= $v['type_name'];?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-3 control-label no-padding-right"> 价格带: </label>
            <div class="col-sm-8 no-padding-left" >
                <select class="form-control" name="param[price_level_id]">
                    <option value="">请选择</option>
                    <?php foreach($selectOption['price_level'] as $k=>$v){?>
                        <option value="<?= $k;?>" <?php if (isset($params['price_level_id']) && $params['price_level_id']==$k){ ?>selected <?php } ?> >
                            <?= $v;?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>


        <div class="col-sm-4 form-group" style="height: 32px">
            <label class="col-sm-4 control-label no-padding-right">款色号: </label>
            <div class="col-sm-8 no-padding-left" >
                <input type="text" class="col-xs-10"  name="param[style_sn]" value="<?= isset($params['style_sn'])?$params['style_sn']:'';?>">
            </div>
        </div>


        <input type="hidden"  name="param[order]" value="<?= isset($params['order'])?$params['order']:'p.style_sn';?>">
        <input type="hidden"  name="param[view]" value="<?php  if(isset( $params['view'])){ echo $params['view'];  } ?>">
        <input type="hidden"  name="page" value="<?= isset($params['page'])?$params['page']:'1';?>">

    </form>

    <div class="col-sm-4 form-group" style="height: 32px">
        <label class="col-sm-3 control-label no-padding-right"> </label>
        <div class="col-sm-8 no-padding-left" >
            <button   class="btn btn-sm-8 btn-primary gosubmit" data-val="2">
                统计
            </button>
            <?php unset($params['page']);if(!empty($params)){?>
            <button   class="btn btn-sm btn-primary download" data-val="1">
                下载
            </button>
            <?php }?>
        </div>
    </div>

</div>
</div>

<div class="col-xs-12">
    <div class="table-header col-xs-12">

        <lable class="col-sm-3">排序：
            <button   class="btn btn-sm <?php if(isset($params['order'])){ echo  ($params['order']=='p.serial_num asc')?'btn-info':'btn-primary';}else{ echo 'btn-info'; } ?>  order" data-val="p.serial_num asc">
                款色
            </button>
            <button   class="btn btn-sm <?php if(isset($params['order'])){ echo  ($params['order']=='nums desc')?'btn-info':'btn-primary'; }else{ echo 'btn-primary'; }?>  order" data-val="nums desc">
                数量
            </button>
            <button   class="btn btn-sm <?php if(isset($params['order'])){ echo  ($params['order']=='cost_price desc')?'btn-info':'btn-primary';}else{ echo 'btn-primary'; } ?>  order" data-val="cost_price desc">
                价格
            </button>
            <button id="print" class="print btn btn-sm">打印</button>
        </lable>

        <lable class="col-sm-3">展示：
            <button   class="btn btn-sm  <?php if(isset($params['view'])){echo  ($params['view']=='0' || $params['view']=='' )?'btn-info':'btn-primary';}else{ echo 'btn-info'; } ?>  gosubmit" data-val="0" >
                列表查看
            </button>
            <button   class="btn btn-sm  <?php if(isset($params['view'])){echo  ($params['view']=='1')?'btn-info':'btn-primary';}else{ echo 'btn-primary'; } ?>  gosubmit" data-val="1">
                图片查看
            </button>
        </lable>
        <!--S 汇总-->
        <label class="col-sm-3">订货数量汇总： <b><?= isset($result['nums'])?$result['nums']:'0';?></b></label>
        订货金额汇总: <b><?= isset($result['amount'])?number_format($result['amount'],2):'0.00';?></b>

        <!--E 汇总-->
    </div>
</div>
<script >
    $().ready(function(){
        $('.gosubmit').click(function(){
            var view_val = $(this).attr('data-val');
            if(view_val == '2'){
                //等于2清空page 当前页码
                $("input[name='page']").val(1);
            }else{
                //设置 显示方式
                $("input[name='param[view]']").val(view_val);
            }

            $("form").eq(0).submit();
        });

        $('.order').click(function(){
            var order_val = $(this).attr('data-val');
            $("input[name='param[order]']").val(order_val);
            $(this).siblings().removeClass('btn-info').addClass('btn-primary');
            $(this).addClass('btn-info');

            $("form").eq(0).submit();
        });


        $('.download').click(function(){
            var val = $(this).attr('data-val');
            $("input[name='param[download]']").val(val);
            $("form").eq(0).submit();
            $("input[name='param[download]']").val(0);
        });
        $(function($){
            $(".print").click(function(){
                $("#print_area").printArea( {
                    mode:'popup'
                });
            });
        });
    });
</script>