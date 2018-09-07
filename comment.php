<?php
require_once 'lib/db.php';
require_once 'lib/pvdup.php';

if (!isset($_GET['candidate_id'])) {
    die;
}
$res = query(
    "SELECT candidate_name, county, district FROM candidates
     WHERE candidate_id='$_GET[candidate_id]'
     LIMIT 1"
);
if (!mysqli_num_rows($res)) {
    die;
}
$row = mysqli_fetch_assoc($res);
$county = $row['county'];
$district = $row['district'];

$candidate_name = $row['candidate_name'];

$res = query("SELECT district_name FROM districts WHERE county='$county' AND district=$district LIMIT 1");
$row = mysqli_fetch_assoc($res);
if ($district < 10) {
    $district = '0' . $district;
}
$district = "【 $district 】$row[district_name]";
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/comment.css">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <title>提交留言</title>
</head>
<body>
    <div class="container">
        <form action="submit.php" method="POST">
            <div class="row">
                <div class="col-sm-2 form-group">
                    <label for="county">縣市</label>
                    <select id="county" class="form-control" disabled="true">
                        <option><?echo $county; ?></option>
                    </select>
                </div>
                <div class="col-sm-6 form-group">
                    <label for="district">選區</label>
                    <select id="district" class="form-control" disabled="true">
                    <option><?echo $district; ?></option>
                    </select>
                </div>
                <div class="col-sm-2 form-group">
                    <label for="candidate_name">擬參選人</label>
                    <select id="candidate_name" class="form-control" disabled="true">
                    <option><?echo $candidate_name; ?></option>
                    </select>
                </div>
                <div class="col-sm-2 form-group">
                    <label for="cand_id">ID</label>
                    <input type="text" id="cand_id" disabled="true" class="form-control" value="<?echo "#" . $_GET['candidate_id']; ?>" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>已驗證的訊息</label>
                    <textarea name="ev_cont" id="ev-cont" rows="10" class="form-control" placeholder="已有佐證的訊息輸入於此，並請在下方提供佐證資料。若無則留空。"></textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label>未驗證的訊息</label>
                    <textarea name="cont" id="cont" rows="10" class="form-control" placeholder="尚無佐證的訊息輸入於此。若無則留空。"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 form-group">
                    <label>給管理員的悄悄話</label>
                    <textarea name="message" rows="2" class="form-control" placeholder="給管理員的悄悄話輸入於此。若無則留空。"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 form-group">
                    <label for="email">電子郵件</label>
                    <input name="au_email" type="email" id="email" name="email" class="form-control" placeholder="您的個資我們絕對保密。" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 form-group">
                    <label>防機器人驗證</label>
                    <div class="g-recaptcha" data-sitekey="6Lff5WkUAAAAAC-tsW7S0CtD4BD35DD4d41Oi92i" data-callback="onRecaptcha" ></div>
                </div>
            </div>
            <input type="hidden" name="pvdup" value="<?register_pvdup('cmt');?>" />
            <input type="hidden" name="candidate_id" value="<?echo $_GET['candidate_id']; ?>" />
            <div class="row">
                <div class="col-sm-12 form-group">
                    <label>完成</label>
                    <input id='submit' type="submit" value="請提供有佐證「或」無佐證的訊息" class="form-control btn btn-primary" disabled="true" />
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="js/comment.js"></script>
</body>
</html>
