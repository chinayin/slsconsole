<?php

const IS_LOGIN_PAGE = true;
require __DIR__ . '/../slsstart.php';
$corpid = \SlsConsole\Env::get('wework.corpid');
$agentid = \SlsConsole\Env::get('wework.agentid');
$redirect_uri = \SlsConsole\Env::get('wework.redirect_uri');
?>
<!DOCTYPE html>
<html lang="zh-cmn">
<head>
    <title>SLS Dashboard Login</title>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no,viewport-fit=cover">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="no-siteapp" http-equiv="Cache-Control">
    <link rel="stylesheet" href="./assets/style.css?v=20220520"/>
    <script src="//wwcdn.weixin.qq.com/node/wework/wwopen/js/wwLogin-1.2.7.js" type="text/javascript"></script>
</head>
<body>
<div class="nav-wrap">
    <div class="top-nav">
        <p class="logo">SLS Dashboard</p>
    </div>
</div>
<?php
$source = 'sls';
$time = time();
$string = implode('^', [$source, $time]);
$hash = md5($string);
$state = $string . '^' . $hash;
?>
<div class="code-wrap d-flex">
    <div class="login-stage">
        <div id="wwqr"></div>
    </div>
</div>
<script>
    var wwLogin = new WwLogin({
        "id": "wwqr",
        "appid": "<?php echo $corpid;?>",
        "agentid": "<?php echo $agentid;?>",
        "redirect_uri": "<?php echo $redirect_uri;?>",
        "state": "<?php echo $state;?>",
        "href": "",
        "lang": "",
    });
</script>
</body>
</html>
