<?php

error_reporting(-1);
ini_set('diplay_errors', 1);
define('IN_MOBIQUO', 1);
if(isset($_GET['allowAccess']))
{
    echo "yes";
    exit;
}

if (isset($_GET['checkip']))
{
    print do_post_request(array('ip' => 1) , true);
}
else
{
    
//    print_r($GLOBALS);
    $output = 'Tapatalk Push Notification Status Monitor<br><br>';
    $output .= 'Push notification test: <b>';
    require_once(dirname(dirname(__FILE__)) . '/SSI.php');
    global $modSettings, $smcFunc;
    if(isset($modSettings['tp_push_key']) && !empty($modSettings['tp_push_key']))
    {
        $push_key = $modSettings['tp_push_key'];
        $return_status = do_post_request(array('test' => 1, 'key' => $push_key), true);
        if ($return_status === '1')
            $output .= 'Success</b>';
        else
            $output .= 'Failed</b><br />'.$return_status;
    }
    else
    {
        $output .= 'Failed</b><br /> No push key set in option<br />';
    }
    
    $ip =  do_post_request(array('ip' => 1), true);
    $forum_url =  get_forum_path();

    $table_exist = mobi_table_exist('tapatalk_users') ?'Yes' : 'No';
    $option_status = isset( $modSettings['tp_pushEnabled'] ) ? ($modSettings['tp_pushEnabled'] ? 'On' : 'Off') : 'Unset';

    $output .="<br>Current forum url: ".$forum_url."<br>";
    $output .="Current server IP: ".$ip."<br>";
    $output .="Tapatalk user table existence:".$table_exist."<br>";
    $output .="Push Notification Option status:".$option_status."<br>";
    $output .="<br>
<a href=\"http://tapatalk.com/api/api.php\" target=\"_blank\">Tapatalk API for Universal Forum Access</a> | <a href=\"http://tapatalk.com/build.php\" target=\"_blank\">Build Your Own</a><br>
For more details, please visit <a href=\"http://tapatalk.com\" target=\"_blank\">http://tapatalk.com</a>";
    echo $output;
}

function do_post_request($data, $pushTest = false)
{
    $push_url = 'http://push.tapatalk.com/push.php';
    $push_host = 'push.tapatalk.com';
    $response = 'CURL is disabled and PHP option "allow_url_fopen" is OFF. You can enable CURL or turn on "allow_url_fopen" in php.ini to fix this problem.';

    if (ini_get('allow_url_fopen'))
    {
        if(!$pushTest)
        {
            $fp = fsockopen($push_host, 80, $errno, $errstr, 5);
            
            if(!$fp)
                return false;
                
            $data =  http_build_query($data, '', '&');
            
            fputs($fp, "POST /push.php HTTP/1.1\r\n");
            fputs($fp, "Host: $push_host\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ". strlen($data) ."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $data);
            fclose($fp);
        }
        else
        {
            $params = array('http' => array(
                'method' => 'POST',
                'content' => http_build_query($data, '', '&'),
            ));

            $ctx = stream_context_create($params);
            $timeout = 10;
            $old = ini_set('default_socket_timeout', $timeout);
            $fp = @fopen($push_url, 'rb', false, $ctx);

            if (!$fp) return false;

            ini_set('default_socket_timeout', $old);
            stream_set_timeout($fp, $timeout);
            stream_set_blocking($fp, 0); 
            

            $response = @stream_get_contents($fp);
        }
    }
    elseif (function_exists('curl_init'))
    {
        $ch = curl_init($push_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,1);
        $response = curl_exec($ch);
        curl_close($ch);
    }
    
    return $response;
}

function mobi_table_exist($table_name)
{
    global $smcFunc, $db_prefix, $db_name;
    $tb_prefix = preg_replace('/`'.$db_name.'`./', '', $db_prefix);
    db_extend();
    $tables = $smcFunc['db_list_tables'](false, $tb_prefix . "tapatalk_users");
    return !empty($tables);
}

function get_forum_path()
{
    $path =  '../';

    if (!empty($_SERVER['SCRIPT_NAME']) && !empty($_SERVER['HTTP_HOST']))
    {
        $path = $_SERVER['HTTP_HOST'];
        $path .= dirname(dirname($_SERVER['SCRIPT_NAME']));
    }
    return $path;
}

if (!function_exists('http_build_query')) {

    function http_build_query($data, $prefix = null, $sep = '', $key = '')
    {
        $ret = array();
        foreach ((array )$data as $k => $v) {
            $k = urlencode($k);
            if (is_int($k) && $prefix != null) {
                $k = $prefix . $k;
            }
 
            if (!empty($key)) {
                $k = $key . "[" . $k . "]";
            }
 
            if (is_array($v) || is_object($v)) {
                array_push($ret, http_build_query($v, "", $sep, $k));
            } else {
                array_push($ret, $k . "=" . urlencode($v));
            }
        }
 
        if (empty($sep)) {
            $sep = ini_get("arg_separator.output");
        }
 
        return implode($sep, $ret);
    }
}