<?php

if (!defined('SMF'))
    die('Hacking attempt...');

//Admin Areas
function Tapatalk_add_admin_areas(&$adminAreas)
{
    global $txt;
    $adminAreas['config']['areas'] += array(
        'tapatalksettings' => array(
            'label' => $txt['tapatalktitle'],
            'file' => 'ManageTapatalk.php',
            'function' => 'ManageTapatalk',
            'icon' => 'tapatalk_settings.png',
            'subsections' => array(
                'general' => array($txt['tp_general_settings']),
                'boards' => array($txt['tp_board_settings']),
                'iar' => array($txt['tp_iar_settings']),
                'rebranding' => array($txt['tp_rebranding_settings']),
                'others' => array($txt['tp_other_settings']),
            ),
        ),
    );
}
?>