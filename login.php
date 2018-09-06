<?php
require_once 'lib/user.php';
if (is_admin()) {
    header("Location: admin.php");
}
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
    <title>管理員登入</title>
</head>
<body>
    <div class="container"><form action="admin.php" method="POST">
        <div class="form-row">
            <div class="col-md-6 form-group">
                <label>帳號</label>
                <input type="text" id="username" name="username" class="form-control"/>
            </div>
            <div class="col-md-6 form-group">
                <label>密碼</label>
                <input type="password" id="password" name="password" class="form-control"/>
            </div>
        </div>
        <div class="form-inline">
            <input type="submit" value="登入" class="btn btn-primary form-control col-2"/>
        </div>
    </form></div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>;
