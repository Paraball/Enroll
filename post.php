<?php
require_once 'db.php';

class Post
{
    public $post_id;
    public $cand_id;
    public $au_email;
    public $time;
    public $ev_cont;
    public $cont;
    public $msg;

    private static function null_check(&$str)
    {
        if (!$str) {
            $str = "NULL";
            return;
        }
        $str = rtrim($str);
        $str = $str ? "'$str'" : "NULL";
    }

    public function __construct($post_id, $cand_id, $au_email, $time, $ev_cont = null, $cont = null, $msg = null)
    {
        $this->post_id = $post_id;
        $this->cand_id = $cand_id;
        $this->au_email = $au_email;
        $this->time = $time;
        $this->ev_cont = $ev_cont;
        $this->cont = $cont;
        $this->msg = $msg;
    }

    public static function get_posts($cand_id, $page = 1, $status = 1, $post_per_page = 10)
    {
        $from = ($page - 1) * $post_per_page;
        $sql = "SELECT evident_content, content, time, message, author_email, post_id FROM posts "
            . "WHERE candidate_id='$cand_id' AND status=$status "
            . "ORDER BY time DESC "
            . "LIMIT $from, $post_per_page";
        $result = query($sql);
        $posts = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = new Post($row['post_id'], $cand_id, $row["author_email"], $row["time"], $row['evident_content'], $row["content"], $row['message']);
        }
        return $posts;
    }

    public static function save_post($cand_id, $au_email, $ev_cont, $cont, $msg, $status = 0)
    {
        Post::null_check($ev_cont);
        Post::null_check($cont);
        Post::null_check($msg);
        $sql = "INSERT INTO posts (candidate_id, author_email, evident_content, content, message, status) "
            . "VALUES ('$cand_id', '$au_email', $ev_cont, $cont, $msg, $status)";
        return query($sql);
    }
}
