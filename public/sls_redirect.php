<?php

use SlsConsole\Sls;

require __DIR__ . '/../slsstart.php';
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
