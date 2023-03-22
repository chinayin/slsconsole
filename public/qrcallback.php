<?php

/*
 * This file is part of the SlsConsole package.
 *
 * @link   https://github.com/chinayin/slsconsole
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../base.php';  //NOSONAR
define('COOKIE_PERM_WEWORKID', 'sls_wework_id');

$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';
$appid = $_GET['appid'] ?? '';
// 验证state
[$source, $timestamp, $hash] = explode('^', $state);
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
