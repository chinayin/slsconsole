<?php

const IS_LOGIN_PAGE = true;
require_once __DIR__ . '/../slsstart.php';

use SlsConsole\AppException;
use SlsConsole\Cookie;
use SlsConsole\Env;
use SlsConsole\Limits;
use SlsConsole\SecurityLdap;

$corpid = Env::get('wework.corpid');
$agentid = Env::get('wework.agentid');
$redirect_uri = Env::get('wework.redirect_uri');

// 登陆逻辑
$provider = trim($_POST['provider'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$isLoginAction = false;
$loginErrorStr = '';

if (HAS_SECURITY_LDAP && $_SERVER['REQUEST_METHOD'] === 'POST' && $provider === 'ldap') {
    $isLoginAction = true;
    if (empty($username) || empty($password)) {
        $loginErrorStr = 'username or password is empty.';
        goto html;
    }
    $ldap = new SecurityLdap();
    try {
        if (!$ldap->login($username, $password)) {
            $loginErrorStr = "login fail.";
            goto html;
        }
    } catch (AppException $ex) {
        $loginErrorStr = $ex->getMessage();
        goto html;
    } catch (\Exception $ex) {
        $loginErrorStr = $ex->getMessage();
        goto html;
    }
    // 重新赋值一次
    $user_id = $username;
    // 判断权限
    $limits = new Limits();
    if (!$limits->isPermission($user_id)) {
        $loginErrorStr = "no permission, user_id = $user_id";
        goto html;
    }
    $perms = $limits->getPerms($user_id);
    if ($user_id && !empty($perms)) {
        Cookie::set(COOKIE_PERM_WEWORKID, $user_id);
        header("Location: index.php");
    }
    $loginErrorStr = "login fail. (unknown)";
}

html:
?>
<!DOCTYPE html>
<html lang="zh-cmn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="./assets/favicon.svg">
    <link rel="stylesheet" href="./assets/style.css?v=20240913"/>
    <script src="//wwcdn.weixin.qq.com/node/wework/wwopen/js/wwLogin-1.2.7.js" type="text/javascript"></script>
    <title>SLS Dashboard Login</title>
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
        <div class="login-item" id="wwqr"></div>
        <?php if (HAS_SECURITY_LDAP) { ?>
            <div class="login-item">
                <form id="login-form" action="login.php" method="post">
                    <input hidden="hidden" name="provider" value="ldap"/>
                    <p class="title">Sign in to SLS Dashboard</p>
                    <label>
                        <input type="text" name="username" placeholder="Account"
                               value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"/>
                    </label>
                    <label>
                        <input type="password" name="password" placeholder="Password"/>
                    </label>
                    <label>
                        <button type="submit">Sign in</button>
                    </label>
                    <?php
                    if ($isLoginAction && $loginErrorStr) {
                        echo "<p class=\"error\">$loginErrorStr</p>";
                    }
                    ?>
                </form>
            </div>
        <?php } ?>
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
