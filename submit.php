<?php

/**
 * 必須 POST 參數:
 * candidate_id 擬參選人ID
 * au_email     留言者 email
 * cont         未驗證的內容
 * ev_cont      已驗證的內容
 * message      私密訊息
 */

require_once 'lib/db.php';
require_once 'lib/pvdup.php';
require_once 'lib/sterilize.php';

$ev_cont = null;
$cont = null;
$msg = null;
$email = null;

function req_exists()
{
    return isset($_POST['candidate_id'])
    && isset($_POST['au_email'])
    && isset($_POST['cont'])
    && isset($_POST['ev_cont'])
    && isset($_POST['message']);
}

function is_req_valid()
{
    //Verify $_POST
    if (!req_exists()) {
        return "POST ARGUMENTS INVALID";
    }

    //Verify pvdup
    if (!verify_pvdup('cmt')) {
        return "PVDUP FAILED";
    }

    //Verify recaptcha
    if (!verify_recaptcha()) {
        return "RECAPTCHA FAILED";
    }

    //Verify email
    if (!filter_var($_POST['au_email'], FILTER_VALIDATE_EMAIL)) {
        return "EMAIL INVALID";
    } else {
        global $email;
        $email = $_POST['au_email'];
    }

    //Verify candidate's id
    $res = query("SELECT * FROM candidates WHERE candidate_id='$_POST[candidate_id]' LIMIT 1");
    if (!mysqli_num_rows($res)) {
        return "CANDIDATE ID INVALID";
    }

    //Verify contents
    global $ev_cont;
    global $cont;
    $ev_cont = txt_to_sql_content($_POST['ev_cont']);
    $cont = txt_to_sql_content($_POST['cont']);
    if (!$ev_cont && !$cont) {
        return "CONTENT INVALID";
    }

    //Verify messages
    global $msg;
    $msg = txt_to_sql_content($_POST['message']);

    return null;
}

function verify_recaptcha()
{
    if (!isset($_POST["g-recaptcha-response"]) || empty($_POST["g-recaptcha-response"])) {
        return false;
    }

    $data = array(
        'secret' => '6Lff5WkUAAAAAEyv7SOG2lfsbxnv-CYzkB8QUhC8',
        'response' => $_POST["g-recaptcha-response"],
    );
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $result = curl_exec($ch);
    curl_close($ch);

    if ($result) {
        $json = json_decode($result, true);
        $scs = $json['success'];
        return $scs === "true" || $scs === true;
    }
    return false;
}

$v = is_req_valid();
if ($v === null) {
    sterilize_null($cont);
    sterilize_null($ev_cont);
    sterilize_null($msg);
    query(
        "INSERT INTO posts ( candidate_id, author_email, inevident_content, evident_content, secret_message )
         VALUES ( '$_POST[candidate_id]', '$email', $cont, $ev_cont, $msg )"
    );
    header("Location: board.php?candidate_id=$_POST[candidate_id]&post");
} else {
    header("Location: index.php");
}
