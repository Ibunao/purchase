<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

?>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/chosen.css" />
<script src="<?= Yii::$app->request->baseUrl; ?>/js/chosen.jquery.min.js" ></script>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/datepicker.css" />
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/bootstrap-timepicker.css" />
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/daterangepicker.css" />
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/colorpicker.css" />

<style>
    body{
        font-family: "Microsoft YaHei";
    }
    .chosen-container{
        width: 280px !important;background-color: #fff !important;
    }
    .form-group{
        margin:0;
        padding: 5px;
    }
    .inc{
        width: 380px;
        height: 33px;;
    }
    .sec{
        width: 280px;
        height: 33px;
    }
    .md{
        width: 180px;
        height: 33px;
    }
    .xs{
        width: 100px;
        height: 33px;
    }
    .cbox{
        color:#00be67;
        width: 15px;
        height: 15px;
    }
    label{
        width: 80px;
        height: 33px;
        text-align: right;
        padding-top: 5px;
        font-size: 16px;
        color:#000000;
    }
    .py{
        margin-left: 84px
    }
    .margin-l{
        margin-left: 43px
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
    #color{
        width: 179px;height:auto;background-color: #fff;position: absolute;top:381px;left: 299px
    }
    #color ul,li{
        list-style: none;margin: 0;padding: 0;
    }
    #color ul li{
        padding: 4px;
    }
    #color ul li:hover{
        cursor: pointer;background-color: #00be67;
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
    }#form_field_select_3_color_chosen a.chosen-single{
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
    #cmhiden{
        margin-bottom: 20px;padding-bottom: 20px;
    }
    #error{
        color:red
    }
    a,a:hover{
        color:#fff
    }
    #size {
        margin-bottom: 5px;
        margin-top: -30px;
    }
    #size .checkbox{
        float: left;
    }
    #size .checkbox:first-child{
        margin-top: -5px;
        margin-bottom: 0px;
    }
    .hidden{
        display: none;
    }
    #memo {
        margin-top: -28px;
    }
    .red{
        color:red
    }
</style>

<h3><b><?php
        if ( $action == 'add' ) {
            echo "商品添加";
        } elseif ( $action == 'copy' ) {
            echo "商品复制";
        }elseif( $action == 'update' ) {
            echo "商品修改";
        }elseif( $action == 'change' ) {
            echo "商品按款号添加";
        }
        ?></b></h3>
<form action="" method="POST">
    <input type="hidden" name="r" value="order/product/<?= $action; ?>">
    <?php if($action == 'change'||$action=='copy') { echo "<p class='text-success'>款号，色系，色号为基础数据，基础数据不可修改，请认真填写，如果除基础数据外有填写错误，请到任意该款号的商品下修改正确即可</p>"; } ?>
<?php if($action == 'update') {
    echo "<p class='text-danger'>款号，色系，色号为基础数据,基础数据不可修改，请认真填写</p>";
} ?>
    <div class="form-group">
        <label for="name">订货会</label>
        <select name="param[purchase]" class="sec"  id="purchase" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php foreach($selectFilter['purchase'] as $v){ ?>
                <option value="<?= $v['purchase_id'] ?>" <?php if( isset($param['purchase_id']) && $param['purchase_id'] == $v['purchase_id'] ) { echo "selected"; } ?>><?= $v['purchase_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">品牌</label>
        <select name="param[brand]" class="sec"  id="brand" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php foreach($selectFilter['brand'] as $v){ ?>
                <option value="<?= $v['brand_id'] ?>" <?php if( isset($param['brand_id']) && $param['brand_id'] == $v['brand_id'] ) { echo "selected"; } ?>><?= $v['brand_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">品名</label>
        <input type="text" name="param[name]" id="name" class="inc" value="<?= !empty($param['name'])?$param['name']:''; ?>" maxlength="22">(品名不多于22个字)
    </div>

    <div class="form-group">
        <label for="name">款号</label>
        <input type="text" name="param[modelSn]"  <?php if($action == 'update'||$action=='change'||$action=='copy'){echo "readonly";} ?> id="modelSn" class="md" value="<?= !empty($param['model_sn'])?$param['model_sn']:''; ?>" maxlength="8" onKeyUp="this.value=this.value.replace(/[^\.\d]/g,'');if(this.value.split('.').length>2){this.value=this.value.split('.')[0]+'.'+this.value.split('.')[1]}")">（<b>先填我！</b> 请输入8位款号）
    </div>
    
    <div class="form-group">
        <label for="name">吊牌价</label>
        <input type="text" name="param[costPrice]" class="md" id="costPrice" <?php if($action=='change'||$action=='copy'){echo "readonly";} ?> value="<?= !empty($param['cost_price'])?$param['cost_price']:''; ?>" onkeyup="value=value.replace(/[^\d.]/g,'')">
    </div>

    <div class="form-group">
        <label for="name">色系</label>
        <select name="param[scheme]" class="sec" <?php if( $action == "update" ){ echo "disabled"; } ?> id="scheme">
            <option value="">请选择</option>
            <?php foreach($selectFilter['scheme'] as $v){ ?>
                <option value="<?= $v['scheme_id'] ?>" <?php if($action == 'update') { ?> <?php if( isset($param['scheme_id']) && $param['scheme_id'] == $v['scheme_id'] ) { echo "selected"; } ?> <?php } ?>><?= $v['scheme_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <?php if($action == 'update') { ?>
        <input type="hidden" id="hcolor" name="param[color_id]" value="<?= $param['color_id']; ?>">
        <input type="hidden" id="hscheme" name="param[scheme_id]" value="<?= $param['scheme_id']; ?>">
    <?php } ?>

    <div class="form-group">
        <label class="form-field-select-1">色号</label>
        <select name="param[color]" class="width-10 chosen-select" <?php if( $action == "update" ){ echo "disabled"; } ?> data-placeholder="选择色号" id="form-field-select-3">
            <option value="">请选择</option>
            <?php foreach($selectFilter['color'] as $v){ ?>
                <option value="<?= $v['color_id'] ?>" <?php if( isset($param['color_id']) && $param['color_id']==$v['color_id'] ) { echo "selected"; } ?>><?= $v['color_name']; ?></option>
            <?php }  ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">尺码组</label>
        <select name="param[sizeGroup]" class="sec" id="sizeGroup" <?php if($action == 'update'||$action=='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php
            if(!empty($selectFilter['sizeGroup'])){
            foreach($selectFilter['sizeGroup'] as $v){ ?>
                <option value="<?= $v['group_id'] ?>" <?php if( isset($param['sizeGroup']) &&  $param['sizeGroup']== $v['group_id']){ echo "selected"; } ?>><?= $v['size_group_name']; ?></option>
            <?php } } ?>
        </select>
    </div>

    <div class="form-group col-xs-12">
        <label for="name">尺码</label>
        <div class="col-xs-12">
            <div id="size" class="col-xs-4 margin-l">
                <?php
                    if($action == "update"||$action == 'change'||$action == 'copy') {
                        foreach($selectFilter['size'] as $v){
                ?>
                        <div class="checkbox">
                            <label>
                                <input name="param[size][]" value="<?= $v['size_id'] ?>" type="checkbox" class="ace" <?php if( isset($param['size_id']) && isset($param['size'])){ foreach($param['size'] as $val){ if($val ==$v['size_id']){ echo "checked"; } } }?>>
                                <span class="lbl"><?= $v['size_name']; ?></span>
                            </label>
                        </div>
                <?php } } ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="name">大类</label>
        <select name="param[catBig]" class="sec" id="catBig" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php foreach($selectFilter['catBig'] as $v){ ?>
                <option value="<?= $v['big_id'] ?>" <?php if( isset($param['cat_b']) && $param['cat_b'] == $v['big_id'] ) { echo "selected"; } ?>><?= $v['cat_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">中类</label>
        <select name="param[catMiddle]" class="sec" id="catMiddle" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php  if(!empty($selectFilter['catMiddle'])){
            foreach($selectFilter['catMiddle'] as $v){ ?>
                <option value="<?= $v['middle_id'] ?>" <?php if( isset($param['cat_m']) && $param['cat_m'] == $v['middle_id'] ) { echo "selected"; } ?>><?= $v['cat_name']; ?></option>
            <?php } } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">小类</label>
        <select name="param[catSmall]" class="sec" id="catSmall" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php  if(!empty($selectFilter['catSmall'])){
                foreach($selectFilter['catSmall'] as $v){ ?>
                    <option value="<?= $v['small_id'] ?>" <?php if( isset($param['cat_s']) && $param['cat_s'] == $v['small_id'] ) { echo "selected"; } ?>><?= $v['cat_name']; ?></option>
                <?php } } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">季节</label>
        <select name="param[season]" class="sec" id="season" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php if(!empty($selectFilter['season'])) {
                foreach($selectFilter['season'] as $v){ ?>
                <option value="<?= $v['season_id']; ?>" <?php if( isset($param['season_id']) && $param['season_id'] == $v['season_id'] ) { echo "selected"; } ?>><?= $v['season_name']; ?></option>
            <?php }} ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">等级</label>
        <select name="param[level]" class="sec" id="level" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php foreach($selectFilter['level'] as $v){ ?>
                <option value="<?= $v['level_id']; ?>" <?php if( isset($param['level_id']) && $param['level_id'] == $v['level_id']) { echo "selected"; } ?>><?= $v['level_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group col-xs-12">
        <label for="name">波段</label>
        <select name="param[wave]" class="sec" id="wave" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <option value="">请选择</option>
            <?php foreach($selectFilter['wave'] as $v){ ?>
                <option value="<?= $v['wave_id']; ?>" <?php if( isset($param['wave_id']) && $param['wave_id'] == $v['wave_id'] ) { echo "selected"; } ?>><?= $v['wave_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group col-xs-12">
        <label for="name">商品类型</label>
        <select name="param[type]" class="sec" id="type" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
            <?php foreach($selectFilter['type'] as $v){ ?>
                <option value="<?= $v['type_id']; ?>" <?php if( isset($param['type_id']) && $param['type_id'] == $v['type_id'] ) { echo "selected"; } ?>><?= $v['type_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">图片</label>
        <input type="text" class="inc" id="banner_url" name="param[image]" value="<?= !empty($param['img_url'])?$param['img_url']:''; ?>" placeholder="图片URL">
    </div>

    <button type="button" style="margin-left:90px" class="btn btn-purple btn-sm btn-more-small" id="banner_upload_regular">
        <i class="icon-cloud-upload"></i>上传
    </button>

    <div class="form-group col-xs-12" <?php if($action =='change'||$action=='copy'){echo "disabled";} ?>>
        <label for="name">描述</label>
        <textarea class="form-control py inc" rows="3" id="memo" name="param[memo]"><?= !empty($param['memo'])?$param['memo']:''; ?></textarea>
    </div>

    <div class="form-group">
        <label for="name">状态</label>
        <select name="param[status]" class="sec">
            <option value="0" <?= isset($param['is_down'])&&$param['is_down']==0?'selected':''; ?>>上架</option>
            <option value="1" <?= isset($param['is_down'])&&$param['is_down']==1?'selected':''; ?>>下架</option>
        </select>
    </div>

    <div class="bt col-xs-12">
        <button class="btn btn-primary  col-md-offset-3 btn-lg sub" type="submit">提交</button>
        <button class="btn col-md-offset-3 btn-defult btn-lg reset">重置</button>
    </div>

</form>
<script>
    $(document).ready(function(){
        $(".chosen-select").chosen();
        //重置按钮
        $(".reset").click(function () {
           go.history(0);
        });

        //提交检查提交数据是否为空
        $('.sub').click(function(){

            var purchase = $("#purchase").val();
            if( purchase == '') {
                alertMsg("请选择订货会");
                return false;
            }

            var brand = $("#brand").val();
            if( brand == '') {
                alertMsg("请选择品牌");
                return false;
            }

            var name = $("#name").val();
            if( name == '' ) {
                alertMsg("请输入品名");
                return false;
            }

            var modelSn = $("#modelSn").val();
            if( modelSn == '') {
                alertMsg("请输入款号");
                return false;
            }else{
                if( modelSn.length < 7 && modelSn.length>=1 ) {
                    alertMsg("款号不低于7位数！！！");
                    return false;
                }
            }

            var costPrice = $("#costPrice").val();
            if( costPrice == '') {
                alertMsg("请输入吊牌价");
                return false;
            }

            var scheme = $("#scheme").val();
            if( scheme == '') {
                alertMsg("请选择色系");
                return false;
            }

            var color = $("#form-field-select-3").val();
            if( color == '' ) {
                alertMsg("请选择色号");
                return false;
            }

            var sizeGroup = $("#sizeGroup").val();
            if( sizeGroup == '') {
                alertMsg("请选择尺码组");
                return false;
            }

            var sizeNum=0;
            $('input[type = "checkbox"]:checked').each(function() {
                var sizeCheckedNum = $(this).val();
                if(sizeCheckedNum != '') {
                    sizeNum = sizeNum + 1;
                }
            });

            if( sizeNum == 0 ) {
                alertMsg('<?php if( $action == "update" ) { echo "如果你不想让这个款号出现，请刷新本页后选择：下架此商品"; } else{ echo "请选择尺码"; } ?>');
                return false;
            }

            var catBig = $("#catBig").val();
            if( catBig == '' ) {
                alertMsg("请选择大类");
                return false;
            }

            var catMiddle = $("#catMiddle").val();
            if( catMiddle == '' ) {
                alertMsg("请选择中类");
                return false;
            }

            var catSmall = $("#catSmall").val();
            if( catSmall == '' ) {
                alertMsg("请选择小类");
                return false;
            }

            var season = $("#season").val();
            if(season == '') {
                alertMsg("请选择季节");
                return false;
            }

            var level=$("#level").val();
            if(level==''){
                alertMsg("请选择等级");
                return false;
            }

            var wave = $("#wave").val();
            if( wave == '' ) {
                alertMsg("请选择波段");
                return false;
            }

            var memo=$("#memo").val();
            if(memo==''){
                alertMsg("请输入商品描述");
                return false;
            }

            var status = $("#status").val();
            if( status =='' ) {
                alertMsg("请选择商品状态");
                return false;
            }
            <?php if( $action == 'update' ) { ?>
                var color_id = $("#hcolor").val();
                if(color_id==''){
                    alertMsg('出错');
                }
                var scheme_id = $("#hscheme").val();
                if( scheme_id == "") {
                    alertMsg('出错');
                }
            <?php } ?>
        });

        /**判断是否有存在的款号**/
        $("#modelSn").change(function(){
           var model_sn = $(this).val();
            console.log(model_sn);
            $.ajax({
                type: "get",
                url: "/admin.php?r=order/product/AjaxCheckModelSnExist&modelSn="+model_sn,
                dataType: "json",
                success:function (data) {
                    if(data.code == 200){
                        $("#modelSn").val("");
                        location.href = "admin.php?r=order/product/change&modelSn="+data.data;
                    }
                }
            });
        });

        /*检查此款号是否存在此色号*/
        $("#form-field-select-3").change(function () {
            var model_sn = $("#modelSn").val();
            var scheme   = $("#scheme").val();
            var color    = $("#form-field-select-3").val();
            $.ajax({
                type: "get",
                url: "/admin.php?r=order/product/AjaxCheckSchemeColorExist&scheme="+scheme+"&color="+color,
                dataType: "json",
                success:function (data) {
                    if(data.code == 400){
                        alert(data.msg);
                        $("#scheme option").removeAttr("selected");
                        $("#form-field-select-3 option").removeAttr("selected");
                        $(".chosen-single span").text("请选择");
                        return false;
                    }
                }
            });
            $.ajax({
                type: "get",
                url: "/admin.php?r=order/product/AjaxCheckModelSnAndColor&model_sn="+model_sn+"&color="+color,
                dataType: "json",
                success:function (data) {
                    if(data.code == 200){
                        $(this).removeAttr("selected");
                        location.href = "admin.php?r=order/product/update&serial_num="+data.data;
                    }
                }
            });
        });

        /**根据尺码组获取尺码**/
        $("#sizeGroup").change(function(){
            var sizeGroup = $("#sizeGroup  option:selected").val();
            if(sizeGroup == "") {
                return false;
            }
            $.ajax({
                type: "get",
                url: "/admin.php?r=order/product/ajaxSizeGroupGetSize&sizeGroup="+sizeGroup,
                dataType: "json",
                success:function (data) {
                    if(data.code == 200){
                        $("#size").empty();
                        $.each(data.data, function (i, data) {
                            $("#size").append('<div class="checkbox"><label><input name="param[size][]" value="'+ data.size_id +'" type="checkbox" class="ace" ><span class="lbl">'+data.size_name+'</span> </label> </div>');
                        });
                    }else if(data.code == 400) {
                        alertMsg(data.msg);
                    }
                }
            });
        });

        /**根据大类获取中类**/
        $("#catBig").change(function(){
            var cat=$("#catBig option:selected").val();
            if(cat==""){
                return false;
            }
            $.ajax({
                type: "get",
                url: "/admin.php?r=order/product/AjaxCatMiddle&catBig="+cat,
                dataType: "json",
                data: cat,
                success:function (data) {
                    if(data.code == 200){
                        $("#catMiddle").empty().append("<option value=''>请选择</option>");
                        $("#catSmall").empty().append("<option value=''>请选择</option>");
                        $("#season").empty().append("<option value=''>请选择</option>");
                        $.each(data.data.small, function (i, data) {
                            $("#catSmall").append("<option value='" + data.small_id + "'>" + data.cat_name + "</option>");
                        });
                        $.each(data.data.middle, function (i, data) {
                            $("#catMiddle").append("<option value='" + data.middle_id + "'>" + data.cat_name + "</option>");
                        });
                        $.each(data.data.season, function (i, data) {
                            $("#season").append("<option value='" + data.season_id + "'>" + data.season_name + "</option>");
                        });
                    }
                }
            });
//            $.ajax({
//                type: "get",
//                url: "/admin.php?r=order/product/AjaxCatSeason&catBig="+cat,
//                dataType: "json",
//                data: cat,
//                success:function (data) {
//                    if(data.code == 200){
//                        $("#season").empty().append("<option value=''>请选择</option>");
//                        $.each(data.data, function (i, data) {
//                            $("#season").append("<option value='" + data.season_id + "'>" + data.season_name + "</option>");
//                        });
//                    }else if(data.code == 400){
//                        alertMsg(data.msg);
//                    }
//                }
//            });
        });

//        /**根据中类获取小类**/
//        $("#catMiddle").change(function(){
//            var cat=$("#catMiddle option:selected").val();
//            if(cat==""){
//                return false;
//            }
//            $.ajax({
//                type: "get",
//                url: "/admin.php?r=order/product/AjaxCatSmall&catSmall="+cat,
//                dataType: "json",
//                data: cat,
//                success:function (data) {
//                    if(data.code == 200){
//                        $("#catSmall").empty().append("<option value=''>请选择</option>");
//                        $.each(data.data, function (i, data) {
//                            $("#catSmall").append("<option value='" + data.small_id + "'>" + data.cat_name + "</option>");
//                        });
//                    }else if(data.code == 400){
//                        layer.msg(data.msg);
//                    }
//                }
//            });
//        });
    });
</script>