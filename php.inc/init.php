<?php
/**
 * Init functions
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   init
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */

define('DEVELOP', FALSE);

/**
 * Path to root directory
 */
define('LOAD', PATH . 'loads/');

if (DEVELOP) {
    ini_set("display_errors", 1);
    error_reporting(E_ALL); 
}

require_once(PATH . 'php.inc/config.php');
require_once(PATH . 'php.inc/lib.loader.php');

$loader = new loader();

$loader->init('telegram', $config['telegram_key']);
$loader->init('mysql', $config);
$loader->init('chat');

/**
 * Return user list array and last messages
 * 
 * @global object $loader
 * @return array
 */
function get_user_list() {
    global $loader;
    
    $param['query'] = 'SELECT * FROM `test_users` LEFT JOIN `test_messages` ON '
                    . '`test_messages`.`item_message_id` = (SELECT `tm1`.`item_message_id` FROM `test_messages` AS tm1 '
                    . 'WHERE `test_users`.`user_id` = tm1.item_from_id ORDER BY `item_date` DESC LIMIT 1) WHERE `test_users`.`user_hide` = 0 ORDER BY `user_favorite` DESC, `item_date` DESC';

    return $loader->mysql->get('users', $param);
} // function get_user_list()