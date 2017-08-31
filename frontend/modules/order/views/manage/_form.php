<?php 
use yii\helpers\Url;
 ?>
<link rel="stylesheet" href="/css/chosen.css" />
<script src="/js/chosen.jquery.min.js" ></script>
<style>
    body{
        font-family: "Microsoft YaHei";
    }
    .form-group{
        margin:0;
        padding: 9px;
    }
    .small-width{
        width: 100px;
        height: 33px;
    }
    .inc{
        width: 400px;
        height: 33px;
    }
    .sec{
        width: 300px;
        height: 33px;
        
    }
    label{
        width: 80px;
        height: 33px;
        text-align: right;
        padding-top: 5px;
        font-size: 16px;
    }
    .bt{
        background-color: #f5f5f5;
        width: 100%;
        height: 100px;
    }
    .bt button{
        margin-top: 20px;
    }
    .bt button:first-child{
        margin-left: 150px;
    }
    #form_field_select_3_color_chosen a.chosen-single{
        height: 38px;
        padding-top: 6px;
        -webkit-border-radius: 0px !important;
        -moz-border-radius: 0px !important;
        border-radius: 0px !important;
        border:1px solid #CCCCCC !important;
    }
    #form_field_select_3_color_chosen a.chosen-single div b{
        padding-top:7px;
    }
    a.chosen-single.chosen-default{
        height: 30px;
        background: none;
        padding-top: 3px;
        -webkit-border-radius: 0px !important;
        -moz-border-radius: 0px !important;
        border-radius: 0px !important;
        border:1px solid #CCCCCC !important;
    }
    .chosen-container-single a.chosen-single{
        height: 30px;
        background: none;
        padding-top: 3px;
        -webkit-border-radius: 0px !important;
        -moz-border-radius: 0px !important;
        border-radius: 0px !important;
        border:1px solid #CCCCCC !important;
    }
    .chosen-container{
        width: 300px !important;
    }
</style>
<h3><b><?php
            if($action == 'add'){
                echo "用户添加";
            }elseif($action == 'update'){
                echo "用户修改";
            }elseif($action == 'copy'){
                echo "用户复制";
            }
        ?></b></h3>
<form action="<?=Url::to(['/order/manage/'.$action]) ;?>" method="post">
    <input type="hidden" <?php if($action == 'update' || $action == "copy"){ ?> name="param[id]" value="<?= isset($param['customer_id'])?$param['customer_id']:''; ?>" <?php } ?>>

    <div class="form-group">
        <label for="name">订货会</label>
        <select name="param[purchase_id]" class="sec dhh" id="purchase">
            <option value="">请选择</option>
            <?php  foreach($insert_option['purchase'] as $k=>$pid){ ?>
                <option value="<?= $k; ?>"  <?php if($action !="copy" ){ if(isset($param['purchase_id'])&&$param['purchase_id']==$k){echo "selected";}} ?> ><?php  echo $pid;  ?></option>
            <?php } ?>
        </select><b style="color: red">*</b>
    </div>

    <div class="form-group">
        <label for="name">客户名称</label>
        <input type="text" name="param[name]" <?php if($action !="copy" ){ ?>  value="<?= isset($param['name'])?$param['name']:''; ?>" <?php } ?> class="inc" id="name"><b style="color: red">*</b>
    </div>

    <div class="form-group">
        <label for="lastname" class="control-label">客户代码</label>
        <input type="text" name="param[code]" class="inc" id="code" <?php if($action !="copy" ){ ?> value="<?= isset($param['code'])?$param['code']:''; ?>" <?php } ?>  onkeyup="this.value=this.value.replace(/^ +| +$/g,'')"><b id="userinfo"></b><b style="color: red">*</b>
    </div>

    <div class="form-group">
        <label for="name">客户手机</label>
        <input type="text" name="param[mobile]" onkeyup='this.value=this.value.replace(/\D/gi,"")' <?php if($action !="copy" ){ ?>  value="<?= isset($param['mobile'])?$param['mobile']:''; ?>"  <?php } ?> id="mobile" class="inc khsj" maxlength="11"><b id="mobileinfo"></b><b style="color: red">*</b>
    </div>

    <div class="form-group">
        <label for="name">客户类型</label>
        <select name="param[type]" class="sec khlx">
            <option value="">请选择</option>
            <?php  foreach($insert_option['type'] as $k=>$type){ ?>
                <option value="<?= $k; ?>" <?php if(isset($param['type'])&&$k==$param['type']){echo "selected";} ?>><?= $type ?></option>
            <?php } ?>
        </select><b style="color: red">*</b>
    </div>

    <div class="form-group">
        <label for="lastname" class="control-label">客户关系代码</label>
        <input type="text" name="param[relation_code]" class="inc" id="relation_code" value="<?= isset($param['relation_code'])?$param['relation_code']:''; ?>" onkeyup="this.value=this.value.replace(/^ +| +$/g,'')"><b>当此关系代码相同的用户，表示一位用户的多个订货会账号</b>
    </div>



    <div class="form-group">
        <label for="name">部门类别</label>
        <select name="param[department]" class="sec">
        <option value="">请选择</option>
        <?php  foreach($insert_option['department'] as $k=>$department){ ?>
            <option value="<?= $department; ?>" <?php if(isset($param['department'])&&$param['department']==$department){echo "selected";} ?>><?=  $department; ?></option>
        <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">大区</label>
        <select name="param[area]" class="sec dq">
            <option value="">请选择</option>
            <?php  foreach($insert_option['area'] as $k=>$area){ ?>
                <option value="<?= $area; ?>" <?php if(isset($param['area'])&&$param['area']==$area){echo "selected";} ?>><?php  echo $area;  ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">省份</label>
        <select name="param[province]" class="sec sf">
            <option value="">请选择</option>
            <?php  foreach($insert_option['province'] as $k=>$province){ ?>
                <option value="<?= $province; ?>" <?php if(isset($param['province'])&&$province==$param['province']){echo "selected";} ?>><?= $province; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">负责人</label>
        <select name="param[leader]" class="sec">
            <option value="">请选择</option>
            <?php  foreach($insert_option['leader'] as $k=>$leader){ ?>
                <option value="<?= $leader; ?>"<?php if(isset($param['leader'])&&$param['leader']==$leader){echo "selected";} ?>><?= $leader; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label class="form-field-select-3">代理名称</label>
        <select class="width-12 chosen-select" name="param[leader_name]"   data-placeholder="选择代理名称" id="form-field-select-3 pm">
            <option value="">请选择</option>
            <?php  foreach($insert_option['leader_name'] as $k=>$leader_name){ ?>
                <option value="<?= $leader_name['agent_code']; ?>" <?php if(isset($param['leader_name'])&&$param['leader_name']==$leader_name['agent_name']){echo "selected";} ?>><?php  echo $leader_name['agent_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">初始密码</label>
        <input type="text" name="param[password]" <?php if($action !="copy" ){ ?>  placeholder="<?= isset($param['password'])?'不修改请勿填写哦！':''; ?>" <?php } ?> class="inc csmm"><?php if($action != "update"){ ?>初始密码不填写默认手机号码后四位<?php } ?>
    </div>

    <div class="form-group">
        <label for="name">订货总指标</label>
        <input type="text" name="param[target]" id="target" value="<?= isset($param['target'])?$param['target']:''; ?>" class="inc dhzb" onkeyup="this.value=this.value.replace(/\D/g,'')">
    </div>

    <p class="text-danger">下面为百分比,<b class="text-info">最大</b>仅限精确到小数点2位</p>

    <div class="form-group">
        <label for="name">服装指标</label>
        <input type="text" name="param[big_1]" id="big_1" maxlength="8" value="<?= isset($param['big_1'])?$param['big_1']:""; ?>" class="small-width">%
        <label for="name">服装折扣</label>
        <input type="text" name="param[big_1_count]" id="big_1_count" maxlength="8" value="<?= isset($param['big_1_count'])?$param['big_1_count']:""; ?>" class="small-width">%
    </div>

    <div class="form-group">
        <label for="name">家居指标</label>
        <input type="text" name="param[big_2]" id="big_2" maxlength="8" value="<?= isset($param['big_2'])?$param['big_2']:""; ?>" class="small-width">%
        <label for="name">家居折扣</label>
        <input type="text" name="param[big_2_count]" id="big_2_count" maxlength="8" value="<?= isset($param['big_2_count'])?$param['big_2_count']:""; ?>" class="small-width">%
    </div>

    <div class="form-group">
        <label for="name">防辐射指标</label>
        <input type="text" name="param[big_3]" id="big_3" maxlength="8" value="<?= isset($param['big_3'])?$param['big_3']:""; ?>" class="small-width">%
        <label for="name">防辐射折扣</label>
        <input type="text" name="param[big_3_count]" id="big_3_count" maxlength="8" value="<?= isset($param['big_3_count'])?$param['big_3_count']:""; ?>" class="small-width">%
    </div>

    <div class="form-group">
        <label for="name">备品指标</label>
        <input type="text" name="param[big_4]" id="big_4" maxlength="8" value="<?= isset($param['big_4'])?$param['big_4']:""; ?>" class="small-width">%
        <label for="name">备品折扣</label>
        <input type="text" name="param[big_4_count]" id="big_4_count" maxlength="8" value="<?= isset($param['big_4_count'])?$param['big_4_count']:""; ?>" class="small-width">%
    </div>

    <div class="form-group">
        <label for="name">化妆品指标</label>
        <input type="text" name="param[big_6]" id="big_6" maxlength="8" value="<?= isset($param['big_6'])?$param['big_6']:""; ?>" class="small-width">%
        <label for="name">化妆品折扣</label>
        <input type="text" name="param[big_6_count]" id="big_6_count" maxlength="8" value="<?= isset($param['big_6_count'])?$param['big_6_count']:""; ?>" class="small-width">%
    </div>

    <div class="bt col-xs-12">
        <button class="btn btn-primary col-md-offset-3 btn-lg">提交</button>
        <button type="reset" class="btn col-md-offset-3 btn-defult btn-lg">重置</button>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $(".chosen-select").chosen();
        $("#code").change(function(){
            var code=$("#code").val();
            $.ajax({
                type:"post",
                url:"/order/manage/ajax?codes="+code,
                dataType: "json",
                success:function(msg){
                    $("#userinfo").html(msg);
                }
            });
        });
    });
    $(function(){
        $("form").submit(function(){
            var name = $("#name").val();
            if(!$.trim(name)){
                alert('客户名称不能为空');
                return false;
            }
            var code = $("#code").val();
            if(!$.trim(code)){
                alert('客户代码不能为空');
                return false;
            }
            var purchaseid = $(".dhh").val();
            if(!$.trim(purchaseid)){
                alert('订货会不能为空,请选择');
                return false;
            }
            var mobile = $(".khsj").val();
            if(!$.trim(mobile)){
                alert('客户手机不能为空');
                return false;
            }
            if(mobile.length != 11){
                alert('客户手机不是11位');
                return false;
            }
            var khlx = $(".khlx").val();
            if(!$.trim(khlx)){
                alert('客户类型不能为空,请选择');
                return false;
            }
            var big_1 = $("#big_1").val();
            var big_2 = $("#big_2").val();
            var big_3 = $("#big_3").val();
            var big_4 = $("#big_4").val();
            var big_6 = $("#big_6").val();
            var target = $("#target").val();
            var big_1_discount = $("#big_1_count").val();
            var big_2_discount = $("#big_2_count").val();
            var big_3_discount = $("#big_3_count").val();
            var big_4_discount = $("#big_4_count").val();
            var big_6_discount = $("#big_6_count").val();
            if( target == "" && (big_1 != "" || big_2 !="" || big_3 !="" || big_4 !="" || big_6 !="")) {
                alert("总指标为空而大类指标不为空");
                return false;
            }
            if( target != ""){
                if(big_1*1 + big_2*1 + big_3*1 + big_4*1 + big_6*1 != 0){
                    if(big_1*1 >100 || big_2*1 >100 || big_3*1 > 100 || big_4*1 > 100 ||big_6*1 > 100) {
                        alert("单个指标大于100%，请重试");
                        return false;
                    }
                    if(( big_1 *1 + big_2*1 + big_3*1 + big_4*1 + big_6*1 ) != 100 ) {
                        alert("大类的指标总和不等于100%，请重试");
                        return false;
                    }
                }
            }
            if(big_1_discount !='' || big_2_discount != '' || big_3_discount != '' || big_4_discount !='' || big_6_discount !='' ){
                if(big_1_discount && (big_1_discount*1 >100 || big_1_discount*1 <=0)){
                    alert("服装折扣率只能在0-100之间,且折扣不能为0");
                    return false;
                }
                if(big_2_discount && (big_2_discount*1  >100 || big_2_discount*1 <=0)){
                    alert("家居折扣率只能在0-100之间,且折扣不能为0");
                    return false;
                }
                if(big_3_discount && (big_3_discount*1  >100 || big_3_discount*1 <=0)){
                    alert("防辐射折扣率只能在0-100之间,且折扣不能为0");
                    return false;
                }
                if(big_4_discount && (big_4_discount*1  >100 || big_4_discount*1 <=0)){
                    alert("备品折扣率只能在0-100之间,且折扣不能为0");
                    return false;
                }
                if(big_6_discount && (big_6_discount*1  >100 || big_6_discount*1 <=0)){
                    alert("化妆品折扣率只能在0-100之间,且折扣不能为0");
                    return false;
                }
            }
        });
    });
</script>