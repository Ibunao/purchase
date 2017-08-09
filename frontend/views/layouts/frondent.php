<!doctype html>
<html>
<head>
    <title>
        <?= Yii::$app->params['web_sites_title']; ?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <link type="text/css" rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/hdmain.css?v=2.1.4.1"/>
    <script type="text/javascript" src="<?= Yii::$app->request->baseUrl; ?>/js/jquery-1.7.2.js"></script>
    <script type="text/javascript" src="<?= Yii::$app->request->baseUrl; ?>/js/jquery.ztree.core-3.5.js"></script>
    <script type="text/javascript" src="<?= Yii::$app->request->baseUrl; ?>/js/jquery.fancybox.js?v=2.1.4"></script>
    <link rel="stylesheet" type="text/css" href="<?= Yii::$app->request->baseUrl; ?>/css/jquery.fancybox.css?v=2.1.4" media="screen" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= Yii::$app->request->baseUrl; ?>/images/fav_ico_72.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= Yii::$app->request->baseUrl; ?>/images/fav_ico_144.png">
</head>
<body>
<?= $content;?>
</body>
</html>