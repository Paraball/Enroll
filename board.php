<?php
require_once 'lib/db.php';
require_once 'lib/candidate.php';
require_once 'lib/user.php';
session_start();

function init()
{
    if (!isset($_GET['candidate_id'])) {
        header("Location: ");
        die;
    }
    $res = query(
        "SELECT * FROM candidates
         WHERE id='$_GET[candidate_id]'
         LIMIT 1"
    );
    if (!mysqli_num_rows($res)) {
        header("Location: ");
        die;
    }

    $row = mysqli_fetch_assoc($res);

    global $candidate_name, $county, $district, $party;
    $candidate_name = $row['name'];
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

function sterilize_page()
{
    global $page;
    if (!isset($_GET['page'])) {
        $page = 1;
    } else if (!is_numeric($_GET['page'])) {
        $page = 1;
    } else {
        $page = max(1, $_GET['page']);
    }
}

function sterilize_order()
{
    global $order;
    if (!isset($_GET['order'])) {
        $order = 1;
    } else {
        $order = $_GET['order'] == '1' ? 1 : 0;
    }
}

function sterilize_view()
{
    global $view;
    if (!isset($_GET['view'])) {
        $view = 2;
    } else if ($_GET['view'] == '2' || $_GET['view'] == '1' || $_GET['view'] == '0') {
        $view = intval($_GET['view']);
    } else {
        $view = 2;
    }
}

$candidate_name = null;
$county = null;
$district = null;
$party = null;
$page = null;
$order = null;
$view = null;

init();
sterilize_district();
sterilize_party();
sterilize_page();
sterilize_order();
sterilize_view();

function post($time, $id, $ev_cont, $cont)
{
    $time = strtotime($time);
    ?>

<div class="board row">
    <div class="col-sm-3 msg">
        <p>
            <span class="meta-name badge badge-info">Date</span>
            <span class="meta-cont"><?echo date('Y-m-d', $time); ?></span>
        </p>
        <p>
            <span class="meta-name badge badge-info">Time</span>
            <span class="meta-cont"><?echo date('H:i:s', $time); ?></span>
        </p>
        <p>
            <span class="meta-name badge badge-info">Post ID</span>
            <span class="meta-cont"><?echo "#" . str_pad($id, 6, '0', STR_PAD_LEFT); ?></span>
        </p>
        <p class="tool">
            <?if (is_admin()) {?>
                <a href="#">編輯此文章</a><a href="#">刪除此文章</a>
            <?} else {?>
                <a href="#">回報</a>
            <?}?>
        </p>
    </div>
    <div class="cont col-sm-9">
        <?php if ($ev_cont) {?>
            <div class="ev">
                <p><span class="meta-name badge badge-light">已驗證的內容</span></p>
                <?echo $ev_cont; ?>
            </div>
        <?}?>
        <?php if ($cont) {?>
            <div class="nev">
                <p><span class="meta-name badge badge-danger">尚未驗證的內容</span></p>
                <?echo $cont; ?>
            </div>
        <?}?>
    </div>
</div>

<?
}
?>





<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/board.css">
    <title><?php echo $candidate->name; ?></title>
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
                    <?
if (is_admin()) {
    ?>
                    <p>
                        <span class="meta-name badge badge-success">ID</span>
                        <span class="meta-cont"><?echo $_GET['candidate_id']; ?></span>
                    </p>
<?
}
?>
                </div>
                <div class="sel-opt">
                    <form>
                        <input type="hidden" name="candidate_id" value="<?echo $_GET['candidate_id']; ?>" />
                        <div class="form-group">
                            <label for="page">頁次</label>
                            <input name="page" type="number" min="1" value="<?echo $page; ?>" id="page" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label for="order">排序</label>
                            <select name="order" class="form-control" id="order">
                                <option value="1" <?if ($order === 1) {
    echo 'selected="true"';
}
?> >按時間倒序</option>
                                <option value="0" <?if ($order === 0) {
    echo 'selected="true"';
}
?> >按時間順序</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="view">搜尋選項</label>
                            <select id="view" name="view" class="form-control">
                                <option value="2" <?if ($view === 2) {
    echo 'selected="true"';
}
?> >顯示所有內容</option>
                                <option value="1" <?if ($view === 1) {
    echo 'selected="true"';
}
?> >只顯示已驗證的內容</option>
                                <option value="0" <?if ($view === 0) {
    echo 'selected="true"';
}
?> >只顯示尚未驗證的內容</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" method="GET" action="" value="跳轉" class="form-control btn btn-success" />
                        </div>
                    </form>
                </div>
                <div class="lmsg">
                    <form method="GET" action="comment.php"><div class="form-group">
                        <input type="hidden" name="candidate_id" value="<?echo $_GET['candidate_id']; ?>" />
                        <input type="submit" value="我要留言" class="btn btn-primary" />
                    </div></form>
                </div>
            </div>
            <div class="col-md-9">
<?php

$offset = ($page - 1) * 6;
$selected = "post_id";
if ($view != '0') {
    $selected .= ", evident_content";
}
if ($view != '1') {
    $selected .= ", inevident_content";
}
$selected .= ", post_time";
$where = "candidate_id='$_GET[candidate_id]' AND post_status=1";
if ($view == '0') {
    $where .= " AND inevident_content IS NOT NULL";
} else if ($view == '1') {
    $where .= " AND evident_content IS NOT NULL";
}
$pt = "post_id " . ($order ? "DESC" : "ASC");

if (is_admin()) {

} else {
    $res = query(
        "SELECT $selected
         FROM posts
         WHERE $where
         ORDER BY $pt
         LIMIT $offset, 6
        "
    );
    while ($row = mysqli_fetch_assoc($res)) {
        post($row['post_time'], $row['post_id'], $row['evident_content'], $row['inevident_content']);
    }
}

?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
