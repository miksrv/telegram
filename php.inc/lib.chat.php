<?php
/**
 * Chat libraru
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   chat
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */
class chat {

    /**
     * Save telegram user avatar
     * 
     * @param string $url avatar file url
     * @param string $name avatar file name
     * @return string full avatar file name
     */
    function save_avatar($url, $name) {
        $temp = file_get_contents($url);

        if (empty($temp)) {
            return ;
        }

        $ext  = substr(strrchr($url, '.'), 1);
        $name = $name . '.' . $ext;

        file_put_contents(LOAD . $name, $temp);

        return $name;
    } // function save_avatar($url, $name)
    

    /**
     * Send to browser json response
     * 
     * @param array $array
     */
    static function json($array) {
        header("Content-type: application/json; charset=utf-8");
        
        echo json_encode($array);
        exit();
    } // static function json($array)

    
    /**
     * Return TRUE is current query is AJAX
     * 
     * @return boolean
     */
    static function is_ajax() {
        return ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    } // static function is_ajax()

    
    /**
     * Redirect user to other URL
     * 
     * @param string $url
     */
    static function redirect($url) {
        header('Location: ' . $url);
        exit();
    } // static function redirect($url)

    
    /**
     * Cuts the text to the desired length
     * 
     * @param string $string input string
     * @param int $length cuteoff string
     * @return string
     */
    static function cutoff($string, $length = 27) {
        return (mb_strlen($string) > $length) ? mb_substr($string, 0, $length) . '..' : $string;
    } // static function cutoff($string, $length = 100)
    
    
    /**
     * Format datetime
     * 
     * @param integer $timestamp
     * @return string
     */
    static function formatdate($timestamp) {
        $difference = floor((time() - $timestamp) / (60*60*24));
 
        if ($difference > 0) {
            return date('d.m.y', $timestamp);
        }

        return date('H:i', $timestamp);
    } // static function formatdate($timestamp)
}