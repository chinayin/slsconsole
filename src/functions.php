<?php

/*
 * This file is part of the SlsConsole package.
 *
 * @link   https://github.com/chinayin/slsconsole
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function is_true($val)  //NOSONAR
{
    $boolval = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    return ($boolval === null ? false : $boolval);
}

function gen_request_id()  //NOSONAR
{
    return substr(md5(uniqid((string)rand(), true)), 8, 16);
}
