<?php
require_once 'db.php';

class Post
{
    public $author;
    public $time;
    public $content;

    public function __construct($author, $time, $content)
    {
        $this->author = $author;
        $this->time = $time;
        $this->content = $content;
    }
}

function get_posts($id, $page)
{
    $post_per_page = 10;
    $from = ($page - 1) * $post_per_page;
    global $conn;
    $sql = "SELECT author, content, time FROM posts "
        . "WHERE candidate_id='$id' AND status=1 "
        . "ORDER BY time DESC "
        . "LIMIT $from, $post_per_page";
    $result = $conn->query($sql);
    $posts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = new Post($row["author"], $row["time"], $row["content"]);
    }
    return $posts;
}

function save_post($candidate_id, $author, $content)
{
    global $conn;
    $sql = "INSERT INTO posts (candidate_id, author, content) "
        . "VALUES ('$candidate_id', '$author', '$content')";
    return $conn->query($sql);
}
