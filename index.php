<?php
require_once 'lib/db.php';

//No cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        body{
            padding-top: 60px;
        }
    </style>
    <title>首頁</title>
</head>
<body>
    <div class="container">
        <form action="board.php" method="GET">
            <div class="form-row">
                <div class="col-sm-6 form-group">
                    <label for="county">縣市</label>
                    <select class="form-control" id="county">
<?
$res = query('SELECT DISTINCT county from districts');
$df_county = isset($_COOKIE['county']) ? $_COOKIE['county'] : null;
while ($row = mysqli_fetch_assoc($res)) {
    if ($df_county === null) {
        $df_county = $row['county'];
    }
    if ($df_county == $row['county']) {
        echo "<option value='$row[county]' selected>$row[county]</option>";
    } else {
        echo "<option value='$row[county]'>$row[county]</option>";
    }
}
?>
                    </select>
                </div>
                <div class="col-sm-6 form-group">
                    <label for="district">選區</label>
                    <select id="district" class="form-control">
<?
$res = query("SELECT district_name from districts WHERE county='$df_county'");
$i = 1;
$df_district = isset($_COOKIE['district']) ? $_COOKIE['district'] : null;
while ($row = mysqli_fetch_assoc($res)) {
    $dname = "【" . ($i < 10 ? "0$i" : $i) . "】" . $row['district_name'];
    if ($df_district === null) {
        $df_district = $i;
    }
    if ($df_district == $i) {
        echo "<option value='$i' selected>$dname</option>";
    } else {
        echo "<option value='$i'>$dname</option>";
    }
    $i++;
}
?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-group">
                    <label for="district">擬參選人</label>
                    <select name="candidate_id" id="cccd" class="form-control">
<?
$res = query("SELECT candidate_id, candidate_name from candidates WHERE county='$df_county' AND district=$df_district");
$df_candidate = isset($_COOKIE['candidate_id']) ? $_COOKIE['candidate_id'] : null;
while ($row = mysqli_fetch_assoc($res)) {
    if ($df_candidate == $row['candidate_id']) {
        echo "<option value='$row[candidate_id]' selected>$row[candidate_name]</option>";
    } else {
        echo "<option value='$row[candidate_id]'>$row[candidate_name]</option>";
    }
}
?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <input id="submit" class="btn btn-primary" type="submit" value="查詢議員"/>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="js/index.js"></script>
</body>
</html>
