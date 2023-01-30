<?php

function extend_date($length, $start = null) {

    if (is_null($start)): $start = date_create(); endif;

    $end = date_add($start, date_interval_create_from_date_string($length));
    return $end;

}

if (!function_exists("str_contains")):

    function str_contains($haystack, $needle) {
    
        if (@strpos($haystack, $needle) !== false): return true;
        else: return false; endif;
    
    }

endif;

function user_rights($profile, $role) {

    if (isset($profile->profile)):
    
        user_rights($profile->profile, $role);
    
    else:
    
        if (isset($profile->role)):
        
            if ($profile->role == $role): return true; endif;
        
        endif;
    
    endif;

    return false;

}

function get_user_ip() { }
function get_user_agent() { }
function get_user_browser() { }
function get_user_type() { }
function get_user_timezone() { }

function load_from_json($target, $path = ROOT) {

    $file = "{$target}.json";

    if (!file_exists($path . $file)): return false; endif;
    $json = file_get_contents($path . $file); unset($file);

    $data = json_decode($json); unset($json);

    return $data;

}

function load_from_table($name, $options = "") {

    global $db;

    $sql = "SELECT * FROM `{$name}`{$options}";
    $query = $db->query($sql); unset($sql);

    $table = [];

    if (!$query): return $table; endif;
    while ($data = $query->fetch_object()):
    
        array_push($table, $data); unset($data);
    
    endwhile; unset($query);

    return $table;

}

function drop_from_table($name, $conditions = "") {

    global $db;

    $sql = "DELETE FROM `{$name}` WHERE `{$conditions}`";
    $query = $db->query($sql); unset($sql);

    return $query;

}

function load_distinct_from_table($name, $options = "") {

    global $db;

    $sql = "SELECT DISTINCT() * FROM `{$name}`{$options}";
    # var_dump($sql); exit;
    $query = $db->query($sql); unset($sql);

    $table = [];

    if (!$query): return $table; endif;
    while ($data = $query->fetch_object()):
    
        array_push($table, $data); unset($data);
    
    endwhile; unset($query);

     return $table;

}

function reset_object(&$var) {

    $var = (object) array();

    return $var;

}

function nav_group_list($cols, $array = [], $path = null) {

    $table = [];

    foreach ($cols as $col):
    
        $row = nav_list($col, $array, $path);
        array_push($table, $row);
    
    endforeach; unset($col);

    return $table;

}

function nav_list($rows, $array = [], $path = null) {

    $table = [];
    foreach ($rows as $target):
    
        $row = array_query($array, "{$path}/{$target}", "href");
        array_push($table, $row); unset($row);
    
    endforeach; unset($target);

    return $table;

}

/* function elapsed_time_greater_than($statement, $stamp) {

    list($length_a, $size_a) = explode(" ", $statement);
    list($length_b, $size_b) = explode(" ", $stamp);

    if ($size_a == $size_b):
    
        if ($length_a > $length_b): return false;
        else: return true; endif;
    
    else:
    
        $result = null;
        $found = false;

        var_dump($statement);
        var_dump($stamp);
        exit;

        while (!$found):
        
            if ($size_a == "seconds"):
            
                $lower = [];
                $higher = ["minutes", "hours", "days", "weeks", "months", "years"];

                if (in_array($size_b, $lower)): $found = true; $result = false;
                elseif (in_array($size_b, $higher)): $found = true; $result = true;
                else: return null; endif;
            
            elseif ($size_a == "minutes"):
            
                $lower = ["seconds"];
                $higher = ["hours", "days", "weeks", "months", "years"];

                if (in_array($size_b, $lower)): $found = true; $result = false;
                elseif (in_array($size_b, $higher)): $found = true; $result = true;
                else: return null; endif;
            
            elseif ($size_a == "hours"):
            
                $lower = ["seconds", "minutes"];
                $higher = ["days", "weeks", "months", "years"];

                if (in_array($size_b, $lower)): $found = true; $result = false;
                elseif (in_array($size_b, $higher)): $found = true; $result = true;
                else: return null; endif;
            
            elseif ($size_a == "days"):
            
                $lower = ["seconds", "minutes", "hours"];
                $higher = ["weeks", "months", "years"];

                if (in_array($size_b, $lower)): $found = true; $result = false;
                elseif (in_array($size_b, $higher)): $found = true; $result = true;
                else: return null; endif;
            
            elseif ($size_a == "weeks"):
            
                $lower = ["seconds", "minutes", "hours", "days"];
                $higher = ["months", "years"];

                if (in_array($size_b, $lower)): $found = true; $result = false;
                elseif (in_array($size_b, $higher)): $found = true; $result = true;
                else: return null; endif;
            
            elseif ($size_a == "months"):
            
                $lower = ["seconds", "minutes", "hours", "days", "weeks"];
                $higher = ["years"];

                if (in_array($size_b, $lower)): $found = true; $result = false;
                elseif (in_array($size_b, $higher)): $found = true; $result = true;
                else: return null; endif;
            
            elseif ($size_a == "years"):
            
                $lower = ["seconds", "minutes", "hours", "days", "weeks", "months"];
                $higher = [];

                if (in_array($size_b, $lower)): $found = true; $result = false;
                elseif (in_array($size_b, $higher)): $found = true; $result = true;
                else: return null; endif;
            
            endif;
        
        endwhile;

        return $result;
    
    endif;

} */

function time_remaining($stamp) {

    return time() - $stamp;

}

function time_between($start, $end, $format = "%R%a days") {

    if (is_numeric($start)): $start = date("Y-m-d H:i:s"); endif;
    if (is_numeric($end)): $end = date("Y-m-d H:i:s"); endif;

    $start = new DateTime($start);
    $end = new DateTime($end);

    $interval = $start->diff($end);
    return $interval->format($format);

}

function decompound_time($elapsed_stamp, $length = null) {

    $remaining_time = abs($elapsed_stamp);

    if (is_null($length)):
    
        while (!isset($result)):
        
            $lengths = [60, (60 * 60), (60 * 60 * 24), (60 * 60 * 24 * 7), (60 * 60 * 24 * 30), (60 * 60 * 24 * 7 * 52)];
            list($minute, $hour, $day, $week, $month, $year) = $lengths;

            if (($remaining_time / $minute) <= 60):
            
                $result = (object) array();
                $result->message = "less than a minute";
                $result->started = date("g.i a", time() - $remaining_time);
                $result->elapsed = $remaining_time . " seconds";
            
            elseif (($remaining_time / $hour) <= 24):
            
                $result = (object) array();
                $result->message = "less than a day";
                $result->started = date("m/d/y H:i:s", time() - $remaining_time);
                $result->elapsed = round($remaining_time / $hour) . " hours";
            
            elseif (($remaining_time / $day) >= 1):
            
                $result = (object) array();
                $result->message = "less than a week";
                $result->started = date("m/d/y H:i:s", time() - $remaining_time);
                $result->elapsed = round($remaining_time / $day) . " days";
            
            elseif (($remaining_time / $week) >= 1):
            
                $result = (object) array();
                $result->message = "less than a month";
                $result->started = date("m/d/y H:i:s", time() - $remaining_time);
                
                if (($remaining_time / $day) % 7 == 1):
                
                    $result->elapsed = round($remaining_time / $week) . " weeks";
                
                else:
                
                    $result->elapsed = round($remaining_time / $week) . " days";
                
                endif;
            
            elseif (($remaining_time / $month) >= 1):
            
                $result = (object) array();
                $result->message = "less than a month";
                $result->started = time() - $remaining_time;
            
            elseif (($remaining_time / $year) >= 1):
            
                $result = (object) array();
                $result->message = "less than a year";
                $result->started = time() - $remaining_time;
            
            endif;
        
        endwhile;
    
    else:
    
        switch($length):
        
            case "minute": $result = $remaining_time / 60; break;
            case "second": $result = $remaining_time; break;
            case "hour": $result = $remaining_time / (60 * 60); break;
            case "day": $result = $remaining_time / (60 * 60 * 24); break;
            case "week": $result = $remaining_time / (60 * 60 * 24 * 7); break;
            case "month": $result = $remaining_time / (60 * 60 * 24 * 30); break;
            case "year": $result = $remaining_time / (60 * 60 * 24 * 7 * 52); break;
    
            default: break;
        
        endswitch;
    
    endif;

    return $result;

}

function compound_time($minutes, $seconds, $hours, $days, $months, $years) {

    if ($months == 0): $months = 1; endif;
    if ($days == 0): $days = 1; endif;
    if ($years == 0): $years = 1; endif;
    if ($hours == 0): $hours = 1; endif;
    if ($minutes == 0): $minutes = 1; endif;
    if ($seconds == 0): $seconds = 1; endif;

    $time = $years * $months * $days * $hours * $minutes * $seconds;
    return $time;

}

function elapse_yearly($stamp = null, $years = 1) { }
function elapse_monthly($stamp = null, $months = 1) { }
function elapse_weekly($stamp = null) { }
function elapse_daily($stamp = null, $days = 1) { }
function elapse_hourly($stamp = null, $hours = 1) { }
function elapse_half_hour($stamp = null) { }
function elapse_quarter_hour($stamp = null) { }
function elapse_minute($stamp = null, $minutes = 1) { }
function elapse_second($stamp = null, $seconds) { }

function elapse_time($stamp = null, $attributes = []) {

    list($increment, $length) = $attributes;

    $labels = ["month", "months", "day", "days", "week", "weeks", "year", "years",
    "hour", "hours", "minute", "minutes", "second", "seconds"];

    if (array_search($length, $labels) === false): return false; endif;
    if (preg_match("/s$/", $length)): $length = substr($length, 0, -1); endif;

    if (!is_null($stamp)): $remaining_time = time_remaining($stamp); endif;

    switch($length):
    
        case "minute": $compound_time = compound_time($increment, 60, 1, 1, 1, 1); break;
        case "hour": $compound_time = compound_time(60, 60, $increment, 1, 1, 1); break;
        case "day": $compound_time = compound_time(60, 60, 24, $increment, 1, 1); break;
        case "week": $compound_time = compound_time(60, 60, 24, 7, 1, 1); break;
        case "month": $compound_time = compound_time(60, 60, 24, 30, $increment, 1); break;
        case "year": $compound_time = compound_time(60, 60, 24, 30, 12, $increment); break;
        
        default: break;
    
    endswitch;

    if (isset($remaining_time)):
    
        if ($compound_time >= abs($remaining_time)):
        
            $elapsed_time = $compound_time - abs($remaining_time);
        
        else:
        
            $elapsed_time = $remaining_time - $compound_time; // this one is wrong?
            $elapsed_time = abs($compound_time - abs($remaining_time)); // this one is right?

            var_dump($elapsed_time);
        
        endif;

        return $elapsed_time;
    
    else:
    
        return $compound_time;
    
    endif;

}

function drop_token($target, $id = 0) {

    if (isset($_SESSION[$target])):
    
        unset($_SESSION[$target]);
    
    endif;

    return false;

}

function set_token($target, $code) {

    $_SESSION[$target] = encode_token($code);

}

function start_token($target, $id = 0) {

    if (!isset($_SESSION[$target])):
    
        $_SESSION[$target] = encode_token(generate_code()); 
        return $_SESSION[$target];
    
    endif;

    if ($id > 0):
    
        $token = $_SESSION[$target];
        $_SESSION[$target] = $id;

        return json_decode('{
            "id": ' . $id . ',
            "target": "' . $target . '",
            "token": "' . $token . '"
        }');
    
    endif;

    return false;

}

function is_encoded($token) {

    if (strlen($token) == 7):
    
        return true;
    
    else:
    
        return false;
    
    endif;

}

function get_token($target) {

    if (isset($_SESSION[$target])):
    
        return $_SESSION[$target];
    
    endif;

    return false;

}

function generate_code($min = 5, $max = 10, $pad = true, $length = 7) {

    $code = rand(0, pow($max, $min) - 1);
    if ($pad): $code = str_pad($code, $length, "0", STR_PAD_LEFT); endif;

    return $code;

}

function encode_token($code) { return convert_uuencode($code); }

function decode_token($string) {

    if ($string !== false): return convert_uudecode($string); endif;

    return false;
}

function redirect2page($url = null) {

	if (is_null($url)): $url = BASE . "?page=home"; endif;
	
    if (strpos($url, "?page=") === false): $url = BASE . "?page=" . $url; endif;
	
    @header("Location: " . $url);

    print '<script type="text/javascript">document.location = "' . $url . '";</script>';

    # print '<meta content="location: ' . $url . ';" />';
    exit;

}

function redirect2web($url) {

	header("Location: " . $url);
	exit;

}

function array_query_id($array, $target, $field = "target") {
	
	$column = array_column($array, $field);
	$id = array_search($target, $column); unset($column);

	return ($id !== false) ? $id : false;

}

function array_query($array, $target, $field = "target") {

	$id = array_query_id($array, $target, $field);
	return ($id !== false) ? $array[$id] : false;

}

function array_query_all($array, $target, $field = "target") {

    $table = [];

    foreach ($array as $row):
    
        if (str_contains($row->$field, $target)): array_push($table, $row); endif;
    
    endforeach;

    return $table;

}

function array_query_table($array, $target, $field = "target") {

    foreach ($array as $id => $data):
    
        $query = array_query($data, $target, $field);
        if ($query !== false): return $query; endif;
    
    endforeach; unset($id, $data);

    return false;

}

function count_page_segments() {

    $count = 0;

    if (isset($_GET['page'])):
    
        $string = $_GET['page'];
        $array = explode("/", $string);

        $count = count($array);
    
    endif;
    
    return $count;

}

function page_segment($target = null, $id = null) {

    if (!isset($_GET['page'])): return false; endif;
    if ($_GET['page'] == ""): return false; endif;

    $string = $_GET['page'];

    if ($target == $string): return $string; endif;

    $array = explode("/", $string);

    if (is_numeric($target)):
    
        $target -= 1;
        if (isset($array[$target])):
        
            if ($id === true): $target += 1; return implode("/", array_slice($array, 0, $target));
            else: return $array[$target]; endif;
        
        endif;
    
    else:
    
        if (strpos($string, $target) !== false): return true; endif;
    
    endif;

    return false;

}

function admin_segment($id = null) {

    if (!isset($_GET['admin'])): return false; endif;
    if ($_GET['admin'] == ""): return true; endif;

    $id -= 1;
    $string = $_GET['admin'];
    $array = explode("/", $string);

    if (isset($array[$id])): return $array[$id];
    else: return false; endif;

}

?>
