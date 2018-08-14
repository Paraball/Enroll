<?php
require_once 'db.php';
require_once 'post.php';

session_start();

function req_exists()
{
    return isset($_POST['candidate_id'])
    && isset($_POST['author'])
    && isset($_POST['content'])
    && isset($_POST['prevent_repeat_saving']);
}

function is_req_valid()
{
    return get_name($_POST['candidate_id'])
    && !empty($_POST['author'])
    && !empty($_POST['content']);
}

function verify()
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
        return $json['success'] == true;
    }
    return false;

}

function prevent_repeat_saving()
{
    if (isset($_SESSION['prevent_repeat_saving'][$_POST['prevent_repeat_saving']])) {
        unset($_SESSION['prevent_repeat_saving'][$_POST['prevent_repeat_saving']]);
        return save_post($_POST['candidate_id'], $_POST['author'], $_POST['content']);
    }
    return false;
}

?>

<!DOCTYPE>
<html>
<head>
    <meta charset="urf-8">
    <title>提交頁面</title>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>

<?php

if (req_exists() && is_req_valid()) {
    if (prevent_repeat_saving()) {
        if (verify()) {
            echo "Post saved.";
        } else {
            echo "Verify failed.";
        }
    } else {
        echo "Repeated post not saved.";
    }
}

?>

</body>
</html>