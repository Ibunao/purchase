$(function($){
    //表单验证
    $('#validation-form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            'Goods[name]': {
                required: true
            }
        },

        messages: {
            'Goods[name]':{
                required:"商品名称不能为空"
            }
        },

        invalidHandler: function (event, validator) { //display error alert on form submit
            $('.alert-danger', $('.login-form')).show();
        },

        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
            $(e).remove();
        },

        errorPlacement: function (error, element) {
            if(element.is(':checkbox') || element.is(':radio')) {
                var controls = element.closest('div[class*="col-"]');
                if(controls.find(':checkbox,:radio').length > 1) controls.append(error);
                else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
            }
            else if(element.is('.select2')) {
                error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
            }
            else if(element.is('.chosen-select')) {
                error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
            }
            else error.insertAfter(element.parent());
        },

        submitHandler: function (form) {
        },
        invalidHandler: function (form) {
        }
    });

    var colorbox_params = {
        reposition:true,
        scalePhotos:true,
        scrolling:false,
        previous:'<i class="icon-arrow-left"></i>',
        next:'<i class="icon-arrow-right"></i>',
        close:'&times;',
        current:'{current} of {total}',
        maxWidth:'100%',
        maxHeight:'100%',
        onOpen:function(){
            document.body.style.overflow = 'hidden';
        },
        onClosed:function(){
            document.body.style.overflow = 'auto';
        },
        onComplete:function(){
            $.colorbox.resize();
        }
    };

    $('.ace-thumbnails [data-rel="colorbox"]').colorbox(colorbox_params);
    $("#cboxLoadingGraphic").append("<i class='icon-spinner orange'></i>");
})

/***********************商品分类*******************************/
$('#goods_cat').on('click',function(){
    var goods_cat_id = $('#goods_cat_id').val();
    //商品分类弹出展示
    $.post(
        '?r=goods/cat/ajaxgoodscat',
        {'cat_id':goods_cat_id},
        function(data) {
            bootbox.dialog({
                message: data,
                buttons:
                {
                    "success" :
                    {
                        "label" : "<i class='icon-ok'></i> 确定",
                        "className" : "btn-sm btn-success",
                        "callback": function() {
                            var cat_name;
                            var cat_id = $('#select_cat_id').val();
                            if ($('.select_3').text()) cat_name = $('.select_3').text();
                            else if ($('.select_2').text()) cat_name = $('.select_2').text();
                            else if ($('.select_1').text()) cat_name = $('.select_1').text();

                            $('#goods_cat').val(cat_name);
                            $('#goods_cat_id').val(cat_id);
                        }
                    }
                }
            });
        }
    );
});

/*商品分类筛选*/
$('body').on('click','.goods-cat-select1 li a',function(){
    $('.goods-cat-select1 .active').removeAttr('class');
    $(this).parent().attr('class','active');
    $('.goods-cat-select2').hide();
    $('.goods-cat-select2').html('');
    $('.goods-cat-select3').hide();
    $('.goods-cat-select3').html('');

    $('.select_1').html($(this).text());
    $('.select_2').html('');
    $('.select_3').html('');
    var cat_id = $(this).parent().attr('li-cat_id');
    cat_select(cat_id,1,2);
    $('#select_cat_id').val(cat_id);
});

$('body').on('click','.goods-cat-select2 li a',function(){
    $('.goods-cat-select2 .active').removeAttr('class');
    $(this).parent().attr('class','active');
    $('.goods-cat-select3').hide();
    $('.goods-cat-select3').html('');
    $('.select_2').html($(this).text());
    $('.select_3').html('');
    var cat_id = $(this).parent().attr('li-cat_id');
    cat_select(cat_id,1,3);
    $('#select_cat_id').val(cat_id);
});

$('body').on('click','.goods-cat-select3 li a',function(){
    $('.goods-cat-select3 .active').removeAttr('class');
    $(this).parent().attr('class','active');
    $('.select_3').html($(this).text());
    var cat_id = $(this).parent().attr('li-cat_id');
    $('#select_cat_id').val(cat_id);
});

/**
 * 分类筛选
 * @param cat_id
 * @param val
 * @param show
 */
function cat_select(cat_id,val,show)
{
    $.post(
        '?r=goods/cat/ajaxgoodscat',
        {'cat_id':cat_id,'default':val},
        function (data) {
            if (data) {
                $('.goods-cat-select'+show).show();
                $('.goods-cat-select'+show).html(data);
            }
        }
    );
}

/***********************商品图片*******************************/
$('.image_default_opt').on('click',function(){
    var html = '<div class="tags image_default">'+
        '<span class="label-holder">'+
        '<span class="label label-danger">默认图片</span>'+
        '</span>'+
        '</div>';
    $('.image_default').remove();
    $(this).parent().prev().append(html);
});

$('.image_delete_opt').on('click',function(){
    $(this).parents('li').remove();
});

/***********************商品规格*******************************/
$('#myTab3 li:first').addClass('active');

$('.goods_spec').on('click',function(){
    var goods_id = $('#goods_id').val();
    var goods_type_id = $('#goods_type').val();
    $.post(
        '?r=goods/default/spec',
        {'goods_id':goods_id,'goods_type_id':goods_type_id},
        function (data) {
            bootbox.dialog({
                message: data,
                buttons:
                {
                    "success" :
                    {
                        "label" : "<i class='icon-ok'></i> 确定",
                        "className" : "btn-sm btn-success",
                        "callback": function() {
                            var goods_id = $("input[name='goods_id']:checked").val();
                            var goods_name = $("input[name='goods_id']:checked").attr('data');

                            $('#goods_regular').val(goods_name);
                            $('#goods_regular_id').val(goods_id);
                        }
                    }
                }
            });
        }
    );
});

$('body').on('click','.spec_value', function() {
    var tags_text = $(this).next().html();
    var tags_val = $(this).val();
    var tags_obj = $(this).parent().parent().find('.spec_tags');

    if ($(this).is(':checked')==false) {
        $('.tag_button_'+tags_val).remove();
    } else {
        var tags='<span class="tag tag_button_'+tags_val+'" data="'+tags_val+'">'+tags_text+'<button class="close" type="button">×</button></span>';
        tags_obj.show();
        tags_obj.append(tags);
    }
    var check_num = $(this).parent().parent().find('.spec_value:checked').length;
    $('#myTab3 .active label').text(check_num);
});

$('body').on('click','.close',function(){
    var tags_data = $(this).parent().attr('data');
    var check_num = $(this).parent().parent().parent().parent().parent().find('.spec_value:checked').length;
    $('#myTab3 .active label').text(check_num*1 - 1);
    $(this).parent().remove();
    $('#spec_value_'+tags_data).remove();

});

$('body').on('click','.spec_button',function(){
    var spec_num;
    var total_num = 1;
    var spec_str = '';
    $('#myTab3').find('label').each(function(){
        spec_num = $(this).html() * 1;
        if (spec_num < 1) {
            alert('每个规格项至少选中一项，才能组合成完整的货品信息。');
            return false;
        }

        total_num *=spec_num;
    });

    $('.tag').each(function(){
        spec_str += $(this).attr('data')+',';
    });
    $.post(
        '?r=goods/spec/ajaxspecproducts',
        {'spec_str':spec_str},
        function(result) {
            var obj = eval(result);
            var htmls = '';
            $(obj).each(function(index){
                var val = eval(obj[index]);
                var html = '<tr>';
                html += '<td style="width: 12%;">'+val.name+'</td>';
                html += '<td style="width: 15%;"><input class="col-sm-12" type="text"></td>';
                html += '<td class="center"><label><input type="checkbox" class="ace" name="aaab"><span class="lbl"></span></label></td>';
                html += '<td><input class="col-sm-5" type="text"></td>';
                html += '<td>0</td>';
                html += '<td><input class="col-sm-8" type="text"></td>';
                html += '<td><input class="col-sm-8" type="text"></td>';
                html += '<td><input class="col-sm-8" type="text"></td>';
                html += '<td><input class="col-sm-5" type="text"></td>';
                html += '<td><input class="col-sm-12" type="text"></td>';
                html += '<td class="center"><label><input type="checkbox" class="ace" name="aaa"><span class="lbl"></span></label></td>';
                html += '<td><button class="btn btn-xs btn-danger"><i class="icon-trash bigger-120"></i></button></td>';
                html += '</tr>';

                htmls += html;
            });
            $('.spec-val').html(htmls);
        }
    );

    if (!spec_num) return false;
    $('#spec_product_num').text(total_num);
});

/***********************商品品牌-类型*******************************/
$('#goods_type').on('change',function(){
    var type_id = $(this).val();
    $.post(
        '?r=goods/brand/ajaxtypebrand',
        {'type_id':type_id},
        function(result) {
            $('#brand_id').html(result);

            type_spec(type_id);
        }
    );

    $('.no_spec').show();
    $('.has_spec').hide();
});

function type_spec(type_id)
{
    $.post(
        '?r=goods/spec/ajaxtypespec',
        {'type_id':type_id},
        function(result) {
            if (result == 'success') {
                $('.goods_spec').show();
            } else {
                $('.goods_spec').hide();
            }
        }
    );
}