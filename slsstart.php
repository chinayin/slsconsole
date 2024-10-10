<?php

/*
 * This file is part of the SlsConsole package.
 *
 * @link   https://github.com/chinayin/slsconsole
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SlsConsole\Cookie;
use SlsConsole\Env;
use SlsConsole\Limits;
use SlsConsole\SlsConfigs;

require_once __DIR__ . '/base.php';     //NOSONAR

defined('IS_LOGIN_PAGE') || define('IS_LOGIN_PAGE', false);
define("HAS_SECURITY_LDAP", (bool)Env::get('ldap.dsn'));
define('COOKIE_PERM_WEWORKID', 'sls_wework_id');
// 企业微信ID
$slsWeworkId = Cookie::get(COOKIE_PERM_WEWORKID);
define('SLS_WEWORK_ID', empty($slsWeworkId) ? null : $slsWeworkId);
// 判断登录
if (empty(SLS_WEWORK_ID) && !IS_LOGIN_PAGE) {
    header("Location: login.php");
    die('ERROR: no login');
}
// 获取本人实际的权限
$limits = new Limits();
$permKeys = SLS_WEWORK_ID ? $limits->getPerms(SLS_WEWORK_ID) : [];
$slsConfigs = (new SlsConfigs())->getConfigs();
$permConfigs = [];
foreach ($slsConfigs as $key => $cfg) {
    if (in_array($key, $permKeys)) {
        $permConfigs[$key] = $cfg;
    }
}
// 拥有的权限
define('SLS_PERM_CONFIGS', $permConfigs);
// 权限分组
$slsConfigsGroups = array_filter(array_unique(array_column($permConfigs, 'group')));
define('SLS_PERM_CONFIGS_GROUPS', $slsConfigsGroups);
