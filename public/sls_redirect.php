<?php

/*
 * This file is part of the SlsConsole package.
 *
 * @link   https://github.com/chinayin/slsconsole
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SlsConsole\Sls;

require_once __DIR__ . '/../slsstart.php';  //NOSONAR
$tag = $_GET['tag'] ?? '';
// PARAM
$c = SLS_PERM_CONFIGS[$tag] ?? [];
if (empty($c)) {
    die("tag config not found.");
}
$projectName = $c['project'] ?? '';
$logstoreName = $c['logstore'] ?? '';
$options = $c['options'] ?? [];
if (isset($c['sls']) && !empty($c['sls'])) {
    $options = array_merge($options, ['ns' => $c['sls']]);
}
try {
    $signInUrl = (new Sls())->buildSigninUrl($projectName, $logstoreName, $options);
    if (empty($signInUrl)) {
        die('signInUrl error.');
    }
    header("Location: " . $signInUrl);
} catch (Exception $e) {
    die($e->getMessage());
}
