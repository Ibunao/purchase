<div class="order_login_bg">
    <div class="scale_holder">
    <form method="post" action="/user/index" id="login" autocomplete="off" class="login_form">
        <div class="login_input">
            <input type="text" name="account" placeholder="请输入用户名" value="<?= isset(Yii::$app->session['code'])?Yii::$app->session['code']:'';?>"  id="account" >
          <span class="icon_clear_area fr">
            <i class="icon_clear"></i>
          </span>
        </div>
        <div class="login_input">
            <input type="password" name="password" placeholder="请输入密码" id="password">
          <span class="icon_clear_area fr">
            <i class="icon_clear"></i>
          </span>
        </div>
        <div class="login_button">
            <input type="submit" id="bt_login" onclick="" value="登录">
        </div>
    </form>
    </div>
</div>

<?php if ($error):?>
<div id="inline_login" class="popup_alert">
    <div class="popup_alert_words">
        订货会、用户名或密码错误
    </div>
    <div class="login_button">
        <input type="button" id="submit_item" value="确定">
        <input type="hidden" id="delete_item" value="">
    </div>
</div>
<?php endif;?>

<script type="text/javascript">
    $(function(){
        <?php if ($error):?>
        $.fancybox.open('#inline_login');
        <?php endif;?>
    });
    /*同页面打开*/
    $(".order_nav a , .oct_footer a").click(function (event) {
        event.preventDefault();
        window.location = $(this).attr("href");
    });
    /*删除input内容*/
    $(".icon_clear_area").click(function () {
        $(this).prev("input").val("");
    });
    /*同页面打开*/

    $('#submit_item').on('click',function(){
        location.href='/user/index';
    });
</script>