<?php

/*
 * This file is part of the SlsConsole package.
 *
 * @link   https://github.com/chinayin/slsconsole
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../slsstart.php';  //NOSONAR

\SlsConsole\Cookie::delete(COOKIE_PERM_WEWORKID);

header("Location: login.php");
