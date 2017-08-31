<?php

use yii\helpers\Url;
use frontend\config\Menu;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= Yii::$app->params['web_sites_title']; ?>后台</title>
    <meta name="keywords" content="<?= Yii::$app->params['web_sites_title']; ?>后台" />
    <meta name="description" content="<?= Yii::$app->params['web_sites_title']; ?>后台" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- basic styles -->
    <link href="/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/font-awesome.min.css" />


    <link rel="stylesheet" href="/css/colorbox.css" />
    <link rel="stylesheet" href="/css/ace.min.css" />
    <link rel="stylesheet" href="/css/ace-rtl.min.css" />
    <link rel="stylesheet" href="/css/ace-skins.min.css" />

    <link rel="stylesheet" href="/css/public.css" />

    <script type="text/javascript">
        window.jQuery || document.write("<script src='/js/jquery-2.0.3.min.js'>"+"<"+"script>");
    </script>


    <script src="/js/ace-extra.min.js"></script>
    <script src="/js/layer/layer.min.js"></script>
</head>

<body>
<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>

    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <i class="icon-leaf"></i>
                    十月妈咪订货会后台管理系统
                </small>
            </a><!-- /.brand -->
        </div><!-- /.navbar-header -->

        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="/avatars/user.jpg" alt="Jason's Photo" />
                            <span class="user-info">
                                <small>欢迎光临,</small>
                                <?= Yii::$app->session['_admini']['name'];?>
                            </span>
                        <i class="icon-caret-down"></i>
                    </a>
                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="#">
                                <i class="icon-cog"></i>
                                设置
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-user"></i>
                                个人资料
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= Url::to(['/admin/logout']) ;?>">
                                <i class="icon-off"></i>
                                退出
                            </a>
                        </li>
                    </ul>
                </li>
            </ul><!-- /.ace-nav -->
        </div><!-- /.navbar-header -->
    </div><!-- /.container -->
</div>

<div class="main-container" id="main-container">
    <script type="text/javascript">
        try{ace.settings.check('main-container' , 'fixed')}catch(e){}
    </script>

    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-shortcuts" id="sidebar-shortcuts">
                <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                    <button class="btn btn-success">
                        <i class="icon-signal"></i>
                    </button>

                    <button class="btn btn-info">
                        <i class="icon-pencil"></i>
                    </button>

                    <button class="btn btn-warning">
                        <i class="icon-group"></i>
                    </button>

                    <button class="btn btn-danger">
                        <i class="icon-cogs"></i>
                    </button>
                </div>

            </div><!-- #sidebar-shortcuts -->

            <ul class="nav nav-list">
                <li class="">
                    <a href="<?=Url::to(['/desktop/default/index']) ;?>">
                        <i class="icon-dashboard"></i>
                        <span class="menu-text"> 控制台</span>
                    </a>
                </li>

                <?php foreach (Menu::$aclList as $k=>$v):?>
                <li class="<?= $this->context->module->id == $k && Yii::$app->request->pathInfo!='desktop/default/index'?'open':'';?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="icon-desktop"></i>
                        <span class="menu-text"><?= $v['name'];?></span>
                        <b class="arrow icon-angle-down"></b>
                    </a>
                    <ul class="submenu" <?= $this->context->module->id == $k && Yii::$app->request->pathInfo!='desktop/default/index'?'style="display: block;"':'';?>>
                        <?php foreach ($v['ctl'] as $vv):?>
                        <li class="<?= $this->context->module->id == $k && in_array(Yii::$app->controller->id,$vv['list_ctl'])?'open':'';?>">
                            <a href="#" class="dropdown-toggle">
                                <i class="icon-double-angle-right"></i>
                                <?= $vv['name'];?>
                                <b class="arrow icon-angle-down"></b>
                            </a>
                            <ul class="submenu" style="display: <?= $this->context->module->id ==$k && in_array(Yii::$app->controller->id,$vv['list_ctl']) && Yii::$app->request->pathInfo != 'desktop/default/index'?'block':'none';?>;">
                                <?php foreach ($vv['act'] as $kkk=>$vvv):?>
                                <li class="<?= $this->context->module->id == $k && Yii::$app->controller->id == $kkk ? 'active' : '';?>">
                                    <a href="<?= Url::to(["/{$k}/{$kkk}/{$vvv['default_id']}"]) ;?>">
                                        <i class="icon-leaf"></i>
                                        <?= $vvv['name'];?>
                                    </a>
                                </li>
                                <?php endforeach;?>
                            </ul>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </li>
                <?php endforeach;?>

            </ul><!-- /.nav-list -->

            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
            </div>
        </div>

        <div class="main-content">
            <div class="breadcrumbs" id="breadcrumbs">
<?= Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
]) ?>
            </div>
            <div class="page-content">
<?= Alert::widget() ?>
<?= $content;?>
                
            </div><!-- /.page-content -->
        </div><!-- /.main-content -->
    </div><!-- /.main-container-inner -->
    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="icon-double-angle-up icon-only bigger-110"></i>
    </a>
</div><!-- /.main-container -->
<!-- basic scripts -->

<script type="text/javascript">
    if("ontouchend" in document) document.write("<script src='/js/jquery.mobile.custom.min.js'>"+"<"+"script>");
</script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/typeahead-bs2.min.js"></script>



<script src="/js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="/js/jquery.ui.touch-punch.min.js"></script>
<script src="/js/jquery.slimscroll.min.js"></script>
<script src="/js/jquery.easy-pie-chart.min.js"></script>
<script src="/js/jquery.sparkline.min.js"></script>
<script src="/js/flot/jquery.flot.min.js"></script>
<script src="/js/flot/jquery.flot.pie.min.js"></script>
<script src="/js/flot/jquery.flot.resize.min.js"></script>


<script src="/js/ace-elements.min.js"></script>
<script src="/js/ace.min.js"></script>


<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery.dataTables.bootstrap.js"></script>

<script src="/js/bootbox.min.js"></script>
<script src="/js/jquery.easy-pie-chart.min.js"></script>

<script src="/js/jquery.colorbox-min.js"></script>
<script src="/js/oct/common.js"></script>
<script>
    var url = '<?= Yii::$app->request->getHostInfo();?>';
</script>
</body>
</html>