/***************************规格管理**********************/
//上移
$('#spec-table').on('click','.spec_up',function(){
    var obj_tr = $(this).parent().parent();
    var obj_prev = obj_tr.prev();
    if (obj_prev.html()) {
        obj_prev.before('<tr>'+obj_tr.html()+'</tr>');
        obj_tr.remove();
    }
    return false;
});

//下移
$('#spec-table').on('click','.spec_down',function(){
    var obj_tr = $(this).parent().parent();
    var obj_next = obj_tr.next();
    if (obj_next.html()) {
        obj_next.after('<tr>'+obj_tr.html()+'</tr>');
        obj_tr.remove();
    }
    return false;
});

//删除
$('#spec-table').on('click','.spec_del',function(){
    var obj_tr = $(this).parent().parent();
    obj_tr.remove();
});

//新增
$('.spec_insert').on('click',function(){
    var obj_tr = $('#spec-table tr:last');
    var html = '<tr><td><input type="text" value="" name="SpecValue[spec_value][]">' +
        '<input type="hidden" value="0" name="SpecValue[spec_value_id][]"></td>' +
        '<td><input type="text" value="" name="SpecValue[alias][]"></td>' +
        '<td><button class="btn btn-xs btn-success spec_up"><i class="fa fa-arrow-up bigger-120"></i></button>' +
        '<button class="btn btn-xs btn-success spec_down"><i class="fa fa-arrow-down bigger-120"></i></button>' +
        '<button class="btn btn-xs btn-danger spec_del"><i class="fa icon-trash bigger-120"></i></button>' +
        '</td></tr>';
    obj_tr.after(html);
    return false;
});