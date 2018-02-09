<?php
/**
 * Lib's loader
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   loader
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */
class loader {
    
    /**
     * Directory for storing the includes files
     */
    const DIR = 'php.inc/';

    /**
     * The array of loaded libraries
     */
    protected $_libraries = array();

    
    function __construct() {

    }
    
    function __get($call) {
        if ( ! isset($this->_libraries[$call])) {
            return $this->init($call);
        }
        
        return $this->_libraries[$call];
    } // function __get($call)


    /**
     * Return loaded class
     * 
     * @param string $library class name
     * @param array $param additional parametrs for loaded class
     * @return object
     * @throws Exception
     */
    function init($library, $param = array()) {
        $file_name = 'lib.' . $library . '.php';
        $file_path = PATH . self::DIR . $file_name;

        if ( ! file_exists($file_path)) {
            throw new Exception('Filepath ' . $file_path . ' not found!');
        }

        if ( isset($this->_libraries[$library])) {
            return $this->_libraries[$library];
        }

        require_once $file_path;

        $this->_libraries[$library] = new $library( ! empty($param) ? $param : NULL);
    } // function init($library, $param = array())
}