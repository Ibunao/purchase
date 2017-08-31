<?php 
use frontend\assets\AppAsset;
use yii\bootstrap\Alert;

AppAsset::register($this);
 ?>
<?php $this->beginPage() ?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= Yii::$app->params['web_sites_title']; ?>后台</title>
    <meta name="keywords" content="<?= Yii::$app->params['web_sites_title']; ?>后台" />
    <meta name="description" content="<?= Yii::$app->params['web_sites_title']; ?>后台" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/font-awesome.min.css" />

    <link rel="stylesheet" href="/css/ace.min.css" />
    <link rel="stylesheet" href="/css/ace-rtl.min.css" />

</head>
<script type="text/javascript">
    var isChrome = window.navigator.userAgent.indexOf("Chrome") !== -1;
    if (!isChrome) {
        alert("不是Chrome浏览器");
        location.href='/';
    }
</script>
<?php $this->beginBody() ?>
<body class="login-layout" style="background-color: #478fca !important;">
<div class="main-container">
<div class="main-content">
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<div class="login-container">
<div class="center">
    <h1>
        <i class="icon-leaf green"></i>
        <span class="red">十月妈咪</span>
        <span class="white">订货会后台</span>
    </h1>
    <h4 class="white">&copy; Company 上海有喜</h4>
</div>

<div class="space-6"></div>

<div class="position-relative">
    <div id="login-box" class="login-box visible widget-box no-border">
        <div class="widget-body">
            <div class="widget-main">
                <h4 class="header blue lighter bigger">
                    <i class="icon-coffee green"></i>
                    请输入你的登录信息
                </h4>
                <div class="space-6"></div>
                <form action="" method="post">
                    <fieldset>
                        <label class="block clearfix">
                            <span class="block input-icon input-icon-right">
                                <input type="text" class="form-control" name="Desktopusers[name]"/>
                                <i class="icon-user"></i>
                            </span>
                        </label>
                        <label class="block clearfix">
                            <span class="block input-icon input-icon-right">
                                <input type="password" class="form-control" name="Desktopusers[password]/>
                                <i class="icon-lock"></i>
                            </span>
                        </label>

                        <div class="space"></div>
<?php 
if (Yii::$app->session->hasFlash('error')) {
    echo Alert::widget([ 'options' => ['class' => 'alert-warning'], 'body' => Yii::$app->session->getFlash('error'), ]);
} 
?>
                        <div class="clearfix">
                            <button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
                                <i class="icon-key"></i>
                                登录
                            </button>
                        </div>

                        <div class="space-4"></div>
                    </fieldset>
                </form>

            </div><!-- /widget-main -->

            <div class="toolbar clearfix">
                <div>
                    <a href="#" onclick="show_box('forgot-box')" class="forgot-password-link">
                        <i class="icon-arrow-left"></i>
                        访问首页
                    </a>
                </div>

            </div>

        </div><!-- /widget-body -->
    </div><!-- /login-box -->

</div><!-- /position-relative -->
</div>
</div><!-- /.col -->
</div><!-- /.row -->
</div>
</div>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    function show_box(id) {
        location.href='/';
    }
</script>
</body>
<?php $this->endBody() ?>
</html>
<?php $this->endPage() ?>