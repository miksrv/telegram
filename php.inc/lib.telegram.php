<?php
/**
 * Telegram library
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   loader
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */
class telegram {
    
    /**
     * Incoming messages
     * @var array
     */
    var $updates = array();
    
    /**
     * API key
     */
    private $_api_key;
    
    
    function __construct($api_key) {
        if (empty($api_key)) {
            throw new Exception('No telegram API key');
        }
        
        $this->_api_key = $api_key;
    } // function __construct($api_key)
    
    
    function endpoint($method, $content = array()) {
        $api_url = 'https://api.telegram.org/bot' . $this->_api_key . '/' . $method;

        return json_decode($this->_exec($api_url, $content), TRUE);
    } // function endpoint($method)
    
    
    /**
     * Recive incoming messages
     * 
     * @see https://core.telegram.org/bots/api#getupdates
     * @param int $offset
     * @param int $limit
     * @param int $timeout
     * @param int $update
     * @return array
     */
    function getUpdates($offset = 0, $limit = 100, $timeout = 0, $update = TRUE) {
        $content = array(
            'offset'  => $offset, 
            'limit'   => $limit, 
            'timeout' => $timeout
        );

        $this->updates = $this->endpoint("getUpdates", $content);

        if ($update) {
            if (count($this->updates["result"]) >= 1) {

                $last_id = $this->updates["result"][count($this->updates["result"]) - 1]["update_id"] + 1;
                $content = array(
                    'offset'  => $last_id,
                    'limit'   => 1,
                    'timeout' => $timeout
                );

                $this->endpoint("getUpdates", $content);
            }
        }

        return $this->updates;
    } // function getUpdates($offset = 0, $limit = 100, $timeout = 0, $update = TRUE)


    /**
     * Use this method to send text messages. 
     * 
     * @see https://core.telegram.org/bots/api#sendmessage
     * @param array $content
     */
    function sendMessage(array $content) {
        return $this->endpoint("sendMessage?chat_id=" . $content['chat_id'] . "&text=" . $content['text']);
    } // function sendMessage(array $content)

    
    /**
     * Use this method to get basic info about a file and prepare it for downloading.
     * 
     * @see https://core.telegram.org/bots/api#getfile
     * @param string $file_id
     * @return array
     */
    function getFile($file_id) {
        return $this->endpoint("getFile?file_id=" . $file_id);
    } // function getFile($file_id)


    /**
     * Use this method to get a list of profile pictures for a user. Returns a UserProfilePhotos object.
     * 
     * @see https://core.telegram.org/bots/api#getUserProfilePhotos
     * @param int $user_id
     * @return array
     */
    function getUserProfilePhotos($user_id) {
        return $this->endpoint("getUserProfilePhotos?user_id=" . $user_id);
    } // function getUserProfilePhotos($user_id)

    
    /**
     * Create last user avatar link
     * 
     * @uses getUserProfilePhotos, getFile;
     * @param integer $user_id
     * @return string
     */
    function getUserAvatarLink($user_id) {
        $photos_list = $this->getUserProfilePhotos($user_id);
    
        if ($photos_list['ok'] == FALSE) {
            return;
        }
        
        $photo_id  = $photos_list['result']['photos'][0][0]['file_id'];
        $file_path = $this->getFile($photo_id);
    
        if ($file_path['ok'] == FALSE) {
            return ;
        }

        return 'https://api.telegram.org/file/bot' . $this->_api_key . '/' . $file_path['result']['file_path'];
    } // function getUserAvatarLink($user_id)


    /**
     * Curl function
     * 
     * @param string $api_url CURL URL
     * @return CURL response
     */
    protected function _exec($api_url, $post = array()) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ( ! is_array($post) && ! empty($post)) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } // function _exec($api_url) 
}