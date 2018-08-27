<?php
session_start();

$rand = null;
function register_pvdup()
{
    global $rand;
    $rand = rand();
    $_SESSION['pvdup'] = $rand;
    echo $rand;
}

function verify_pvdup()
{
    global $rand;
    if (!isset($_SESSION['pvdup']) || !isset($_POST['pvdup'])) {
        return false;
    }
    if ($_POST['pvdup'] == $_SESSION['pvdup']) {
        unset($_SESSION['pvdup']);
        return true;
    }
    return false;
}
