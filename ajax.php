<?php
/**
 * Ajax functions
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   ajax
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */

define('PATH', str_replace(basename(__FILE__), '', __FILE__));

include_once PATH . 'php.inc/init.php';

$chat = isset($_GET['chat']) ? filter_input(INPUT_GET, 'chat', FILTER_SANITIZE_NUMBER_INT) : FALSE;
$text = isset($_POST['text']) ? filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING) : FALSE;
$favorite = isset($_GET['favorite']) ? filter_input(INPUT_GET, 'favorite', FILTER_SANITIZE_NUMBER_INT) : FALSE;
$action = isset($_GET['action']) ? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) : FALSE;

if ( ! $loader->chat::is_ajax()) {
    $loader->chat::redirect('/');
}

switch ($action) {
    case 'getUserList' :

        ob_start();

        foreach (get_user_list() as $item) {
            include(PATH . 'views/user.php');
        }

        $output = ob_get_contents();

        ob_end_clean();

        $loader->chat::json(array(
            'status'  => true,
            'content' => str_replace(array("\n", "  "), "", $output)
        ));

        break;

    case 'getUpdates' :
        $messages = $loader->mysql->get('messages', array(
            'where' => array(
                'item_from_id' => $chat,
                'item_viewed'  => 0,
                'item_hide'    => 0
            ),
            'order' => array('item_date' => 'ASC'))
        );

        $loader->mysql->set('messages', array(
            'data' => array(
                'item_viewed'  => 1,
            ),
            'where' => array(
                'item_from_id' => $chat
            ))
        );

        if ( ! empty($messages) && is_array($messages)) {
            $messages[0]['item_date'] = $loader->chat::formatdate($messages[0]['item_date']);
        }

        $loader->chat::json(array(
            'status'   => true,
            'messages' => $messages
        ));

        break;
}

if ( ! empty($chat)) {
    if ($favorite !== FALSE && ($favorite == 1 || $favorite == 0)) {
        $result = $loader->mysql->set('users', array(
            'data' => array(
                'user_favorite' => $favorite,
            ),
            'where' => array(
                'user_id' => $chat,
            ))
        );

        $loader->chat::json(array(
            'status' => true
        ));
    }

    if ( ! empty($text)) {
        $telegram = $loader->telegram->sendMessage(array(
            'chat_id' => $chat,
            'text'    => $text,
        ));
        
        if ($telegram['ok'] == false) {
            $loader->chat::json(array(
                'status' => true
            ));
        }

        $loader->mysql->set('messages', array(
            'data' => array(
                'item_update_id'  => time(),
                'item_message_id' => time(),
                'item_from_id'    => $chat,
                'item_date'       => time(),
                'item_text'       => $text,
                'item_viewed'     => 1,
                'item_answer'     => 1,
            ))
        );

        $loader->chat::json(array(
            'status' => true,
            'user'   => array(
                'user_avatar' => 'manager.png'
            ),
            'messages' => array(
                'item_text' => $text,
                'item_date' => $loader->chat::formatdate(time())
            )
        ));
    }

    $loader->mysql->set('messages', array(
        'data' => array(
            'item_viewed'  => 1,
        ),
        'where' => array(
            'item_from_id' => $chat
        ))
    );

    $user_data = $loader->mysql->get('users', array(
        'where' => array(
            'user_id' => $chat
        ))
    );
    
    $messages = $loader->mysql->get('messages', array(
        'where' => array(
            'item_from_id' => $chat,
            'item_hide'    => 0,
        ),
        'order' => array('item_date' => 'ASC'))
    );

    foreach ($messages as $key => $val) {
        $messages[$key]['item_date'] = $loader->chat::formatdate($val['item_date']);
    }

    if (empty($messages) || empty($user_data)) {
        $loader->chat::json(array(
            'status' => true,
        ));
    }

    $loader->chat::json(array(
        'status'   => true,
        'user'     => $user_data,
        'messages' => $messages
    ));
}