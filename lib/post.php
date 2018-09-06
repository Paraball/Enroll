<?php
require_once 'db.php';
require_once 'user.php';

function echo_post($post_id, $array, $ev_cont, $cont, $links, $msg = null)
{
    ?>

<div class="board row">
    <div class="col-sm-3 msg">
        <?foreach ($array as $k => &$v) {?>
            <p><span class="meta-name badge badge-info"><?echo $k; ?></span><span class="meta-cont"><?echo $v; ?></span></p>
        <?}?>
        <p class="links">
            <?foreach ($links as $k => &$v) {?>
                <a href="#" class="<?echo $v; ?>" post-id="<?echo $post_id; ?>"><?echo $k; ?></a>
            <?}?>
        </p>
    </div>
    <div class="cont col-sm-9">
        <?if ($ev_cont) {?>
            <div class="ev">
                <p><span class="meta-name badge badge-success">已驗證的內容</span></p>
                <?echo $ev_cont; ?>
            </div>
        <?}?>
        <?if ($cont) {?>
            <div class="nev">
                <p><span class="meta-name badge badge-danger">尚未驗證的內容</span></p>
                <?echo $cont; ?>
            </div>
        <?}?>
        <?if ($msg && is_admin()) {?>
            <div class="secret">
                <p><span class="meta-name badge badge-secondary">留言者的秘密訊息</span></p>
                <?echo $msg; ?>
            </div>
        <?}?>
    </div>
</div>

<?
}

function view_posts($candidate, $page = 1, $order = 1, $evident = 2, $ppp = 6)
{
    $sql = "SELECT post_id, evident_content, inevident_content, publish FROM posts WHERE candidate_id='$candidate' AND post_status=1";
    switch ($evident) {
        case 0:
            $sql .= " AND inevident_content IS NOT NULL";
            break;
        case 1:
            $sql .= " AND inevident_content IS NULL";
            break;
        case 2:
            break;
        default:
            die;
    }
    switch ($order) {
        case 0:
            $sql .= " ORDER BY post_id ASC";
            break;
        case 1:
            $sql .= " ORDER BY post_id DESC";
            break;
        default:
            die;
    }
    $limit = ($page - 1) * $ppp;
    $sql .= " LIMIT $limit, $ppp";
    $res = query($sql);
    if(!mysqli_num_rows($res)){
        echo "<p class='empty'>查無文章</p>";
        return;
    }
    while ($row = mysqli_fetch_assoc($res)) {
        $time = strtotime($row['publish']);
        $array = array(
            'DATE' => date('Y-m-d', $time),
            'TIME' => date('H:i:s', $time),
            'POST' => '#' . str_pad($row['post_id'], 6, '0', STR_PAD_LEFT),
        );
        $links = array('回報' => 'report');
        echo_post($row['post_id'], $array, $row['evident_content'], $row['inevident_content'], $links);
    }
}

function manage_posts($candidate, $page = 1, $order = 1, $evident = 2, $status = 1, $ppp = 6)
{
    if (!is_admin()) {
        die;
    }
    $sql = "SELECT * FROM posts WHERE candidate_id='$candidate'";
    switch ($evident) {
        case '0':
            $sql .= " AND inevident_content IS NOT NULL";
            break;
        case '1':
            $sql .= " AND inevident_content IS NULL";
            break;
        case '2':
            break;
        default:
            die;
    }
    if ($status != 9) {
        $sql .= " AND post_status=$status";
    }
    switch ($order) {
        case '0':
            $sql .= " ORDER BY post_id ASC";
            break;
        case '1':
            $sql .= " ORDER BY post_id DESC";
            break;
        default:die;
    }
    $limit = ($page - 1) * $ppp;
    $sql .= " LIMIT $limit, $ppp";
    $res = query($sql);
    if(!mysqli_num_rows($res)){
        echo "<p class='empty'>查無文章</p>";
        return;
    }
    while ($row = mysqli_fetch_assoc($res)) {
        $time = strtotime($row['publish']);
        $array = array(
            'DATE' => date('Y-m-d', $time),
            'TIME' => date('H:i:s', $time),
            'POST' => '#' . str_pad($row['post_id'], 6, '0', STR_PAD_LEFT),
            'AUTHOR' => $row['author_email'],
        );
        switch ($row['post_status']) {
            case '0':
                $array['STATUS'] = '待審核';
                $links = array(
                    '發布' => 'publish',
                    '編輯' => 'edit',
                    '刪除' => 'delete',
                );
                break;
            case '1':
                $array['STATUS'] = '已發布';
                $links = array(
                    '取消發布' => 'cancel',
                    '編輯' => 'edit',
                    '刪除' => 'delete',
                );
                break;
            case '-1':
                $array['STATUS'] = '已刪除';
                $links = array(
                    '取消刪除' => 'cancel',
                    '編輯' => 'edit',
                    '永久刪除' => 'die',
                );
                break;
        }

        echo_post($row['post_id'], $array, $row['evident_content'], $row['inevident_content'], $links, $row['secret_message']);
    }
}

function max_page($evident = 2, $status = 0, $where = array(), $ppp = 6)
{
    if (isset($where['candidate_id'])) {
        $sql = "SELECT COUNT(*) AS total FROM posts WHERE candidate_id='$where[candidate_id]'";
    } else if (isset($where['county'])) {
        $sql = "SELECT COUNT(*) AS total FROM posts INNER JOIN candidates using (candidate_id) WHERE county='$where[county]'";
        if (isset($where['district']) && is_numeric($where['district'])) {
            $sql .= " AND district=$where[district]";
        }
    } else {
        $sql = "SELECT COUNT(*) AS total FROM posts WHERE TRUE";
    }
    switch ($status) {
        case '3':
            break;
        case '2':
            $sql .= " AND (post_status=0 OR post_status=-1)";
            break;
        case '1':
            $sql .= " AND post_status=1";
            break;
        case '0':
            $sql .= " AND post_status=0";
            break;
        case '-1':
            $sql .= " AND post_status=-1";
            break;
        default:
            die;
    }
    switch ($evident) {
        case '2';
            break;
        case '1':
            $sql .= " AND evident_content IS NOT NULL";
            break;
        case '0':
            $sql .= " AND inevident_content IS NOT NULL";
            break;
        default:
            die;
    }
    $res = query($sql);
    $row = mysqli_fetch_assoc($res);
    $count = $row['total'];
    if ($count == 0) {
        return 1;
    }
    return ceil($count / $ppp);
}

function permit_posts($page = 1, $order = 1, $evident = 2, $status = 0, $where = array(), $ppp = 20)
{
    if (!is_admin()) {
        die;
    }
    $sql = "SELECT * FROM ((posts INNER JOIN candidates USING (candidate_id)) INNER JOIN districts USING (county, district))";
    if (isset($where['candidate_id'])) {
        $sql .= " WHERE candidate_id=$where[candidate_id]";
    } else if (isset($where['county'])) {
        $sql .= " WHERE county='$where[county]'";
        if (isset($where['district'])) {
            $sql .= " AND district=$where[district]";
        }
    } else {
        $sql .= " WHERE TRUE";
    }
    switch ($evident) {
        case 0:
            $sql .= " AND inevident_content IS NOT NULL";
            break;
        case 1:
            $sql .= " AND inevident_content IS NULL";
            break;
        case 2:
            break;
        default:
            die;
    }
    switch ($status) {
        case -1:
            $sql .= " AND post_status=-1";
            break;
        case 0:
            $sql .= " AND post_status=0";
            break;
        case 1:
            $sql .= " AND post_status=1";
            break;
        case 2:
            $sql .= " AND (post_status=0 OR post_status=-1)";
            break;
        case 3:
            break;
        default:die;
    }
    switch ($order) {
        case 0:
            $sql .= " ORDER BY post_id ASC";
            break;
        case 1:
            $sql .= " ORDER BY post_id DESC";
            break;
        default:die;
    }
    $limit = ($page - 1) * $ppp;
    $sql .= " LIMIT $limit, $ppp";

    $res = query($sql);
    if (mysqli_num_rows($res)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $time = strtotime($row['publish']);
            $cand_id = $row['candidate_id'];
            $county = $row['county'];
            $district = $row['district'];
            $district = "【" . ($district < 10 ? "0$district" : $district) . "】$row[district_name]";
            $cand_name = $row['candidate_name'];
            $array = array(
                'CAND' => $cand_name,
                'COUNTY' => $county,
                'DIST' => $district,
                'DATE' => date('Y-m-d', $time),
                'TIME' => date('H:i:s', $time),
                'POST' => '#' . str_pad($row['post_id'], 6, '0', STR_PAD_LEFT),
                'AUTHOR' => $row['author_email'],
            );
            switch ($row['post_status']) {
                case 0:
                    $array['STATUS'] = '待審核';
                    $links = array(
                        '發布' => 'publish',
                        '編輯' => 'edit',
                        '刪除' => 'delete',
                    );
                    break;
                case 1:
                    $array['STATUS'] = '已發布';
                    $links = array(
                        '取消發布' => 'cancel',
                        '編輯' => 'edit',
                        '刪除' => 'delete',
                    );
                    break;
                case -1:
                    $array['STATUS'] = '已刪除';
                    $links = array(
                        '取消刪除' => 'cancel',
                        '編輯' => 'edit',
                        '永久刪除' => 'die',
                    );
                    break;
            }

            echo_post(
                $row['post_id'],
                $array,
                $row['evident_content'],
                $row['inevident_content'],
                $links,
                $row['secret_message']
            );
        }
    }

    //No posts
    else {
        echo "<p class='empty'>查無文章</p>";
    }
}