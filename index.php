<?php
require_once 'lib/candidate.php';
$counties = get_counties();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/index.css">
    <title>首頁</title>
</head>
<body>
    <div class="container">
        <div class="inner">

            <div>
                <h2>縣市議員選舉</h2>
                <form action="board.php" method="GET">
                    <div class="form-group">
                        <label for="county">縣市</label>
                        <select class="form-control" id="county">
                            <?php foreach ($counties as $c) {
                                echo "<option value='$c'>$c</option>";
                                $districts[$c] = get_districts($c);
                            }?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="district">選區</label>
                        <select id="district" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="district">擬參選人</label>
                        <select name="candidate_id" id="cccd" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit" />查詢議員</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="js/index.js"></script>
</body>
</html>
