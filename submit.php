<?php

require_once 'db.php';
require_once 'post.php';

function echo_success()
{
    echo 'Your post has been submitted.';
}

function req_exists()
{
    return isset($_POST['candidate_id'])
    && isset($_POST['author'])
    && isset($_POST['content']);
}

function is_req_valid()
{
    return get_name($_POST['candidate_id'])
    && !empty($_POST['author'])
    && !empty($_POST['content']);
}

function verify()
{
    return true;
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
    if (verify() && save_post($_POST['candidate_id'], $_POST['author'], $_POST['content'])) {
        echo "Post saved.";
    } else {
        echo "Post not saved.";
    }
}

?>

</body>
</html>