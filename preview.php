<?
require_once 'lib/sterilize.php';

if(!isset($_POST['content'])){
    die;
}

echo txt_to_sql_content($_POST['content']);