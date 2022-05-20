<?php
require __DIR__ . '/../base.php';
define('COOKIE_PERM_WEWORKID', 'sls_wework_id');

$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';
$appid = $_GET['appid'] ?? '';
// 验证state
list($source, $timestamp, $hash) = explode('^', $state);
if (empty($source) || empty($timestamp) || empty($hash)) {
    die('source | timestamp | hash is empty');
} elseif (md5(implode('^', [$source, $timestamp])) !== $hash) {
    die('decrypt fail');
}
//
try {
    $wework = new \SlsConsole\Wework();
    $info = $wework->getUserInfo($code);
    $user_id = $info['user_id'];
    // 判断权限
    $limits = new \SlsConsole\Limits();
    if (!$limits->isPermission($user_id)) {
        die("no permission, user_id = $user_id");
    }
    $perms = $limits->getPerms($user_id);
    //
    if ($user_id && !empty($perms)) {
        \SlsConsole\Cookie::set(COOKIE_PERM_WEWORKID, $user_id);
        header("Location: index.php");
    } else {
        die("wwqrcode login fail, user_id = $user_id");
    }
} catch (\Exception $ex) {
    die($ex->getMessage());
}
