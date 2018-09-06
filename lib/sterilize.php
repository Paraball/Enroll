<?php

function sterilize_get($arg, $min = 0, $default = 0, $max = 65535)
{
    if (!isset($_GET[$arg])) {
        return $default;
    }
    if (!is_numeric($_GET[$arg])) {
        return $min;
    }
    return min(max($_GET[$arg], $min), $max);
}

function txt_to_sql_content($txt)
{
    if (empty($txt)) {
        return null;
    }
    $str = "";
    $txt = preg_split('/\n|\r\n?/', $txt);
    foreach ($txt as $t) {
        $t = str_replace("　", "", $t);
        if ($t === "" || ctype_space($t)) {
            continue;
        }
        $str .= "<p>" . htmlentities($t) . "</p>";
    }
    return $str ? $str : null;
}

function html_to_txt($html){
    return str_replace("</p>", "", str_replace("<p>", "\n\n", $html));
}