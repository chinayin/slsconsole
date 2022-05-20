<?php
// 检测PHP环境
if (version_compare(PHP_VERSION, '7.2', '<')) {
    die('require PHP > 7.2 !');
}

//error_reporting(0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
const SLS_CONSOLE_VERSION = '2.0';
const EXT = '.php';
const DS = DIRECTORY_SEPARATOR;
const IS_CLI = PHP_SAPI == 'cli';
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);
defined('ENV_PREFIX') or define('ENV_PREFIX', 'PHP_');
defined('ROOT_PATH') or define('ROOT_PATH', __DIR__ . DS);
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);
defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);
defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'log' . DS);
// 修复json_encode时精度问题
ini_set('serialize_precision', -1);
// 载入vendor
require ROOT_PATH . 'vendor/autoload.php';
// 加载环境变量配置文件
if (is_file(ROOT_PATH . '.env')) {
    $env = parse_ini_file(ROOT_PATH . '.env', true);

    foreach ($env as $key => $val) {
        $name = ENV_PREFIX . strtoupper($key);

        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $item = $name . '_' . strtoupper($k);
                putenv("$item=$v");
            }
        } else {
            putenv("$name=$val");
        }
    }
}
// request_id
if (!IS_CLI && isset($_SERVER['TRACE_PHP_ID']) && !empty($_SERVER['TRACE_PHP_ID'])) {
    define('TRACE_PHP_ID', $_SERVER['TRACE_PHP_ID']);
    define('REQUEST_ID', TRACE_PHP_ID);
}
defined('REQUEST_ID') || define('REQUEST_ID', gen_request_id());
IS_CLI || header('x-request-id: ' . REQUEST_ID);
