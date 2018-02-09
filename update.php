<?php
/**
 * Telegram update
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   update
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */
set_time_limit(300);
//ini_set('max_execution_time', 300);

$starttime = time();

define('PATH', str_replace(basename(__FILE__), '', __FILE__));

include_once PATH . 'php.inc/init.php';

/**
 * MAIN CYCLE
 */
$last_update = 0;

$users    = $loader->mysql->get('users');
$messages = $loader->mysql->get('messages');
$temp  = array();

if ( ! empty($users)) {
    foreach ($users as $key => $val) {
        $temp[$val['user_id']] = $val;
    }
}

$users = $temp;

if ( ! empty($messages)) {
    $temp = array();

    foreach ($messages as $key => $val) {
        $temp[] = (int) $val['item_update_id'];
    }
}

$messages = $temp;

unset($temp);

while (true) {
    sleep(1);

    if ((time() - $starttime) >= 280) {
        exit('STOP BY TIMER ' . (time() - $starttime) . 'SEC');
    }

    $update = $loader->telegram->getUpdates();

    if ($update === NULL || ! isset($update['result'])) {
        return ;
    }

    foreach ($update['result'] as $item) {

        if ($last_update >= $item['update_id'] || in_array($item['update_id'], $messages)) {
            continue;
        }

        $last_update = $item['update_id'];

        $user_id   = (int) $item['message']['from']['id'];
        
        if ( ! key_exists($user_id, $users) || empty($users[$user_id]['user_avatar'])) {
            $avatar_link = $loader->telegram->getUserAvatarLink($user_id);
            $avatar_file = $loader->chat->save_avatar($avatar_link, $user_id);

            if (empty($users['user_avatar'])) {
                $result  = $loader->mysql->set('users', array(
                    'data' => array(
                        'user_avatar' => $avatar_file,
                    ),
                    'where' => array(
                        'user_id'     => $user_id,
                    ))
                );
            }
        }

        if ( ! key_exists($user_id, $users)) {
            $language  = isset($item['message']['from']['language_code']) ? $item['message']['from']['language_code'] : 'ru';
            $user_data = array(
                'user_id'            => $user_id,
                'user_first_name'    => $item['message']['from']['first_name'],
                'user_username'      => $item['message']['from']['username'],
                'user_language_code' => $language,
                'user_avatar'        => $avatar_file,
            );
            $result   = $loader->mysql->set('users', array('data' => $user_data));

            $users[$user_id] = $user_data;
        }

        $result = $loader->mysql->set('messages', array(
            'data' => array(
                'item_update_id'  => $item['update_id'],
                'item_message_id' => $item['message']['message_id'],
                'item_from_id'    => $item['message']['from']['id'],
                'item_date'       => $item['message']['date'],
                'item_text'       => htmlspecialchars(stripslashes($item['message']['text']), ENT_QUOTES),
            ))
        );

        $messages[] = (int) $item['update_id'];
    }
}