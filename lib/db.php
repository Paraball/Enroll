<?php
$servername = "localhost";
$username = "root";
$password = "wenny086";
$database = "enroll_post";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection to database failed.");
}

function query($sql)
{
    global $conn;
    return $conn->query($sql);
}

function sterilize_null(&$arg)
{
    $arg = $arg === null ? "NULL" : "'$arg'";
}

function get_affected(){
    return $conn->affected_rows;
}
