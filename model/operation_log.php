<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of operation log
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */

class OperationLog extends Object{

    protected $tablename = 's_log_oper';

    /**
     * 检索可选字段
     */
    protected $query_data = ['begin_time', 'end_time', 'uid', 'uname', 'flag', 'module_id', 'menu_id','cid','platform'];

	/**
     * constructor
	 *
     * @param  int 	$id 	ID
     */
	public function __construct($id = NULL){
		parent::__construct($this->tablename, $id); 
	}

    /**
     * read s_log_info OR s_log_err via imark
     *
     * @param  string        $type  1:info / 0:err
     * @return array|False  
     */
    public function read_detail($type = 1){   
        $tables = ['s_log_err', 's_log_info'];
        $tablename = $tables[$type];
        if (!$this->id){
            $ret = False;
        }else{
            $sql = "SELECT d.* FROM `{$this->tablename}` AS o, `{$tablename}` AS d WHERE o.id={$this->id} AND o.imark = d.imark";
            $ret = $this->app->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        return $ret;
    }

}




