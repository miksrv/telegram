<?php
/**
 * MySQL driver
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   mysql
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */
class mysql {
    
    protected $hostname = '';
    protected $username = '';
    protected $password = '';
    protected $database = '';
    protected $prefix   = '';
    
    private $_source;
    
    function __construct($param) {
        if ( ! is_array($param) || empty($param)) {
            throw new Exception('Empty params for mysql driver');
        }
        
        foreach ($param as $key => $item) {
            if (isset($this->$key)) {
                $this->$key = $item;
            }
        }

        if (empty($this->hostname) || empty($this->username) || 
            empty($this->password) || empty($this->database)) {
            throw new Exception('There are no parameters to connect to MySQL');
        }
        
        $this->_connect();
    } // function __construct($param)


    /**
     * Create a connect to database
     */
    function _connect() {
        $this->_source = mysqli_connect(
            $this->hostname, 
            $this->username, 
            $this->password
        );

        if ( ! $this->_source) {
            throw new Exception('Can not create database connection');
        }
        
        if ( ! mysqli_select_db($this->_source, $this->database)) {
            throw new Exception('Can not select database');
        }

        $this->_exec("SET NAMES 'utf8'");
    } // function _connect()


    /**
     * Return query result array (SELECT)
     * 
     * @param string $table
     * @param array $param conditions
     */
    function get($table, $param = array()) {
        if ( ! isset($param['query']) || empty($param['query'])) {
            $query = 'SELECT' . $this->_param_fields($param) . 'FROM `' . $this->prefix . $table . '`' . $this->_param_join($param) . $this->_param_where($param) . $this->_param_order($param) . $this->_param_limit($param);
        } else {
            $query = $param['query'];
        }

        $exec  = $this->_exec($query);

        if ( $exec == FALSE || ! mysqli_num_rows($exec)) {
            return array();
        }

        $result = array();

        while ($tmp = mysqli_fetch_assoc($exec)) {
            array_push($result, $tmp);
        }

        return $result;
    } // function get($table, $param = array())


    /**
     * Save data in database
     * 
     * @param string $table
     * @param array $param
     * @return type
     */
    function set($table, $param) {
        if ( ! isset($param['where'])) {
            $query = 'INSERT INTO `' . $this->prefix . $table . '`' . $this->_param_insert($param);
        } else {
            $query = 'UPDATE `' . $this->prefix . $table . '` SET ' . $this->_param_update($param) . $this->_param_where($param);
        }

        return $this->_exec($query);
    } // function set($table, $param)
    
    /**
     * Executes the database query
     * 
     * @param string $query
     * @return array
     * @throws Exception
     */
    function _exec($query) {
        if ( ! $this->_source) {
            throw new Exception('Could not connect to database');
        }

        return $result = mysqli_query($this->_source, $query);
    } // function _exec($query)

    
    /**
     * Create JOIN LEFT
     * 
     * @param array $param
     * @return string
     */
    protected function _param_join($param) {
        if ( ! isset($param['join']) || ! is_array($param['join']) || empty($param['join'])) {
            return ;
        }

        $result = ' LEFT JOIN ' . $this->prefix . $param['join']['table'] . ' ON ';

        foreach ($param['join']['where'] as $key => $val) {
            $result .= '`' . $key . '` = `' . $this->prefix . $param['join']['table'] . '`.`' . $val . '`';
        }
        
        return $result . (isset($param['join']['order']) ? $this->_param_order($param['join']) : '')
                       . (isset($param['join']['limit']) ? $this->_param_limit($param['join']) : '');
    } // protected function _param_join($param)

    
    /**
     * Create LIMIT param
     * 
     * @param array $param
     * @return string
     */
    protected function _param_limit($param) {
        if ( ! isset($param['limit']) || ! is_array($param['limit']) || empty($param['limit'])) {
            return ;
        }

        return ' LIMIT' . $param['limit'];
    } // protected function _param_limit($param)


    /**
     * Create conditions ORDER BY
     * 
     * @param array $param
     * @return string
     */
    protected function _param_order($param) { 
        if ( ! isset($param['order']) || ! is_array($param['order']) || empty($param['order'])) {
            return ;
        }

        $result = ' ORDER BY ';

        foreach ($param['order'] as $key => $val) {
            $result .= '`' . $key . '` ' . $val . ' ';
        }
        
        return $result;
    } // protected function _param_order($param)
    
    
    /**
     * Create conditions for UPDATE
     * 
     * @param array $param
     * @return string
     */
    protected function _param_update($param) {
        if ( ! isset($param['data']) || ! is_array($param['data']) || empty($param['data'])) {
            throw new Exception('No data insert params');
        }

        $data  = '';
        $count = count($param['data']);
        
        foreach ($param['data'] as $key => $val) {
            $data .= '`' . $key . '` = \'' . $val . '\'' . ( --$count ? ', ' : '');
        }

        return $data;
    } // protected function _param_update($param)

    
    /**
     * Create conditions for INSERT
     * 
     * @param array $param
     * @return string
     */
    protected function _param_insert($param) {
        if ( ! isset($param['data']) || ! is_array($param['data']) || empty($param['data'])) {
            throw new Exception('No data insert params');
        }
        
        $keys  = " (";
        $vals  = " (";
        $count = count($param['data']);

        foreach ($param['data'] as $key => $val) {
            $sep = ( --$count ? ", " : ")");
            $keys .= '`' . $key . '`' . $sep;
            $vals .= (is_int($val) ? $val : '\'' . $val . '\'') . $sep;
        }
        
        return $keys . ' VALUES' . $vals;
    } // protected function _param_insert($param)

    
    /**
     * Create fields conditions for query
     * 
     * @param mixed(array|string) $param
     * @return string
     */
    protected function _param_fields($param) {
        if ( ! isset($param['fields'])) {
            return ' * ';
        }
        
        if (is_string($param['fields'])) {
            return ' ' . $param['fields'] . ' ';
        }
        
        if (is_array($param['fields'] && empty($param['fields']))) {
            return ' * ';
        }
        
        $fields = ' ';
        $count = count($param['fields']);
        
        foreach ($param['fields'] as $val) {
            $fields .= $val . ( --$count ? ', ': ' ');
        }
        
        return $fields;
    } // protected function _param_fields($param)


    /**
     * Create conditions `WHERE` for query
     * 
     * @param mixed(array|string) $param
     * @return string
     */
    protected function _param_where($param) {
        if ( ! isset($param['where'])) {
            return '';
        }

        if (is_string($param['where'])) {
            return ' WHERE ' . $param['where'];
        }
        
        if (is_array($param['where'] && empty($param['where']))) {
            return '';
        }
        
        $where = ' WHERE ';
        $count = count($param['where']);
        
        foreach ($param['where'] as $key => $val) {
            $where .= '`' . $key . '` = \'' . $val . '\'' . ( --$count ? ' AND ': '');
        }

        return $where;
    } // protected function _param_where($param)
}