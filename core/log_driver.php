<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Log driver
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     core
 */


/**
 * Log Writer by MySQL
 *
 * Log of INFO write into the 's_log_info' table
 * Log of ERR write into the 's_log_err' table
 * Log of Opertion write into the 's_log_oper' table
 *
 * @package model
 * @author  Linvo
 * @since   0.0.1
 */
class MySQLLogWriter{

    /**
     * constructor
     */
    public function __construct(){
        $app = \Slim\Slim::getInstance();
        $this->db = $app->db;
    }

    /**
     * Write data
     * @param  string    $data json data
     * @param  int       $level 
     */
    public function write($data, $level){
        $data = json_decode($data, true);
        if (is_array($data) && $data){
            if ($level >= 7) $this->_write_info($data);
            else if ($level == 6) $this->_write_notice($data);
            else $this->_write_err($data);
        }
    }

    /**
     * Insert data to 's_log_info'
     * @param  mixed     $data
     */
    private function _write_info($data){
        //info_log($data);
        $this->db->insert("s_log_info", $data);
    }

    /**
     * Insert data to 's_log_err'
     * @param  mixed     $data
     */
    private function _write_err($data){
        //err_log($data);
        $this->db->insert("s_log_err", $data);
    }

    /**
     * Insert data to 's_log_oper'
     * @param  mixed     $data
     */
    private function _write_notice($data){
        $this->db->insert("s_log_oper", $data);
    }


}



