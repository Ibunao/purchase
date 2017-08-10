<div class="showD"  style="display: none">
    <table class="table table-striped table-bordered table-hover dataTable"  style="width: 800px;height: 440px">
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <tr class="odd">
            <td rowspan="6" colspan="2" id="image">图片</td>
            <td colspan="2">大类
            </td>
            <td colspan="15" id="cat_big"> </td>
        </tr>
        <tr class="odd">
            <td colspan="2">小类
            </td>
            <td colspan="15" id="cat_small"> </td>
        </tr>
        <tr class="odd">
            <td colspan="2"> 季节
            </td>
            <td colspan="15" id="season"> </td>
        </tr>
        <tr class="odd">
            <td colspan="2">款色号
            </td>
            <td colspan="15" id="style_sn"> </td>
        </tr>
        <tr class="odd">
            <td colspan="2">吊牌价
            </td>
            <td colspan="15" id="cost_price"> </td>
        </tr>
        <tr class="odd">
            <td colspan="2">商品名称
            </td>
            <td colspan="15" id="product_name"> </td>
        </tr>
        <tr class="odd type_name">
            <td>订单类别</td>
            <td>颜色 <span  id="scheme"></span>  </td>
            <!--                <td>S </td>-->
            <!--                <td>M</td>-->
            <!--                <td>L</td>-->
            <!--                <td>XL </td>-->
            <!--                <td>合计</td>-->
        </tr>
        <tr class="odd customer">
            <td>加盟订单 </td>
            <td> </td>
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
        </tr>
        <tr class="odd self">
            <td>直营订单 </td>
            <td> </td>
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
        </tr>
        <tr class="odd all">
            <td>总订单 </td>
            <td> </td>
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
            <!--                <td> </td>-->
        </tr>
        </tbody>
    </table>
</div>

<script>
    $(function($){
        $(".Dialogue").click(function(){
            var style_sn = $(this).attr('data-val');
            var html_demo = $(".showD").html();
            $.get('admin.php?r=order/default/dialogue&style_sn='+style_sn,function(data){
                if(data.code!==400){
                    var data = data.data;
                    $("#cat_big").html(data.big_name);
                    $("#cat_small").html(data.small_name);
                    $("#season").html(data.season_name);
                    $("#style_sn").html(data.style_sn);
                    $("#cost_price").html(data.cost_price);
                    $("#product_name").html(data.name);
                    $("#scheme").html(data.scheme_name + data.color_name);
                    $("#image").html('<img src="'+data.img_url+'" height="230px"/>');

                    var td_class_red = "class='red'";
                    var customer = self= all = 0;
                    $.each(data.order_count,function(k,v){
                        //var s= k+2;
                        //$(".type_name").children('td').eq(s).html(v.size)  ;
                        $(".type_name").last().append('<td>'+v.size+'</td>')  ;
                        if(v.customer>0){
                            $(".customer").last().append('<td '+td_class_red+'>'+v.customer+'</td>')  ;
                        }else{
                            $(".customer").last().append('<td>'+v.customer+'</td>')  ;
                        }

                        if(v.self>0){
                            $(".self").last().append('<td '+td_class_red+'>'+v.self+'</td>')  ;
                        }else{
                            $(".self").last().append('<td>'+v.self+'</td>')  ;
                        }


                        //竖列合计
                        if((parseFloat(v.self)+parseFloat(v.customer))>0 ){
                            $(".all").last().append('<td '+td_class_red+'>'+(parseFloat(v.self)+parseFloat(v.customer))+'</td>');
                        }else{
                            $(".all").last().append('<td>'+(parseFloat(v.self)+parseFloat(v.customer))+'</td>');
                        }


                        //横列合计
                        customer += parseInt(v.customer);
                        self  +=parseInt(v.self);
                        all += (parseFloat(v.self)+parseFloat(v.customer));


                    });

                    $(".type_name").last().append('<td>合计</td>')  ;
                    if(customer>0){
                        $(".customer").last().append('<td '+td_class_red+'>'+ customer+'</td>')  ;
                    }else{
                        $(".customer").last().append('<td>'+ customer+'</td>')  ;
                    }
                    if(self>0){
                        $(".self").last().append('<td '+td_class_red+'>'+ self+'</td>')  ;
                    }else{
                        $(".self").last().append('<td>'+ self+'</td>')  ;
                    }
                    if(all>0){
                        $(".all").last().append('<td '+td_class_red+'>'+ all+'</td>')  ;
                    }else{
                        $(".all").last().append('<td>'+ all+'</td>')  ;
                    }


                    var html = $(".showD").html();


                    $(".showD").html(html_demo);

                    $.layer({
                        type: 1,   //0-4的选择,
                        title: false,
                        border: [0],
                        closeBtn: [0],
                        shadeClose: true,
                        area: ['800px', '440px'],
                        page: {
                            html: html
                        }
                    });
                }else{
                    alert('出错了');
                }



            },'json');

        });


    })





</script>
