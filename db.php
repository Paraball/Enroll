<?php

$servername = "localhost";
$username = "root";
$password = "wenny086";
$database = "enroll_post";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function has_district($county, $district)
{
    if(!is_numeric($district)){
        return false;
    }
    global $conn;
    $sql = "SELECT * FROM districts "
        . "WHERE county='$county' AND district=$district "
        . "LIMIT 1;";
    $result = $conn->query($sql);
    return mysqli_num_rows($result) > 0;
}

function get_counties()
{
    global $conn;
    $sql = "SELECT DISTINCT county FROM districts;";
    $result = $conn->query($sql);
    $counties = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $counties[] = $row["county"];
    }
    return $counties;
}

function get_districts($county)
{
    global $conn;
    $sql = "SELECT name FROM districts "
        . "WHERE county='$county' "
        . "ORDER BY district ASC;";
    $result = $conn->query($sql);
    $districts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $districts[] = $row["name"];
    }
    return $districts;
}

function get_council_candidates($county, $district)
{
    global $conn;
    $sql = "SELECT name, party, id "
        . "FROM council_candidates "
        . "WHERE county='$county' AND district='$district' "
        . "ORDER BY name ASC;";
    $result = $conn->query($sql);
    $candidates = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $candidates[] = new CCD($row["name"], $row["party"], $row["id"]);
    }
    return $candidates;
}

function get_district_name($county, $district)
{
    global $conn;
    $sql = "SELECT name FROM districts "
        . "WHERE county='$county' AND district='$district' "
        . "LIMIT 1";
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row["name"];
    }
    return "";
}

function get_name($id)
{
    global $conn;
    $sql = "SELECT name FROM council_candidates WHERE id='$id' LIMIT 1";
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row["name"];
    }
    return null;
}