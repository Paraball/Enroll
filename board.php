<?php
require_once 'lib/db.php';
require_once 'lib/user.php';
require_once 'lib/post.php';
require_once 'lib/sterilize.php';
session_start();

function init()
{
    if (!isset($_GET['candidate_id'])) {
        header("Location: ");
        die;
    }
    $res = query(
        "SELECT * FROM candidates
         WHERE candidate_id='$_GET[candidate_id]'
         LIMIT 1"
    );
    if (!mysqli_num_rows($res)) {
        header("Location: ");
        die;
    }

    $row = mysqli_fetch_assoc($res);

    global $candidate_name, $county, $district, $party;
    $candidate_name = $row['candidate_name'];
    $county = $row['county'];
    $district = $row['district'];
    $party = $row['party'];
}

function sterilize_district()
{
    global $district;
    if ($district < 10) {
        $district = "0$district";
    }
}

function sterilize_party()
{
    global $party;
    if ($party == null) {
        $party = "無黨籍";
    }
}

$candidate_name = null;
$county = null;
$district = null;
$party = null;
$order = sterilize_get('order', 0, 1, 1);
$view = sterilize_get('view', 0, 2, 2);
$max_page = max_page($view, 1, array('candidate_id' => $_GET['candidate_id']));
$page = sterilize_get('page', 1, 1, $max_page);

init();
sterilize_district();
sterilize_party();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/board.css">
    <link rel="stylesheet" href="css/post.css">
    <style>
        body{
            padding-top: 60px;
        }
        @media only screen and (max-width: 768px) {
            body{
                padding-top: 8px;
            }
        }
    </style>
    <title><?php echo $candidate_name; ?></title>
</head>
<body>

    <div class="container">
<?
if (isset($_GET['post'])) {
    ?>
<div class="row">
    <div class="col-sm-12 text-center">
        <p class="top-hint">您的留言已經提交，請等待管理員審查。</p>
    </div>
</div>
<?
}
?>
        <div class="row">
            <div class="col-md-3">
                <div class="photo">
                    <img src="image/null.png" />
                </div>
                <div class="data">
                    <p>
                        <span class="meta-name badge badge-success">姓名</span>
                        <span class="meta-cont"><?echo $candidate_name; ?></span>
                    </p>
                    <p>
                        <span class="meta-name badge badge-success">縣市</span>
                        <span class="meta-cont"><?echo $county; ?></span>
                    </p>
                    <p>
                        <span class="meta-name badge badge-success">選區</span>
                        <span class="meta-cont"><?echo $district; ?></span>
                    </p>
                    <p>
                        <span class="meta-name badge badge-success">政黨</span>
                        <span class="meta-cont"><?echo $party; ?></span>
                    </p>
                    <p>
                        <span class="meta-name badge badge-success">ID</span>
                        <span class="meta-cont"><?echo $_GET['candidate_id']; ?></span>
                    </p>
                </div>
                <div class="sel-opt">
                    <form>
                        <input type="hidden" name="candidate_id" value="<?echo $_GET['candidate_id']; ?>" />
                        <div class="form-group">
                            <label for="page">頁次（共<?echo $max_page;?>頁）</label>
                            <input name="page" type="<? if($max_page==1) echo "text"; else echo "number"; ?>" min="1" max="<?echo $max_page;?>" value="<?echo $page; ?>" id="page" class="form-control text-center" <? if($max_page==1) echo "disabled"; ?> />
                        </div>
                        <div class="form-group">
                            <label for="order">排序</label>
                            <select name="order" class="form-control" id="order">
                                <option value="1" <?if ($order == '1') {echo 'selected="true"';}?> >按時間倒序</option>
                                <option value="0" <?if ($order == '0') {echo 'selected="true"';}?> >按時間順序</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="view"><?echo is_admin() ? "驗證選項" : "搜尋選項"; ?></label>
                            <select id="view" name="view" class="form-control">
                                <option value="2" <?if ($view == '2') {echo 'selected="true"';}?> >顯示所有內容</option>
                                <option value="1" <?if ($view == '1') {echo 'selected="true"';}?> >不顯示含尚未驗證內容的文章</option>
                                <option value="0" <?if ($view == '0') {echo 'selected="true"';}?> >只顯示含尚未驗證內容的文章</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" method="GET" action="" value="跳轉" class="form-control btn btn-success" />
                        </div>
                    </form>
                </div>
                <?if (!is_admin()) {?>
                    <div class="lmsg">
                        <form method="GET" action="comment.php"><div class="form-group">
                            <input type="hidden" name="candidate_id" value="<?echo $_GET['candidate_id']; ?>" />
                            <input type="submit" value="我要留言" class="btn btn-primary" />
                        </div></form>
                    </div>
                <?}?>
            </div>
            <div class="col-md-9">
<?
if (is_admin()) {
    manage_posts($_GET['candidate_id'], $page, $order, $view);
} else {
    view_posts($_GET['candidate_id'], $page, $order, $view);
}
?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="js/post.js"></script>
</body>
</html>
