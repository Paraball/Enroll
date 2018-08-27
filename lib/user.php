<?php

function is_admin()
{
    if (!isset($_SESSION['user'])) {
        return false;
    }
    return $_SESSION['user'] === 'admin';
}
