<?php
session_start();

$rand = null;
function register_pvdup($key)
{
    global $rand;
    $rand = rand();
    $_SESSION[$key] = $rand;
    echo $rand;
}

function verify_pvdup($key)
{
    global $rand;
    if (!isset($_SESSION[$key]) || !isset($_POST[$key])) {
        return false;
    }
    if ($_POST[$key] == $_SESSION[$key]) {
        unset($_SESSION[$key]);
        return true;
    }
    return false;
}
