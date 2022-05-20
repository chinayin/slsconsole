<?php

function is_true($val)
{
    $boolval = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    return ($boolval === null ? false : $boolval);
}

function gen_request_id()
{
    return substr(md5(uniqid(rand(), true)), 8, 16);
}
