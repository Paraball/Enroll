<?php
require_once 'lib/db.php';

if (!isset($_GET['type']) || !isset($_GET['county'])) {
    die;
}

//Council candidates
if ($_GET['type'] === 'council') {

    //Response candidates
    if (isset($_GET['district'])) {
        $res = query(
            "SELECT candidate_id, candidate_name FROM candidates
             WHERE county='$_GET[county]' AND district=$_GET[district]"
        );
        $results = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = $row;
        }
        echo json_encode($results);
        die;
    }

    //Response districts
    else {
        $res = query(
            "SELECT district_name FROM districts
             WHERE county='$_GET[county]'
             ORDER BY district ASC"
        );
        $results = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = $row;
        }
        echo json_encode($results);
        die;
    }

}

//Mayor candidates
else {

}
