<?php
require_once 'db.php';
session_start();

function log_in($username, $password)
{
    $res = query("SELECT password FROM users WHERE username='$username' LIMIT 1");
    while ($row = mysqli_fetch_assoc($res)) {
        echo hash('sha256', $password) . "<br>";
        echo $row['password'] . "<br>";
        if (strcasecmp(hash('sha256', $password), $row['password']) === 0) {
            $_SESSION['user'] = 'admin';
            return true;
        }
        return false;
    }
    return false;
}

function log_out()
{
    if (!isset($_SESSION['user'])) {
        return false;
    }
    if ($_SESSION['user'] === 'admin') {
        unset($_SESSION['user']);
        return true;
    }
    return false;
}

function is_admin()
{
    if (!isset($_SESSION['user'])) {
        return false;
    }
    if ($_SESSION['user'] === 'admin') {
        $_SESSION['user'] = 'admin'; //Extend session
        return true;
    }
    return false;
}
