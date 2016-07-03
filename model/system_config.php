<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of system config
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */

class SystemConfig extends Object{


	/**
     * constructor
	 *
     * @param  int 	$id ID
     */
	public function __construct($id = NULL){
        parent::__construct('s_config', $id); 
	}


    /**
     * read by name
     *
     * @param  string        $name
     * @return array|False  
     */
    public function read_by_name($name){   
        $db_fields = ['value'];
        $db_where = ['name' => $name];
        $ret = $this->read($db_fields, $db_where);
        return $ret;
    }

    /**
     * read the config list, except for that own detail
     *
     * @param  string      $page       current page
     * @param  string      $page_num   total of items in a page
     * @param  array       $db_fields  SQL:fields
     * @return array|False
     */
    public function read_list_no_detail($page = NULL, $page_num = NULL, $db_fields = '*'){   
        $page = $page ? intval($page) : 1;
        $page_num = $page_num ? intval($page_num) : 50;
        $start_count = ($page - 1) * $page_num;
        $db_where = [
            'type'  => 1, //no detail
            'ORDER' => 'id DESC',
            'LIMIT' => [$start_count, $page_num]
        ];
        $ret = $this->read($db_fields, $db_where);
        return $ret;
    }


}




