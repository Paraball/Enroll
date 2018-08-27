<?php
require_once 'lib/candidate.php';

if (!isset($_POST['type']) || !isset($_POST['county'])) {
    die;
}

//Council candidates
if ($_POST['type'] === 'council') {

    //Response candidates
    if (isset($_POST['district'])) {
        $candidates = Candidate::get_candidates($_POST['county'], $_POST['district'], 'simple');
        echo json_encode($candidates);
        die;
    }

    //Response districts
    else {
        echo json_encode(get_districts($_POST['county']));
        die;
    }

}

//Mayor candidates
else {

}
