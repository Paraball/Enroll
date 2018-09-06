<?php
require_once 'lib/db.php';
require_once 'lib/user.php';
require_once 'lib/sterilize.php';
require_once 'lib/post.php';
if (!is_admin()) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        log_in($_POST['username'], $_POST['password']);
        header("Location: admin.php");
    }
    header("Location: login.php");
}

$df_ct = null;
$df_dis = null;
function echo_counties()
{
    global $df_ct;
    $df_ct = isset($_GET['county']) ? $_GET['county'] : null;
    $res = query("SELECT DISTINCT county from districts");

    echo "<select class='form-control' id='county' name='county'>";
    echo "<option value=''>所有縣市</option>";
    while ($row = mysqli_fetch_assoc($res)) {
        if ($df_ct == $row['county']) {
            echo "<option value='$row[county]' selected>$row[county]</option>";
        } else {
            echo "<option value='$row[county]'>$row[county]</option>";
        }
    }
    echo "</select>";
}

function echo_districts()
{
    global $df_ct;
    if (!$df_ct) {
        echo "<select class='form-control' disabled id='district' name='district'><option value=''>所有選區</option></select>";
        return;
    }
    global $df_dis;
    $df_dis = isset($_GET['district']) ? $_GET['district'] : null;
    $res = query("SELECT district_name FROM districts where county='$df_ct' ORDER BY district ASC");

    echo "<select class='form-control' id='district' name='district'>";
    echo "<option value=''>所有選區</option>";
    $i = 1;
    while ($row = mysqli_fetch_assoc($res)) {
        $dname = "【" . ($i < 10 ? "0" . $i : $i) . "】" . $row['district_name'];
        if ($df_dis == $i) {
            echo "<option value='$i' selected>$dname</option>";
        } else {
            echo "<option value='$i'>$dname</option>";
        }
        $i++;
    }
    echo "</select>";
}

function echo_candidates()
{
    global $df_dis;
    if (!$df_dis) {
        echo "<select class='form-control' disabled id='candidate' name='candidate_id'><option value=''>所有擬參選人</option></select>";
        return;
    }
    global $df_ct;
    $df_cn = isset($_GET['candidate_id']) ? $_GET['candidate_id'] : null;
    $res = query("SELECT candidate_id, candidate_name FROM candidates WHERE county='$df_ct' AND district=$df_dis ORDER BY candidate_id ASC");

    echo "<select class='form-control' id='candidate' name='candidate_id'>";
    echo "<option value=''>所有擬參選人</option>";
    while ($row = mysqli_fetch_assoc($res)) {
        if ($df_cn == $row['candidate_id']) {
            echo "<option value='$row[candidate_id]' selected>$row[candidate_name]</option>";
        } else {
            echo "<option value='$row[candidate_id]'>$row[candidate_name]</option>";
        }
    }
    echo "</select>";
}

function echo_options($arg, $array)
{
    if (!isset($_GET[$arg])) {
        foreach ($array as $k => &$v) {
            echo "<option value='$k'>$v</option>";
        }
        return;
    }
    foreach ($array as $k => &$v) {
        if ($_GET[$arg] == $k) {
            echo "<option value='$k' selected>$v</option>";
        } else {
            echo "<option value='$k'>$v</option>";
        }
    }
}

function get_page_url($target_page)
{
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (strpos($url, "page=") !== false) {
        return preg_replace("/page=\d*/", "page=$target_page", $url);
    }
    if (strpos($url, "?") !== false) {
        return $url . "&page=$target_page";
    }
    return $url . "?page=$target_page";
}

function echo_page_js($target_page)
{
    $url = get_page_url($target_page);
    echo "onclick=\"location.href='" . $url . "'\"";
}

$order = sterilize_get('order', 0, 1, 1);
$status = sterilize_get('post_status', -1, 0, 3);
$view = sterilize_get('view', 0, 2, 2);

if (isset($_GET['candidate_id']) && $_GET['candidate_id']) {
    $where['candidate_id'] = $_GET['candidate_id'];
} else if (isset($_GET['county']) && $_GET['county']) {
    $where['county'] = $_GET['county'];
    if (isset($_GET['district']) && is_numeric($_GET['district'])) {
        $where['district'] = $_GET['district'];
    }
} else {
    $where = array();
}

$max_page = max_page($view, $status, $where);
$page = sterilize_get('page', 1, 1, $max_page);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/post.css">
    <style>
        body{
            padding-top: 60px;
        }
        .board {
            padding-left: 10px;
            padding-right: 10px;
            margin-left: -5px;
        }
        form{
            margin-bottom: 36px;
        }
    </style>
    <title>管理員頁面</title>
</head>
<body>
    <div class="container">
        <form>
            <div class="row">
                <div class="col-lg-2 col-sm-4">
                    <div class="form-group form-row">
                        <label for="county">縣市</label>
                        <?echo_counties();?>
                    </div>
                    <div class="form-group form-row">
                        <label for="county">選區</label>
                        <?echo_districts();?>
                    </div>
                    <div class="form-group form-row">
                        <label for="county">擬參選人</label>
                        <?echo_candidates();?>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-8">
                    <div class="form-group form-row">
                        <label for="county">文章排序</label>
                        <select class="form-control" name="order">
<?
echo_options('order', array(
    '1' => '由新而舊',
    '0' => '由舊而新',
));
?>
                        </select>
                    </div>
                    <div class="form-group form-row">
                        <label for="county">狀態過濾</label>
                        <select class="form-control" name="post_status">
<?
echo_options('post_status', array(
    '0' => '只顯示待審核的文章',
    '-1' => '只顯示已刪除的文章',
    '1' => '只顯示已發布的文章',
    //'2' => '只顯示待審核和已刪除的文章',
    //'3' => '顯示所有文章',
));
?>
                        </select>
                    </div>
                    <div class="form-group form-row">
                        <label for="county">佐證資料過濾</label>
                        <select class="form-control" name="view">
<?
echo_options('view', array(
    '2' => '顯示所有文章',
    '1' => '不顯示含未提供佐證的文章',
    '0' => '只顯示含未提供佐證的文章',
));
?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-7 col-sm-12">
                    <div class="form-group form-row">
                        <label for="county">頁數（共 <?echo $max_page; ?> 頁）</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <input class="btn btn-outline-secondary" type="button" value="第一頁"
<?
if ($page == 1) {
    echo "disabled";
} else {
    echo_page_js(1);
}
?>
                                />
                                <input class="btn btn-outline-secondary" type="button" value="上一頁"
<?
if ($page == 1) {
    echo "disabled";
} else {
    echo_page_js($page - 1);
}
?>
                                />
                            </div>
                            <input name="page" type="
<?if ($max_page == 1) {
    echo "text";
} else {
    echo "number";
}
?>"

                                min="1" max="<?echo $max_page; ?>" class="form-control text-center" placeholder="" aria-label="" aria-describedby="basic-addon1" value="<?echo $page; ?>" <? if ($max_page==1) echo "disabled"; ?>>
                            <div class="input-group-append">
                                <input class="btn btn-outline-secondary" type="button" value="下一頁"
<?
if ($page == $max_page) {
    echo "disabled";
} else {
    echo_page_js($page + 1);
}
?>
                                />
                                <input class="btn btn-outline-secondary" type="button" value="最末頁"
<?
if ($page == $max_page) {
    echo "disabled";
} else {
    echo_page_js($max_page);
}
?>
                                />
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <label>快速搜尋</label>
                        <input type="text" class="form-control" placeholder="在這裡輸入擬參選人姓名、留言者信箱等資訊，快速搜尋相關文章。">
                    </div>
                    <div class="form-group form-row">
                        <label>查詢文章</label>
                        <input type="submit" class="form-control btn btn-primary" value="查詢" />
                    </div>
                </div>
            </div>
        </form>
<?
$order = sterilize_get('order', 0, 1, 1);
$status = sterilize_get('post_status', -1, 0, 3);
$view = sterilize_get('view', 0, 2, 2);

if (isset($_GET['candidate_id']) && $_GET['candidate_id']) {
    $where['candidate_id'] = $_GET['candidate_id'];
} else if (isset($_GET['county']) && $_GET['county']) {
    $where['county'] = $_GET['county'];
    if (isset($_GET['district']) && is_numeric($_GET['district'])) {
        $where['district'] = $_GET['district'];
    }
} else {
    $where = array();
}

$max_page = max_page($view, $status, $where);
$page = sterilize_get('page', 1, 1, $max_page);
permit_posts($page, $order, $view, $status, $where);
?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="js/admin.js"></script>
    <script src="js/post.js"></script>
</body>
</html>


