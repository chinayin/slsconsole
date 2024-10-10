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
use SlsConsole\Sts;

require_once __DIR__ . '/../slsstart.php';  //NOSONAR
$tag = $_GET['tag'] ?? '';
// PARAM
$c = SLS_PERM_CONFIGS[$tag] ?? [];
if (empty($c)) {
    die("ERROR: tag config not found.");
}
$projectName = $c['project'] ?? '';
$logstoreName = $c['logstore'] ?? '';
$options = $c['options'] ?? [];
$suffix = $c['sls'] ?? '';
$region = $c['region'] ?? 'cn-beijing';
if (!empty($c['sls'])) {
    $options = array_merge($options, ['ns' => $c['sls']]);
}
try {
    $assumeRole = (new Sts($suffix))->assumeRole();
    $sls = new Sls(new \Darabonba\OpenApi\Models\Config([
        'accessKeyId' => $assumeRole['Credentials']['AccessKeyId'],
        'accessKeySecret' => $assumeRole['Credentials']['AccessKeySecret'],
        'securityToken' => $assumeRole['Credentials']['SecurityToken'],
        'regionId' => 'cn-shanghai'
    ]));
    //$embedUrl = $sls->buildSigninUrl($projectName, $logstoreName, $options);
    $embedUrl = $sls->buildEmbedUrl($region, $projectName, $logstoreName, $options);
    if (empty($embedUrl)) {
        die('ERROR: embedUrl error.');
    }
    header("Location: " . $embedUrl);
} catch (Exception $e) {
    die("ERROR: {$e->getMessage()}");
}
