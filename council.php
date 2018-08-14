<?php
require_once 'db.php';
require_once 'post.php';
require_once 'sterilize.php';

session_start();
$r = '' . rand();
$_SESSION['prevent_repeat_saving'][$r] = '1';

class CCD
{

    public $name;
    public $party;
    public $id;

    public function __construct($name, $party, $id)
    {
        $this->name = $name;
        $this->party = $party;
        $this->id = $id;
    }

}

if (isset($_GET['id'])) {

    $name = get_name($_GET['id']);
    if (!$name) {
        header("Location: " . basename(__FILE__));
        die;
    }
    $title = $name;
    $status = 'candidate';

} else if (isset($_GET['county']) && isset($_GET['district'])) {

    if (!has_district($_GET['county'], $_GET['district'])) {
        header("Location: " . basename(__FILE__));
        die;
    }
    $title = $_GET['county'] . " " . sprintf("%02d", $_GET['district']) . " 選區";
    $status = 'district';

} else if (!empty($_GET)) {

    header("Location: " . basename(__FILE__));
    die;

} else {

    $title = "首頁";
    $status = 'home';
}

?>

<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link type="text/css" rel="stylesheet" href="css/candidate.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="js/form-validate.js"></script>
</head>
<body>

<?php

if ($status == 'candidate') {

    $posts = get_posts($_GET['id'], sterilize_get('page', 1));
    if (empty($posts)) {
        echo "<p>目前沒有訊息。</p>";
    } else {
        foreach ($posts as &$p) {
            echo "<h3>$p->author</h3>";
            echo "<p>$p->content</p>";
            echo "<p>$p->time</p><hr>";
        }
    }
    ?>

    <form action="submit.php" method="POST">
        <h2>留下訊息</h2>
        <input name="candidate_id" type="hidden" value="<?php echo $_GET['id']; ?>" />
        <input name="prevent_repeat_saving" type="hidden" value="<?php echo $r; ?>" />
        <p><textarea id="content" name="content" maxlength="5000" ></textarea></p>
        <p>您的暱稱: <input id="author" name="author" type="text" maxlength="30" />
        <div class="g-recaptcha" data-sitekey="6Lff5WkUAAAAAC-tsW7S0CtD4BD35DD4d41Oi92i"></div>
        <input id="submit" type="submit" value="提交" /><span id="errm"></span></p>
    </form>

    <?

} else if ($status == 'district') {

    $candidates = get_council_candidates($_GET['county'], $_GET['district']);
    $dis_name = get_district_name($_GET['county'], $_GET['district']);

    echo "<h1>$_GET[county] " . sprintf("%02d", $_GET['district']) . " 選區：$dis_name</h1>";

    if (empty($candidates)) {

        echo "<p>目前沒有表態參選的議員。</p>";

    } else {

        foreach ($candidates as &$c) {
            if ($c->party == null) {
                $p = "無黨籍";
            } else {
                $p = $c->party;
            }
            $url = basename(__FILE__) . "?id=$c->id";
            echo "<a href='$url'>";
            echo "<h2>" . $c->name . "（" . $p . "）</h2>";
            echo "</a>";
        }
    }

} else if ($status == 'home') {

    $counties = get_counties();
    foreach ($counties as &$county) {
        echo "<h1>$county</h1>";
        $districts = get_districts($county);
        $nDis = sizeof($districts);
        for ($i = 0; $i < $nDis; $i++) {
            $url = basename(__FILE__) . "?county=$county&district=" . ($i + 1);
            echo "<a href='$url'>";
            echo "<h2>" . sprintf("%02d", $i + 1) . " 選區：$districts[$i]</h2>";
            echo "</a>";
        }
    }

}

?>
</body>
</html>