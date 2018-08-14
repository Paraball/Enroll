<?php

function sterilize_get($arg, $min)
{
    if (!isset($_GET[$arg])) {
        return $min;
    }
    if (!is_numeric($_GET[$arg])) {
        return $min;
    }
    return max($_GET[$arg], $min);
}
