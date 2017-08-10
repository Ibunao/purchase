<?php $this->beginContent('//layouts/main'); ?>
<?php echo $this->renderPartial('/common/_header',array());?>
<?php echo $content;?>
<!--右侧主区域-->

<!--弹出-->
<?php echo $this->renderPartial('/common/_popup');?>
<script type="text/javascript">
    /*mainnav*/
    function nav_toggle(obj){
        var cur = $(obj);
        var trgl = cur.find('label');
        cur.closest('.hd_nav_fLevel').removeClass('selected');
        $('.nav_trgl').removeClass('trglUp');
        $('.hd_nav_semi').fadeOut();
        var obj = cur.closest('.hd_nav_fLevel').find('ul');
        if(obj.css('display') != 'none'){
            obj.fadeOut();
            $('#nav_shade').addClass('none');
            trgl.removeClass('trglUp');
        }else{
            obj.fadeIn();
            $('.hd_nav_fLevel').removeClass('selected');
            cur.closest('.hd_nav_fLevel').addClass('selected');
            $('#nav_shade').removeClass('none');
            trgl.addClass('trglUp');
        }
    }
    $('#nav_shade').click(
        function () {
            $('.hd_nav_fLevel').removeClass('selected');
            $(this).addClass('none');
            $('.hd_nav_semi').fadeOut();
        });
    function nav_sort(obj){
        var cur = $(obj);
        var obj = cur.closest('.hd_nav_fLevel_bt').find('label');
        if(cur.closest('.hd_nav_fLevel_bt').hasClass('selected')){
            if (obj.hasClass('sort_a')) {
                obj.removeClass('sort_a');
            }else{
                obj.addClass('sort_a');
            }
        }else{
            $('.hd_nav_fLevel_bt').removeClass('selected');
            cur.closest('.hd_nav_fLevel_bt').addClass('selected');
        }
    }
    /*mainnav*/
</script>
<?php $this->endContent(); ?>