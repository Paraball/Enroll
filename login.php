<?php

require_once 'db.php';
session_start();

function is_admin($username, $password)
{
    global $conn;
    $ep = hash('sha256', $password);
    $sql = "SELECT password FROM users "
        . "WHERE username='$username' "
        . "LIMIT 1;";
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)) {
        return !strcasecmp($row['password'], $ep);
    }
    return false;
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    if (is_admin($_POST['username'], $_POST['password'])) {
        $_SESSION['user'] = 'admin';
        header('Location: admin.php');
    }
}
?>

<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8">
    <title>登入</title>
</head>
<body>
    <form action="login.php" method="POST">
        <p>帳號: <input type="text" name="username" maxlength="16" /></p>
        <p>密碼: <input type="password" name="password" maxlength="64" /></p>
        <p><input type="submit" value="登入" /></p>
    </form>
</body>
</html>
