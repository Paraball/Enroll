<?php
require_once 'db.php';
require_once 'sterilize.php';
require_once 'candidate.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header("Location: login.php");
}

if (isset($_GET['post_id']) && isset($_GET['per'])) {

    $sql = "UPDATE posts SET status=$_GET[per] "
        . "WHERE post_id=$_GET[post_id];";
    query($sql);
    header("Location: admin.php");
}

?>

<!DOCTYPE>
<html>
<head>
    <meta charset="utf-8">
    <title>文章管理</title>
</head>
<body>

<?php
$page = sterilize_get('page', 1);
$post_per_page = 10;
$from = ($page - 1) * $post_per_page;
$sql = "SELECT * FROM posts "
    . "WHERE status=0 "
    . "ORDER BY time ASC "
    . "LIMIT $from, $post_per_page;";
$result = query($sql);
if (mysqli_num_rows($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cd_name = Candidate::get_candidate($row['candidate_id'])->name;
        $pid = '#' . sprintf('%06d', $row['post_id']);
        echo "<h3>$pid, $cd_name</h3>";
        echo "<p>$row[evident_content]</p>";
        echo "<p>$row[content]</p>";
        echo "<p>$row[message]</p>";
        echo "<p>$row[author_email], $row[time], ";
        echo "<a href='" . basename(__FILE__) . "?post_id=$row[post_id]&per=1'>核准</a>, ";
        echo "<a href='" . basename(__FILE__) . "?post_id=$row[post_id]&per=-1'>刪除</a></p><hr>";
    }
} else {
    echo '目前沒有待審核的文章';
}

?>

</body>
</html>


