<?php
require_once 'candidate.php';
require_once 'post.php';
require_once 'sterilize.php';

session_start();
$r = rand();
$_SESSION['prevent_repeat_saving'][$r] = '1';

//A candidate's page
if (isset($_GET['id'])) {

    $cand = Candidate::get_candidate($_GET['id']);
    if (!$cand) {
        header("Location: " . basename(__FILE__));
        die;
    }
    $title = $cand->name;
    $status = 'candidate';

}

//A district list
else if (isset($_GET['county']) && isset($_GET['district'])) {

    if (!has_district($_GET['county'], $_GET['district'])) {
        header("Location: " . basename(__FILE__));
        die;
    }
    $title = $_GET['county'] . " " . sprintf("%02d", $_GET['district']) . " 選區";
    $status = 'district';

}

//Sterilize invalid arguments
else if (!empty($_GET)) {
    header("Location: " . basename(__FILE__));
    die;
}

//Homepage
else {
    $title = "市議員選舉";
    $status = 'home';
}

?>

<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="css/candidate.css">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="js/form-validate.js"></script>
    <title><?php echo $title; ?></title>
</head>
<body>

<?php

//A candidate's page
if ($status === 'candidate') {

    $posts = Post::get_posts($_GET['id'], sterilize_get('page', 1));

    //If no posts
    if (empty($posts)) {
        echo "<p>目前沒有訊息。</p>";
    }

    //If any posts exist
    else {
        foreach ($posts as &$p) {
            echo "<p>$p->cont</p>";
            echo "<p>$p->ev_cont</p>";
            echo "<p>$p->time</p><hr>";
        }
    }
    ?>

    <form action="submit.php" method="POST">
        <h2>留下訊息</h2>
        <input name="candidate_id" type="hidden" value="<?php echo $_GET['id']; ?>" />
        <input name="prevent_repeat_saving" type="hidden" value="<?php echo $r; ?>" />
        <h3>請在這裡輸入有佐證的訊息：</h3>
        <p><textarea class="content" id="ev_cont" name="ev_cont" maxlength="5000" ></textarea></p>
        <h3>請在這裡輸入未佐證的訊息：</h3>
        <p><textarea class="content" id="cont" name="cont" maxlength="5000" ></textarea></p>
        <h3>若您有訊息要留給管理員，請在此輸入：</h3>
        <p><textarea class="content" id="message" name="message" maxlength="5000" ></textarea></p>
        <h3>您的信箱：</h3>
        <p><input id="au_email" name="au_email" type="email" maxlength="30" /></p>
        <div class="g-recaptcha" data-sitekey="6Lff5WkUAAAAAC-tsW7S0CtD4BD35DD4d41Oi92i"></div>
        <input id="submit" type="submit" value="提交" /><span id="errm"></span></p>
    </form>

<?php
}

//A district list
else if ($status === 'district') {

    $candidates = Candidate::get_candidates($_GET['county'], $_GET['district']);
    $dis_name = get_district_name($_GET['county'], $_GET['district']);
    echo "<h1>$title：$dis_name</h1>";

    //If no candidates
    if (empty($candidates)) {
        echo "<p>目前沒有表態參選的議員。</p>";
    }

    //If any candidates exist
    else {

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
}

//Homepage
else if ($status === 'home') {

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

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>