<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of system config detail
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */

class SystemConfigDetail extends Object{


    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = ['fid', 'value'];

	/**
     * constructor
	 *
     * @param  int 	$id ID
     */
	public function __construct($id = NULL){
        parent::__construct('s_config_detail', $id); 
	}


    /**
     * read the config list by fid
     *
     * @param  string      $fid       the main config id
     * @return array|False
     */
    public function read_list_by_fid($fid){   
        $db_where = [
            'fid'  => intval($fid),
            'ORDER' => 'id DESC',
        ];
        $ret = $this->read('*', $db_where);;
        return $ret;
    }


}




