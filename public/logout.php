<?php
require __DIR__ . '/../slsstart.php';

\SlsConsole\Cookie::delete(COOKIE_PERM_WEWORKID);

header("Location: login.php");
