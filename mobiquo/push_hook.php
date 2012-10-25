<?php
error_reporting(-1);
function tapatalk_push_reply($post_id)
{
    global $user_info, $context, $smcFunc, $boardurl, $modSettings;

    if(!isset($modSettings['tp_pushEnabled']) || !$modSettings['tp_pushEnabled'])
        return;
    //subscribe push
    $pushed_user_ids = array();
    if ($context['current_topic'] && $post_id && (function_exists('curl_init') || ini_get('allow_url_fopen')))
    {
        $request = $smcFunc['db_query']('', '
            SELECT ts.id_member
            FROM {db_prefix}log_notify ts
            LEFT JOIN {db_prefix}tapatalk_users tu ON (ts.id_member=tu.userid)
            WHERE ts.id_topic = {int:topic_id} AND tu.subscribe=1',
            array(
                'topic_id' => $context['current_topic'],
            )
        );
        while($row = $smcFunc['db_fetch_assoc']($request))
        {
            if ($row['id_member'] == $user_info['id']) continue;
    
            $ttp_data = array(
                'userid'    => $row['id_member'],
                'type'      => 'sub',
                'id'        => $context['current_topic'],
                'subid'     => $post_id,
                'title'     => tt_push_clean($_POST['subject']),
                'author'    => tt_push_clean($user_info['name']),
                'dateline'  => time(),
            );
            $pushed_user_ids[] = $row['id_member'];
            store_as_alert($ttp_data);
            $ttp_post_data = array(
                'url'  => $boardurl,
                'data' => base64_encode(serialize(array($ttp_data))),
            );
            $return_status = tt_do_post_request($ttp_post_data);
        }
    }
    tapatalk_push_quote_tag($post_id, false, $pushed_user_ids);
}

function tapatalk_push_quote_tag($post_id, $newtopic = false, $pushed_user_ids = array())
{
    global $user_info, $context, $smcFunc, $boardurl, $modSettings, $topic;
    
    if(!isset($modSettings['tp_pushEnabled']) || !$modSettings['tp_pushEnabled'])
        return;
    if (($newtopic ? $topic : $context['current_topic']) && isset($_POST['message']) && $post_id && (function_exists('curl_init') || ini_get('allow_url_fopen')))
    {
        $message = $_POST['message'];
        //quote push
        $quotedUsers = array();
        if(preg_match_all('/\[quote author=(.*?) link=.*?\]/si', $message, $quote_matches))
        {
            $quotedUsers = $quote_matches[1];
            $quote_ids = loadMemberData($quotedUsers, true);
            $request = $smcFunc['db_query']('', '
                SELECT tu.userid
                FROM {db_prefix}tapatalk_users tu
                WHERE tu.quote = 1 AND tu.userid IN ({array_int:quoteids})' ,
                array(
                    'quoteids' => $quote_ids,
                )
            );
            while($row = $smcFunc['db_fetch_assoc']($request))
            {
                if ($row['userid'] == $user_info['id']) continue;
                if (in_array($row['userid'], $pushed_user_ids)) continue;
                
                $ttp_data = array(
                    'userid'    => $row['userid'],
                    'type'      => 'quote',
                    'id'        => ($newtopic ? $topic : $context['current_topic']),
                    'subid'     => $post_id,
                    'title'     => tt_push_clean($_POST['subject']),
                    'author'    => tt_push_clean($user_info['name']),
                    'dateline'  => time(),
                );
                $pushed_user_ids[] = $row['userid'];
                store_as_alert($ttp_data);
                $ttp_post_data = array(
                    'url'  => $boardurl,
                    'data' => base64_encode(serialize(array($ttp_data))),
                );
                $return_status = tt_do_post_request($ttp_post_data);
            }
        }
        //@ push
        if (preg_match_all( '/(?<=^@|\s@)(#(.{1,50})#|\S{1,50}(?=[,\.;!\?]|\s|$))/U', $message, $tags ) )
        {
            foreach ($tags[2] as $index => $tag)
            {
                if ($tag) $tags[1][$index] = $tag;
            }
            $tagged_usernames =  array_unique($tags[1]);
            $tag_ids = loadMemberData($tagged_usernames, true);
            $request = $smcFunc['db_query']('', '
                SELECT tu.userid
                FROM {db_prefix}tapatalk_users tu
                WHERE tu.tag = 1 AND tu.userid IN ({array_int:tag_ids})' ,
                array(
                    'tag_ids' => $tag_ids,
                )
            );
            while($row = $smcFunc['db_fetch_assoc']($request))
            {
                if ($row['userid'] == $user_info['id']) continue;
                if (in_array($row['userid'], $pushed_user_ids)) continue;
                
                $ttp_data = array(
                    'userid'    => $row['userid'],
                    'type'      => 'tag',
                    'id'        => ($newtopic ? $topic : $context['current_topic']),
                    'subid'     => $post_id,
                    'title'     => tt_push_clean($_POST['subject']),
                    'author'    => tt_push_clean($user_info['name']),
                    'dateline'  => time(),
                );
                $pushed_user_ids[] = $row['userid'];
                store_as_alert($ttp_data);
                $ttp_post_data = array(
                    'url'  => $boardurl,
                    'data' => base64_encode(serialize(array($ttp_data))),
                );
                $return_status = tt_do_post_request($ttp_post_data);
            }
        }
    }
}
function tapatalk_push_pm()
{
    global $user_info, $smcFunc, $boardurl, $modSettings;

    if(!$modSettings['tp_pushEnabled'] || (!function_exists('curl_init') && !ini_get('allow_url_fopen')))
        return;
    if (isset($_REQUEST['recipient_to']) && is_array($_REQUEST['recipient_to']) && !empty($_REQUEST['recipient_to']) && isset($_REQUEST['subject']))
    {
        $timestr = time();
        $id_pm_req = $smcFunc['db_query']('', '
            SELECT p.id_pm
            FROM {db_prefix}personal_messages p
            WHERE p.msgtime > {int:msgtime_l} AND p.msgtime < {int:msgtime_h} AND p.id_member_from = {int:send_userid} ',
            array(
                'msgtime_l' => $timestr-2,
                'msgtime_h' => $timestr+2,
                'send_userid' => $user_info['id'],
            ));
        $id_pm = $smcFunc['db_fetch_assoc']($id_pm_req);
        if($id_pm_req)
            $smcFunc['db_free_result']($id_pm_req);

        if ($id_pm)
        {
            $request = $smcFunc['db_query']('', '
                SELECT tu.userid
                FROM {db_prefix}tapatalk_users tu
                WHERE tu.userid IN ({array_int:recipient_to}) AND tu.pm = 1',
                array(
                    'recipient_to' => $_REQUEST['recipient_to'],//$recipientList['to'],
                )
            );
            while($row = $smcFunc['db_fetch_assoc']($request))
            {
                if ($row['userid'] == $user_info['id']) continue;
                
                $ttp_data = array(
                    'userid'    => $row['userid'],
                    'type'      => 'pm',
                    'id'        => $id_pm['id_pm'],
                    'title'     => tt_push_clean($_REQUEST['subject']),
                    'author'    => tt_push_clean($user_info['name']),
                    'dateline'  => time(),
                );
                store_as_alert($ttp_data);
                $ttp_post_data = array(
                    'url'  => $boardurl,
                    'data' => base64_encode(serialize(array($ttp_data))),
                );
                $return_status = tt_do_post_request($ttp_post_data);
            }
        }
    }
}

function tt_do_post_request($data)
{
    $push_url = 'http://push.tapatalk.com/push.php';
    $response = 'CURL is disabled and PHP option "allow_url_fopen" is OFF. You can enable CURL or turn on "allow_url_fopen" in php.ini to fix this problem.';
    if (function_exists('curl_init'))
    {
        $ch = curl_init($push_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);

        $response = curl_exec($ch);
        curl_close($ch);
    }
    elseif (ini_get('allow_url_fopen'))
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
    return $response;
}

function tt_push_clean($str)
{
    $str = strip_tags($str);
    $str = trim($str);
    return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
}

function store_as_alert($push_data)
{
	global $smcFunc, $db_prefix;

    db_extend();
    $matched_tables = $smcFunc['db_list_tables'](false, $db_prefix . "tapatalk_push");
    if(!empty($matched_tables))
	{
		$push_data['title'] = $smcFunc['db_escape_string']($push_data['title']);
		$push_data['author'] = $smcFunc['db_escape_string']($push_data['author']);
		$request = $smcFunc['db_insert']('ignore',
					'{db_prefix}tapatalk_push',
					array('userid' => 'int', 'type' => 'string', 'id' => 'int', 'subid' => 'int', 'title' => 'string', 'author' => 'string', 'dateline' => 'int'),
					array($push_data['userid'], $push_data['type'], $push_data['id'], isset($push_data['subid'])? $push_data['subid'] : ' ', $push_data['title'], $push_data['author'], $push_data['dateline']),
					array('userid')
		);
		$affected_rows = $smcFunc['db_affected_rows']($request);
	}
}

