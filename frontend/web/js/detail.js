/*弹出层页面加载start*/
/*页面加载*/
$(function(){
    var sumWidth =0;
    var sumHeight =0;
    $(".pop_order_dt").find("ul").each(function(){
        sumHeight += 1;
    });
    $(".pop_order_dt").find("li").each(function(){
        sumWidth += $(this).width();
    });
    var liWidth = sumWidth / sumHeight;
    $(this).css("width",liWidth);

//        if (liWidth > 535)
//            $("#inline1").css("width",630);
//        else if (liWidth < 430) {$("#inline1").css("width",630);
//        else $("#inline1").css("width",liWidth+63);
});
$(document).ready(function(){
//        $(".number-size").change(function () {
//            var getAll=$(".number-size").val();
//            if(getAll<=0){
//                $(".number-size").val("0");
//            }
//        });
    $(".number-size").change(function () {
        //var s=0;
        //var all=0;
        $("li").each(function () {
            var getNum = $(this);
            if(getNum.find('.number-size').val()<0){
                getNum.find('.number-size').val('0');
            }
        });
    });
    $(".is_down").attr("disabled",true);
    $(".is_down").change(function () {
        $(".is_down").each(function () {
            var getChange = $(this).val();
            if (getChange != 0){
                $(this).val("");
            }
        });
    });
    $(".sub").click(function(){
        var dt='';
        var product_id = '';
        var quantity = 0;
        $("li").each(function () {
            var obj = $(this);
            if (obj.find('.number-size').val()*1 > -1) {
                quantity = obj.find('.number-size').val();
                product_id = obj.find('#getInfoMaTion').val() * 1;
                dt += '|'+product_id+'_'+quantity;
            }
        });
        $.ajax({
            type:"post",
            url:"/order/getAllPrice",
            dataType:"json",
            data:{dt:dt},
            async: true,
            success:function (data) {
                if(data.code == '200'){
                    $(".fancybox-close").click();
                    $("#footer_order_money").text(data.data.cost_item);
                    $("#footer_order_number").text(data.data.nums);
                    $("#footer_order_percent").text(data.data.percent);
                    var js= window.location.pathname;
                    if(js=='/order/index2'){
                        window.location.reload();
                    }
                }else if(data.code == '400'){
                    alert(data.msg);
                }
            }
        });
    });
});
window.onbeforeunload = function () {
    var scrollPos;
    if (typeof window.pageYOffset != 'undefined') {
        scrollPos = window.pageYOffset;
    }
    else if (typeof document.compatMode != 'undefined' &&
        document.compatMode != 'BackCompat') {
        scrollPos = document.documentElement.scrollTop;
    }
    else if (typeof document.body != 'undefined') {
        scrollPos = document.body.scrollTop;
    }
    document.cookie = "scrollTop=" + scrollPos; //存储滚动条位置到cookies中
};
window.onload = function () {
    if (document.cookie.match(/scrollTop=([^;]+)(;|$)/) != null) {
        var arr = document.cookie.match(/scrollTop=([^;]+)(;|$)/); //cookies中不为空，则读取滚动条位置
        document.documentElement.scrollTop = parseInt(arr[1]);
        document.body.scrollTop = parseInt(arr[1]);
    }
}
/*弹出层页面加载end*/