<?php

$app_kindle_url = isset($modSettings['tp_kindle_url']) ? $modSettings['tp_kindle_url'] : '';
$app_android_url = isset($modSettings['tp_android_url']) ? $modSettings['tp_android_url'] : '';
$app_ios_id = isset($modSettings['tp_app_ios_id']) ? $modSettings['tp_app_ios_id'] : '';;

$app_banner_message = isset($modSettings['tp_app_banner_msg']) ? $modSettings['tp_app_banner_msg'] : '';
$app_banner_message = preg_replace('/\r\n/','<br>',$app_banner_message);

$is_mobile_skin = false;
$app_location_url = get_scheme_url();
$tapatalk_dir_url = $boardurl. '/mobiquo';
$app_forum_name = !empty($GLOBALS['mbname'])? $GLOBALS['mbname'] : '';;


if (file_exists($tapatalk_dir_url . '/smartbanner/head.inc.php'))
    include($tapatalk_dir_url . '/smartbanner/head.inc.php');
    
$context['html_headers'] .= $app_head_include;

if (!isset($context['tapatalk_body_hook']))
    $context['tapatalk_body_hook'] = '';

$context['tapatalk_body_hook'] .= '
<!-- Tapatalk Detect body start -->
<style type="text/css">
.ui-mobile [data-role="page"], .ui-mobile [data-role="dialog"], .ui-page 
{
top:auto;
}
</style>
<script type="text/javascript">tapatalkDetect()</script>
<!-- Tapatalk Detect banner body end -->

';

function get_scheme_url()
{
    global $boardurl, $user_info, $context;

    $baseUrl = $boardurl;
    $baseUrl = preg_replace('/https?:\/\//', 'tapatalk://', $baseUrl);

    $location = 'index';
    $other_info = array();
    //is action? 'pm', 'profile', 'login2', 'login', 'search2'
    if(isset($_GET['action']) && !empty($_GET['action']))
    {
        if($_GET['action'] == 'pm')
            $location = 'message';
        else if($_GET['action'] == 'profile')
        {
            $location = 'profile';
            if(isset($_GET['u']) && !empty($_GET['u']))
                $other_info[] = 'uid='.$_GET['u'];
            else if(!empty($user_info['id']))
                $other_info[] = 'uid='.$user_info['id'];
        }
        else if($_GET['action'] == 'login2' || $_GET['action'] == 'login')
            $location = 'login';
        else if($_GET['action'] == 'search2')
            $location = 'search';
        else if($_GET['action'] == 'who')
            $location = 'online';
    }
    //Query string topic=36.msg123 board=1.0 topic=36.0
    else
    {
        if(!empty($_SERVER['QUERY_STRING']))
        {
            $extra_info = $_SERVER['QUERY_STRING'];
            if(strpos($extra_info, 'board') !== false)
            {
                $location = 'forum';
                if(isset($context['current_board']) && !empty($context['current_board']))
                    $other_info[] = 'fid='.$context['current_board'];
            }
            else if(strpos($extra_info, 'topic') !== false)
            {
                if(isset($context['current_board']) && !empty($context['current_board']))
                    $other_info[] = 'fid='.$context['current_board'];
                if(isset($context['current_topic']) && !empty($context['current_topic']))
                    $other_info[] = 'tid='.$context['current_topic'];
                if(strpos($extra_info, 'msg') !== false)
                {
                    $location = 'post';
                    $matches = preg_split('/msg/', $extra_info);
                    if(isset($matches[1]))
                        $other_info[] = 'pid='.$matches[1];
                }
                else
                {
                    $location = 'topic';
                }
            }
        }
    }
    $other_info_str = implode('&', $other_info);
    $scheme_url = $baseUrl . '/' . (!empty($user_info['id']) ? '?user_id='.$user_info['id'].'&' : '?') . 'location='.$location.(!empty($other_info_str) ? '&'.$other_info_str : '');
    return $scheme_url;
}