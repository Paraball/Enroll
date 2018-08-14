<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header("Location: login.php");
}

if (isset($_GET['post_id']) && isset($_GET['per'])) {

    include_once 'db.php';
    global $conn;
    $sql = "UPDATE posts SET status=$_GET[per] "
        . "WHERE post_id=$_GET[post_id];";
    $conn->query($sql);
    header("Location: admin.php");
}

?>

<!DOCTYPE>
<html>
<head>
    <meta charset="utf-8">
    <title>管理</title>
</head>
<body>

<?php
include_once 'db.php';
include_once 'sterilize.php';

function sterialize_page()
{
    if (!isset($_GET['page'])) {
        return 1;
    }
    if (!is_numeric($_GET['page'])) {
        return 1;
    }
    if ($_GET['page'] < 1) {
        return 1;
    }
    return $_GET['page'];
}

global $conn;
$page = sterilize_get('page', 1);
$post_per_page = 10;
$from = ($page - 1) * $post_per_page;
$sql = "SELECT * FROM posts "
    . "WHERE status=0 "
    . "ORDER BY time ASC "
    . "LIMIT $from, $post_per_page;";
$result = $conn->query($sql);
if (mysqli_num_rows($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cd_name = get_name($row['candidate_id']);
        echo "<h3>$cd_name</h3>";
        echo "<p>$row[content]</p>";
        echo "<p>$row[author], $row[time], ";
        echo "<a href='" . basename(__FILE__) . "?post_id=$row[post_id]&per=1'>核准</a>, ";
        echo "<a href='" . basename(__FILE__) . "?post_id=$row[post_id]&per=-1'>刪除</a></p><hr>";
    }
} else {
    echo '目前沒有待審核的文章';
}

?>

</body>
</html>


