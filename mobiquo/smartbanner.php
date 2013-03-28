<?php

$byo_ios_app_id = isset($modSettings['tp_byo_app_id']) ? $modSettings['tp_byo_app_id'] : '';;
$byo_app_name = isset($modSettings['tp_byo_app_name']) ? $modSettings['tp_byo_app_name'] : 'Tapatalk Forum App';;
$byo_app_desc = isset($modSettings['tp_byo_app_desc']) ? $modSettings['tp_byo_app_desc'] : 'App for this forum';;
$byo_app_icon_url = isset($modSettings['tp_byo_app_icon_url']) ? $modSettings['tp_byo_app_icon_url'] : '';;

$app_ios_msg = isset($modSettings['tp_iphone_msg']) ? $modSettings['tp_iphone_msg'] : 'This forum has an app for iPhone and iPod Touch! Click OK to learn more about Tapatalk.';
$app_ios_url = isset($modSettings['tp_iphone_url']) ? $modSettings['tp_iphone_url'] : 'http://itunes.apple.com/us/app/tapatalk-forum-app/id307880732?mt=8';
$app_ios_hd_msg = isset($modSettings['tp_ipad_msg']) ? $modSettings['tp_ipad_msg'] : 'This forum has an app for iPad! Click OK to learn more about Tapatalk.';
$app_ios_hd_url = isset($modSettings['tp_ipad_url']) ? $modSettings['tp_ipad_url'] : 'http://itunes.apple.com/us/app/tapatalk-hd-for-ipad/id481579541?mt=8';
$app_kindle_msg = isset($modSettings['tp_kindle_msg']) ? $modSettings['tp_kindle_msg'] : 'This forum has an app for Kindle Fire! Click OK to learn more about Tapatalk.';
$app_kindle_url = isset($modSettings['tp_kindle_url']) ? $modSettings['tp_kindle_url'] : 'http://www.amazon.com/gp/mas/dl/android?p=com.quoord.tapatalkpro.activity';
$app_kindle_hd_msg = isset($modSettings['tp_kindle_hd_msg']) ? $modSettings['tp_kindle_hd_msg'] : 'This forum has an app for Kindle Fire! Click OK to learn more about Tapatalk.';
$app_kindle_hd_url = isset($modSettings['tp_kindle_hd_url']) ? $modSettings['tp_kindle_hd_url'] : 'http://www.amazon.com/gp/mas/dl/android?p=com.quoord.tapatalkHD';
$app_android_msg = isset($modSettings['tp_android_msg']) ? $modSettings['tp_android_msg'] : 'This forum has an app for Android. Click OK to learn more about Tapatalk.';
$app_android_url = isset($modSettings['tp_android_url']) ? $modSettings['tp_android_url'] : 'market://details?id=com.quoord.tapatalkpro.activity';
$app_android_hd_msg = isset($modSettings['tp_android_hd_msg']) ? $modSettings['tp_android_hd_msg'] : 'This forum has an app for Android. Click OK to learn more about Tapatalk.';
$app_android_hd_url = isset($modSettings['tp_android_hd_url']) ? $modSettings['tp_android_hd_url'] : 'market://details?id=com.quoord.tapatalkpro.activity';

$app_location_url = get_scheme_url();
$tapatalk_dir_name = "mobiquo";
$tapatalk_dir_url = $boardurl. '/mobiquo';

$context['html_headers'] .= '
<!-- Tapatalk Detect head start -->
<link   href="'.$tapatalk_dir_url.'/smartbanner/jquery.smartbanner.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript">
    var byo_ios_app_id      = "'.intval($byo_ios_app_id).'";
    var byo_app_name        = "'.addslashes($byo_app_name).'";
    var byo_app_desc        = "'.addslashes($byo_app_desc).'";
    var byo_app_icon_url    = "'.addslashes($byo_app_icon_url).'";
    var app_ios_msg         = "'.addslashes($app_ios_msg).'";
    var app_ios_url         = "'.addslashes($app_ios_url).'";
    var app_ios_hd_msg      = "'.addslashes($app_ios_hd_msg).'";
    var app_ios_hd_url      = "'.addslashes($app_ios_hd_url).'";
    var app_android_msg     = "'.addslashes($app_android_msg).'";
    var app_android_url     = "'.addslashes($app_android_url).'";
    var app_android_hd_msg  = "'.addslashes($app_android_hd_msg).'";
    var app_android_hd_url  = "'.addslashes($app_android_hd_url).'";
    var app_kindle_msg      = "'.addslashes($app_kindle_msg).'";
    var app_kindle_url      = "'.addslashes($app_kindle_url).'";
    var app_kindle_hd_msg   = "'.addslashes($app_kindle_hd_msg).'";
    var app_kindle_hd_url   = "'.addslashes($app_kindle_hd_url).'";
    var tapatalk_dir_name   = "'.addslashes($tapatalk_dir_name).'";
    var tapatalk_dir_url    = "'.addslashes($tapatalk_dir_url).'";
    var app_location_url    = "'.addslashes($app_location_url).'";
</script>
<script src="'.$tapatalk_dir_url.'/smartbanner/jquery.smartbanner.js" type="text/javascript"></script>
<!-- Tapatalk Detect head end-->
';

if (!isset($context['tapatalk_body_hook']))
    $context['tapatalk_body_hook'] = '';

$context['tapatalk_body_hook'] .= '
<!-- Tapatalk Detect body start -->
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