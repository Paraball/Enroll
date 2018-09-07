<?php
require_once 'lib/db.php';
require_once 'lib/user.php';
require_once 'lib/sterilize.php';

if (!is_admin()) {
    die;
}

if (isset($_POST['save'])) {
    if (isset($_POST['post_id']) && is_numeric($_POST['post_id']) && isset($_POST['ev']) && isset($_POST['nev'])) {
        $ev = txt_to_sql_content($_POST['ev']);
        $ev = $ev ? "'$ev'" : "NULL";
        $nev = txt_to_sql_content($_POST['nev']);
        $nev = $nev ? "'$nev'" : "NULL";
        $res = query("UPDATE posts SET evident_content=$ev, inevident_content=$nev WHERE post_id=$_POST[post_id]");
        if ($res) {
            header("Location: edit.php?post_id=$_POST[post_id]&success");
        } else {
            echo "FAILED";
        }
    }
    die;
}

if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
    die;
}

$res = query("SELECT evident_content, inevident_content FROM posts WHERE post_id=$_GET[post_id] LIMIT 1");
if (!mysqli_num_rows($res)) {
    die;
}

if (isset($_GET['status'])) {
    if (!is_numeric($_GET['status'])) {
        die;
    }
    if ($_GET['status'] == -2) {
        query("DELETE FROM posts WHERE post_id=$_GET[post_id]");
        echo 1;
        die;
    }
    $res = query("UPDATE posts SET post_status=$_GET[status] WHERE post_id=$_GET[post_id]");
    echo $res == true ? "1" : "0";
    die;
}

$row = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
    <meta content="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/post.css">
    <title>文章編輯</title>
    <style>
        body{
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .top-hint {
            background: #CFC;
            border-radius: 3px;
            padding: 0.5em;
            margin-bottom: 1.25em;
        }
    </style>
</head>

<body>
    <div class="container">
<?
if(isset($_GET['success'])){
?>
<div class="row">
    <div class="col-sm-12 text-center">
        <p class="top-hint">文章已更新</p>
    </div>
</div>
<?
}
?>
        <form action="edit.php" method="POST">
            <input type="hidden" name="save" value="1" />
            <input type="hidden" name="post_id" value="<?echo $_GET['post_id'] ?>" />
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label>已驗證的訊息</label>
                    <textarea name="ev" id="evIn" class="form-control" rows="10"><?echo html_to_txt($row['evident_content']); ?></textarea>
                </div>
                <div class="col-sm-6 form-group">
                    <label>未驗證的訊息</label>
                    <textarea name="nev" id="nevIn" class="form-control" rows="10"><?echo html_to_txt($row['inevident_content']); ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-inline">
                    <input id="preview" type="hidden" class="btn btn-primary mr-sm-2" value="預覽" />
                    <input type="submit" class="btn btn-primary" value="儲存" id="submit" />
                </div>
            </div>
        </form>
        <hr />
        <div style="display: none" id="evOut" class="row ev"><p><span class="meta-name badge badge-success">已驗證的內容</span></p></div>
        <div style="display: none" id="nevOut" class="row nev"><p><span class="meta-name badge badge-danger">尚未驗證的內容</span></p></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="js/preview.js"></script>
</body>
</html>
