//关闭ajax缓存
$.ajaxSetup ({
    cache: false //关闭AJAX相应的缓存
});

function numberformat(domInput) {

    $(domInput).css("ime-mode", "disabled");
    $(domInput).bind("keypress", function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);  //兼容火狐 IE
        if (!$.browser.msie && (e.keyCode == 0x8))  //火狐下 不能使用退格键
        {
            return;
        }
        return code >= 48 && code <= 57 || code == 46 || code == 13;
    });
    $(domInput).bind("blur", function () {
        if (this.value.lastIndexOf(".") == (this.value.length - 1)) {
            this.value = this.value.substr(0, this.value.length - 1);
        } else if (isNaN(this.value)) {
            this.value = " ";
        }
    });
    $(domInput).bind("paste", function () {
        var s = clipboardData.getData('text');
        if (!/\D/.test(s));
        value = s.replace(/^0*/, '');
        return false;
    });
    $(domInput).bind("dragenter", function () {
        return false;
    });
    $(domInput).bind("keyup", function () {
        this.value = this.value.replace(/[^\d.]/g, "");
    });
}
/*订单*/
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
    $(".pop_order_div").css("width",liWidth);

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
                if(data==1){
                    $(".fancybox-close").click();
                    var js= window.location.pathname;
                    if(js=='/order/index2'){
                        window.location.reload();
                    }
                }else{
                    alert('修改失败');
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


/*搜索*/
$('#keyWords').on('keydown',function(e){
    numberformat(this);
});

$('.kw-search').on('click',function(){
    $('.form-search').submit();
});

/*滚动加载*/
var stop=true;//防止重复加载
$(window).scroll(function(){
    var totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
    var url = $('#url').val();
    if (!url) return false;
    if($(document).height() <= totalheight+120){
        if(stop==true){
            stop=false;
            $('#J_loadmore').addClass('enabled');
            $.post(
                url,
                {},
                function(txt){
                    $('#J_loadmore').removeClass('enabled');
                    $('.load-div').remove();
                    $(".load-ul").append(txt);
                    stop=true;
            },"text");
        }
    }
});
/*滚动加载*/

/**********************左侧菜单**********************/
$('li.level0 a').click(function(e) {
    var href = $(this).attr('href');
    var dropDown = $(this).parent().next();
    $('.dropdown').not(dropDown).slideUp('200');
    dropDown.slideDown();
    e.preventDefault();
    $('li.level0 a').removeClass('curSelected');
    $(this).addClass('curSelected');
    $('.dropdown a').removeClass('curSelectedNode');
    location.href=href;
});
$('.dropdown a').click(function(){
    $('.dropdown a').removeClass('curSelectedNode');
    $(this).addClass('curSelectedNode');
});
$('.mm_header a').click(function(){
    $('.dropdown').slideUp('200');
    $('li.level0 a').removeClass('curSelected');
    $('.dropdown a').removeClass('curSelectedNode');
});
/*mm_header全部商品，level0一级，dropdown二级*/
/*左侧菜单*/

/*********************弹出层********************/
$('body').on('click','.fancybox',function(){
    var model_sn = $(this).attr('data');
    $('.fancybox').fancybox();
    $.post(
        '/default/detail',
        {'model_sn':model_sn},
        function(result) {
            $('.popup_order_all').html(result);
        }
    );
});


//预定弹出层
$('.fancybox3').on('click',function(){
    var url = $(this).attr('data');
    $('#delete_item').val(url);
});

//预定商品数量
$('#inline1').on('keyup','.number-size',function(){
    var num_sum = model_num();
    $('.pop_order_num').text(num_sum);
    if ($(this).val() * 1 < 1) {
        $(this).val('');
        return false;
    }
});

function model_num()
{
    var num_sum = 0;
    $('.number-size').each(function(){
        num_sum += $(this).val() * 1;
    });
    return num_sum;
}
//提交预定
$('#inline1').on('click','.bt_send_to_cart',function(){
    $('.pop_form').submit();
//    $.ajax({
//        cache: false,
//        type: "POST",
//        url:'/order/create',
//        data:$('.pop_form').serialize(),// 你的formid
//        async: false,
//        dataType:'json',
//        error: function(request) {
//            location.href='/default/index';
//        },
//        success: function(result) {
//            if (result.code == 200) {
//                $.fancybox.close('#inline1');
//                $('.total_num').html(result.data.total_num);
//                $('.amount').html(result.data.amount);
//            }
//        }
//    });
});

//确定删除商品
$('#bt_login').on('click',function(){
    location.href=$(this).next().val();
});
$('.fancybox3').fancybox();

//确定提交订单
$('#bt_order').on('click',function(){
    location.href='/order/submit';
});
$('.fancybox4').fancybox();

//撤销订单
$('#bt_order2').on('click',function(){
    location.href='/order/repeal';
});

$('.repeal').click(function(){
    $.post(
        '/order/repealcheck',
        {},
        function (result) {
            if (result == 200) {
                $.fancybox.open('#inline5');
            } else {
                $.fancybox.open('#inline7');

            }
        }
    );
});

$('#bt_order3').on('click',function(){
    location.href='/order/repeal';
});

$('.fancybox_img').fancybox();

/*同页面打开*/
$(".order_nav a , .hd_footer a , .mm_menu a , .hd_nav a, .menu a").click(function (event) {
    event.preventDefault();
    window.location = $(this).attr("href");
});