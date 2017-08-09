<?php // var_dump($this->order_state);;die;?>
<footer class="hd_footer oct_footer">
    <a class="hd_footer_myorder fl" href="/default/index">
        <label class="icon_footer_myorder"></label>
        返回
    </a>
    <a class="hd_footer_ordernum fl" href="#">
        <label class="icon_footer_ordernum"></label>
        订货数量：<span class="word_dark_green pd_r5" id="footer_order_number"><?php echo $mon=$this->total_num;?></span>件
    </a>
    <a class="hd_footer_orderprice fl" href="#">
        <label class="icon_footer_orderprice"></label>
        订货金额：<span class="word_dark_green pd_r5" id="footer_order_money"><?php echo $mon=$this->amount;?></span>元
    </a>
    <a class="hd_footer_orderindex fl" href="#">
        <label class="icon_footer_orderprice"></label>
        订货指标：<span class="word_dark_green pd_r5" id="footer_order_target"><?php echo $tar=Yii::app()->session['target'];?></span>元
    </a>
    <a class="hd_footer_ordernd fl" href="#">
        <label class="icon_footer_ordernum"></label>
        完成率：<span class="word_dark_green pd_r5" id="footer_order_percent"><?php if($tar==0||$mon==0){echo 0;}else{echo round($mon/$tar*100,2);}  ?></span>%
    </a>

    <?php if ($this->order_state == 'active'):?>
        <a href="#inline4" class="hd_footer_logout fl fancybox4">
            <label class="icon_footer_logout"></label>
            提交
        </a>
    <?php endif;?>

    <?php if ($this->order_state == 'confirm'):?>
        <a href="#inline5123" class="hd_footer_logout fl repeal">
            <label class="icon_footer_logout"></label>
            撤销
        </a>
    <?php endif;?>

    <?php if ($this->order_state == 'finish'):?>
        <a href="#inline6" class="hd_footer_logout fl fancybox4">
            完成
        </a>
    <?php endif;?>
</footer>
