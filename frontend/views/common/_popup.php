<div id="inline1" style="width:433px;display: none;">
    <div class="popup_order_all">

    </div>
</div>

<div id="inline3" class="popup_alert" style="display: none;">
    <div class="popup_alert_words">
        确定删除？
    </div>
    <div class="login_button">
        <input type="button" id="bt_login" value="确定">
        <input type="hidden" id="delete_item" value="">
    </div>
</div>

<div id="inline4" class="popup_alert" style="display: none;">
    <?php if ($this->amount >= Yii::app()->session['target']):?>
    <div class="popup_alert_words">
        确定提交？
    </div>
    <div class="login_button">
        <input type="button" id="bt_order" value="确定">
        <input type="hidden" id="delete_item" value="">
    </div>
    <?php elseif ($this->amount && $this->amount < Yii::app()->session['target']):?>
    <div class="popup_alert_words">
        未达到订货指标,确定提交？
    </div>
        <div class="login_button">
            <input type="button" id="bt_order" value="确定">
            <input type="hidden" id="delete_item" value="">
        </div>
    <?php else :?>
    <div class="popup_alert_words">
        您未预定商品没法提交
    </div>
    <?php endif;?>
</div>

<div id="inline5" class="popup_alert" style="display: none;">
    <div class="popup_alert_words">
        确定撤销？
    </div>
    <div class="login_button">
        <input type="button" id="bt_order2" value="确定">
        <input type="hidden" id="delete_item" value="">
    </div>
</div>
<div id="inline7" class="popup_alert" style="display: none;">
    <div class="popup_alert_words">
        订单已完成没法撤销？
    </div>
    <div class="login_button">
        <input type="button" id="bt_order3" value="确定">
        <input type="hidden" id="delete_item" value="">
    </div>
</div>
<div id="inline6" class="popup_alert" style="display: none;">
    <div class="popup_alert_words">
        管理员已审核完成，无法再进行操作！
    </div>
</div>