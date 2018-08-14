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

function verify() //TODO

{
    return true;
}

function prevent_repeat_saving()
{
    if (isset($_SESSION['prevent_repeat_saving'][$_POST['prevent_repeat_saving']])) {
        unset($_SESSION['prevent_repeat_saving'][$_POST['prevent_repeat_saving']]);
        echo "Sesssion unset";
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
</head>
<body>

<?php

if (req_exists() && is_req_valid()) {

    if (verify() && prevent_repeat_saving()) {
        echo "Post saved.";
    } else {
        echo "Post not saved.";
    }
}

?>

</body>
</html>