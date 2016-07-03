<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Basic class of object
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     core
 */
class Object{
	
	/**
	 * tablename of object
	 */
	protected $tablename;

	/**
	 * ID of the object
	 */
	protected $id;


    /**
     * fields will write into table
     */
    protected $format_data = [];
    protected $amount_data = [];
    protected $order_data = [];
    protected $query_data = ['id','search','begin_time','begin_date','end_time','end_date','AND','OR'];

//    protected $check_table = ['b_order','b_stock_out','b_stock_in','b_commission','b_debit_note','b_inventory_phy',
//        'b_inventory_sys','b_payment_note','b_price','b_price_temp','b_settle_customer','b_settle_proxy_supplier',
//        'b_settle_supplier'];
	/**
     * constructor
     * @param  string   $tablename 	tablename of object
     * @param  int  	$id 		ID
     */
	public function __construct($tablename, $id = NULL){
		$this->tablename = $tablename;
		$this->id = NULL ? $id===NULL : intval($id);
		$this->app = \Slim\Slim::getInstance();
	}

	/**
     * update by ID
     *
     * NOTICE: This function may return Boolean FALSE, 
     *         but may also return a non-Boolean value(e.g. 0) which evaluates to FALSE. 
     *
     * @param  array     $db_set 	SQL:SET
     * @return int|False the number of rows that were modified
     */
	public function update_by_id($db_set, $allow_all = false){		
		if (!$this->id){
			$ret = False;
		}else{
			$db_where = ['id' => $this->id];
		    $ret = $this->update($db_set, $db_where, $allow_all);	
		}
		return $ret;
	}

	/**
     * delete by ID
     *
     * NOTICE: This function may return Boolean FALSE, 
     *         but may also return a non-Boolean value(e.g. 0) which evaluates to FALSE. 
     *
     * @return int|False the number of rows that were modified
     */
	public function delete_by_id(){		
		if (!$this->id){
			$ret = False;
		}else{
			$db_where = ['id' => $this->id];
			$ret = $this->delete($db_where);
		}
		return $ret;
	}
	
	/**
     * update object set status 0
     *
     */
	public function delete_only_status(){
		if (!$this->id){
			$ret = False;
		}else{
			$db_set = ['status' => '0'];
			$db_where = ['id' => $this->id];
		    $ret = $this->update($db_set, $db_where, true);	
		}
		return $ret;
	}
	

	/**
     * read by id
     *
     * @param  array     	$db_fields 	SQL:fields
     * @return array|False 	
     */
	public function read_by_id($id = Null, $db_fields = '*'){
        if(!$id){
            $id = $this->id;
        }
		if (!$id){
			$ret = False;
		}else{
            $db_where = ['id' => $id];
			$ret = $this->read($db_fields, $db_where);
		}
		return $ret;
	}

	/**
	 * read list
	 *
	 * @param  array       $data       POST/GET param
	 * @param  array       $db_fields  SQL:fields
	 * @param  bool        $status 	   set default status
	 * @return array|False
	 */
	public function read_list($data, $db_fields = '*'){
        $page_num = get_value($data, 'page_num', 5000);
		$db_where = $this -> build_query($data);
        $db_where_page = $this -> build_query($data, False);
        if (isset($this->list_return) && $db_fields == '*'){
            $db_fields = $this->list_return;
        }
		$ret_data = $this->read($db_fields, $db_where);
        $ret_count = $this->count($db_where_page);
        $ret_page = intval($ret_count/$page_num);
        if($ret_count%$page_num!=0){
            $ret_page ++;
        }
        $ret = [
            'data' => $ret_data,
            'count' => $ret_count,
            'page_count' => $ret_page
        ];
		return $ret;
	}

    /**
     * read list
     *
     * @param  array       $data       POST/GET param
     * @param  array       $db_fields  SQL:fields
     * @param  bool        $status 	   set default status
     * @return array|False
     */
    public function read_list_nopage($data, $db_fields = '*'){
        $data['page'] = 1;
        if(!get_value($data, 'page_num') || get_value($data, 'page_num')>5000){
            $data['page_num'] = 5000;
        }
        $db_where = $this -> build_query($data);
        if (isset($this->list_return) && $db_fields == '*'){
            $db_fields = $this->list_return;
        }
        $ret_data = $this->read($db_fields, $db_where);
        return $ret_data;
    }

    /**
     * read One Record
     *
     * @param  array       $data       POST/GET param
     * @param  array       $db_fields  SQL:fields
     * @return array|False
     */
    public function read_one($data, $db_fields = '*'){
        $data['page'] = 1;
        $data['page_num'] = 1;
        $db_where = $this -> build_query($data);
        $ret_data = $this->read($db_fields, $db_where);
        if($ret_data){
            return $ret_data[0];
        }
        else{
            return False;
        }
    }
	
	/**
	 * build query
	 *
	 * @param  array       $data       POST/GET param
	 * @param  bool        $status 	   set default status
	 * @return array for db_where
	 */
	public function build_query($data, $page = True){

        $orderby = [];
        if(get_value($data, 'orderby')){
            if(strpos($data['orderby'], ',')){
                $my_orderby = explode(',', $data['orderby']);
            }
            else{
                $my_orderby = [$data['orderby']];
            }
            foreach($my_orderby as $val){
                if(strpos($val, '^')){
                    $iarray = explode('^', $val);
                    if($iarray[0]=='id' || in_array($iarray[0], $this->order_data)){
                        $item = $iarray[0];
                        $otype = strtoupper($iarray[1]);
                        $orderby[] = $item. ' '. $otype;
                    }
                }
                else{
                    if($data['orderby']=='id' || in_array($data['orderby'], $this->order_data)){
                        $item = $data['orderby'];
                        $orderby[] = $item. ' '. 'DESC';
                    }
                }
            }
        }
        else{
            $orderby = 'id DESC';
        }

        if($page){
            //设置默认页码和最大页数
            $page = get_value($data, 'page', 1);
            $page_num = get_value($data, 'page_num', 200);
            $start_count = ($page - 1) * $page_num;
            $db_where = [
                'ORDER' => $orderby,
                'LIMIT' => [$start_count, $page_num]
            ];
        }
        else{
            $db_where = [];
        }

        if(isset($data['GROUP'])){
            $db_where['GROUP'] = $data['GROUP'];
        }
		
		//AND最终组装的参数列表
		$and_data = [];
		if(isset($this->query_data)){
            foreach($data as $key=>$val){
                if($val === ""){
                    continue;
                }

                //去掉中括号，判断基本条件
                $temp = strpos($key,'[');
                if($temp){
                    $key_check = substr($key, 0 ,strpos($key,'['));
                }
                else{
                    $key_check = $key;
                }
                if(!in_array($key_check, $this->query_data) && !in_array($key_check, $this->format_data)
                    && !in_array('*'.$key_check, $this->format_data)){
                    //只有在检索列表或者字段列表才允许检索
                    continue;
                }

                $time_name = 'createtime';
                if(in_array('checktime', $this->format_data)){
                    $time_name = 'checktime';
                }
//                if(in_array($this->tablename, $this->check_table)){
//                    $time_name = 'checktime';
//                }

                if($key == 'begin_time'){
                    $and_data[$time_name.'[>=]'] = $val;
                }
                elseif($key == 'end_time'){
                    $and_data[$time_name.'[<=]'] = $val;
                }
                elseif($key == 'begin_date'){
                    $and_data[$time_name.'[>=]'] = $val. ' 00:00:00';
                }
                elseif($key == 'end_date'){
                    $and_data[$time_name.'[<=]'] = $val. ' 23:59:59';
                }
                elseif($key == 'search'){
                    if(isset($this->search_data) && $this->search_data && $val){
                        $and_data['OR'] = [];
                        foreach($this->search_data as $val2){
                            $and_data['OR'][$val2.'[~]'] = '%'.$val.'%';
                        }
                    }
                }
                else{
                    if($val == 'null' || $val === []){
                        $and_data[$key] = Null;
                    }
                    else{
                        $and_data[$key] = $val;
                    }
                }
            }


//			foreach($this->query_data as $val){
//                                                //like 的情况
//				if($val[0] == '*'){
//					$val = substr($val, 1);
//					if(!get_value($data, $val)){
//						continue;
//					}
//					$and_data[$val.'[~]'] = '%'.$data[$val].'%';
//				}
//				else{
//                    if(!isset($data[$val])){
//                        continue;
//                    }
//                    if($data[$val] == []){
//                        $data[$val] = 'null';
//                    }
//					// if(!get_value($data, $val)){
//					// 	continue;
//					// }
//					//begin_time 和 end_time的情况
//					if($val == 'begin_time'){
//						$and_data['createtime[>=]'] = $data[$val];
//					}
//					elseif($val == 'end_time'){
//						$and_data['createtime[<=]'] = $data[$val];
//					}
//                    elseif($val == 'begin_date'){
//                        $and_data['createtime[>=]'] = $data[$val]. ' 00:00:00';
//                    }
//                    elseif($val == 'end_date'){
//                        $and_data['createtime[<=]'] = $data[$val]. ' 23:59:59';
//                    }
//                    elseif($val == 'big_search'){
//                        $and_data['OR'] = [
//                            'name[~]' => '%'.$data[$val].'%',
//                            'py_name[~]' => '%'.$data[$val].'%',
//                        ];
//
//                        if(in_array('code', $this->query_data)){
//                            $and_data['OR']['code[~]'] = '%'.$data[$val].'%';
//                        }
//                    }
//                    elseif($val == 'gsearch'){
//                        $and_data['OR'] = [
//                            'gname[~]' => '%'.$data[$val].'%',
//                            'gcode[~]' => '%'.$data[$val].'%',
//                        ];
//                    }
//                    elseif($val == 'search'){
//                        if(isset($this->search_data) && $this->search_data && $data[$val]){
//                            $and_data['OR'] = [];
//                            foreach($this->search_data as $v2){
//                                $and_data['OR'][$v2.'[~]'] = '%'.$data[$val].'%';
//                            }
//                        }
//                    }
//                    //最一般的情况
//                    else{
//                        if($data[$val] == 'null'){
//                            $and_data[$val] = Null;
//                        }
//                        else{
//                            $and_data[$val] = $data[$val];
//                        }
//                    }
//				}
//			}
		}
		
		//判断and参数最终是否大于1个条件，如果大于1个，写法会和其它情况有点不一样
		if(count($and_data) > 1){
			$db_where['AND'] = $and_data;
		}
		else{
			foreach($and_data as $key=>$val){
				$db_where[$key] = $val;
				break;
			}
		}
		
		return $db_where;
	}
	

	
	/**
	 * Now, here is the CRUD
	 */

    /**
     * DB:create via batch
     * @param  array        $db_datas        some items for insert
     * @return int|False    lastInsertId
     */
    public function create_batch($db_datas, $createtime = 1){
        foreach($this->format_data as $key=>$val){
            if($val[0] == '*'){
                $this->format_data[$key] = substr($val, 1);
            }
        }
        foreach ($db_datas as $k => $db_data){
            foreach ($db_data as $key => $val){
                if (!in_array($key, $this->format_data)){
                    unset($db_datas[$k][$key]);
                }
                if(in_array($key, $this->amount_data)){
                    $db_datas[$k][$key] = yuan2fen($db_datas[$k][$key]);
                }
            }
        }

        $ret = 0;
        if ($db_datas){
            $now = date('Y-m-d H:i:s');
            foreach ($db_datas as $k => $db_data){
                foreach ($db_data as $key => $val){
                    if (get_value($db_data, 'name')){
                        require_once 'core/pinyin.php';
                        $db_datas[$k]['py_name'] = pinyin($db_data['name']);
                    }
                    if($createtime){
                        $db_datas[$k]['createtime'] = $now;
                        if(in_array('checktime', $this->format_data)){
                            $db_datas[$k]['checktime'] = $now;
                        }
                    }
                }
            }
            $ret = $this->app->db->insert($this->tablename, $db_datas);
        }
        if ($ret === False){
            if (stripos($this->app->db->pdo->errorInfo()[2], 'Duplicate entry') === 0){ //记录已存在
                error(1200);
            } else {
                error(9900);
            }
        }
        return $ret;
    }

	/**
     * DB:create
     * @param  array    	$db_data 		data for insert
     * @return int|False 	lastInsertId
     */
	public function create($db_data, $createtime=1){
        foreach($this->format_data as $key=>$val){
            if($val[0] == '*'){
                $this->format_data[$key] = substr($val, 1);
            }
        }
		foreach($db_data as $key=>$val){
			if(!in_array($key, $this->format_data)){
				unset($db_data[$key]);
			}
            if(in_array($key, $this->amount_data)){
                $db_data[$key] = yuan2fen($db_data[$key]);
            }
		}
        $ret = 0;
        if ($db_data){
        	if(get_value($db_data, 'name')){
                require_once 'core/pinyin.php';
                $db_data['py_name'] = pinyin( $db_data['name'] );
        	}
            if($createtime){
                $now = date('Y-m-d H:i:s');
                $db_data['createtime'] = $now;
                if(in_array('checktime', $this->format_data)){
                    $db_data['checktime'] = $now;
                }
            }
            $ret = $this->app->db->insert($this->tablename, $db_data);
        }
        if ($ret === False){
            if (stripos($this->app->db->pdo->errorInfo()[2], 'Duplicate entry') === 0){ //记录已存在
                error(1201);
            } else {
                error(9900);
            }
        }
		return $ret;
	}
	/**
     * DB:update
     *
     * NOTICE: This function may return Boolean FALSE, 
     *         but may also return a non-Boolean value(e.g. 0) which evaluates to FALSE. 
     *
     * @param  array     $db_set 		SQL:SET
     * @param  array  	 $db_where 		SQL:WHERE
     * @return int|False the number of rows that were modified
     */
	public function update($db_set, $db_where, $allow_all=false, $updatetime = True){
        if(!$allow_all){
    		foreach($db_set as $key=>$val){
    			if(!in_array($key, $this->format_data)){
    				unset($db_set[$key]);
    			}
                if(in_array($key, $this->amount_data)){
                    $db_set[$key] = yuan2fen($db_set[$key]);
                }
    		}
        }
        $ret = 0;
        if ($db_set){
            if(get_value($db_set, 'name')){
                require_once 'core/pinyin.php';
                $db_set['py_name'] = pinyin($db_set['name']);
            }
            if($updatetime){
                $db_set['updatetime'] = date('Y-m-d H:i:s');
            }
            $ret = $this->app->db->update($this->tablename, $db_set, $db_where);
        }
        $ret === False && error(9900);
        return $ret;
	}

	/**
     * DB:select
     *
     * @param  array     	$db_fields 	SQL:fields
     * @param  array  	 	$db_where 	SQL:WHERE
     * @return array|False 	
     */
	public function read($db_fields = '*', $db_where = NULL){
		$ret = $this->app->db->select($this->tablename, $db_fields, $db_where);
        $ret === False && error(9900);
        foreach($ret as $k=>$v){
            foreach($v as $key=>$val){
                if(in_array($key, $this->amount_data)){
                    $ret[$k][$key] = fen2yuan($ret[$k][$key]);
                }
            }
        }
        return $ret;
	}

	/**
     * DB:delete
     *
     * NOTICE: This function may return Boolean FALSE, 
     *         but may also return a non-Boolean value(e.g. 0) which evaluates to FALSE. 
     *
     * @param  array  	 $db_where 		SQL:WHERE
     * @return int|False the number of rows that were modified
     */
	public function delete($db_where){		
		$ret = $this->app->db->delete($this->tablename, $db_where);
        $ret === False && error(9900);
        return $ret;
	}

	/**
	 * Now, here is the other basic functions
	 */

	/**
     * Get total of the data
     *
     * @param  array  	 	$db_where 	SQL:WHERE
     * @return int|False 	
     */
	public function count($db_where = NULL){		
		$ret = $this->app->db->count($this->tablename, $db_where);
        $ret === False && error(9900);
        return $ret;
	}

	/**
     * Check the data is EXISTS
     * NOTCIE:This method is very very faster than select!
     *
     * @param  array  	 	$db_where 	SQL:WHERE
     * @return boolean 	
     */
	public function has($db_where = NULL){
        if ($db_where){
            $db_where = [
                'AND' => $db_where
            ];    
        }
		$ret = $this->app->db->has($this->tablename, $db_where);
        return $ret;
	}

    public function sum($db_fields, $data = NULL){
        $db_where = $this->build_query($data, False);
        $ret = $this->app->db->sum($this->tablename, $db_fields, $db_where);
        foreach($ret as $key=>$val){
            if(in_array($key, $this->amount_data)){
                $ret[$key] = fen2yuan($val);
            }
        }
        return $ret;
    }

    /**
     * reset the object id
     */
    public function set_id($id = NULL){
        $this->id = $id===NULL ? NULL : intval($id);
    }

    public function get_id(){
        return $this->id;
    }

    /**
     * get code
     */
    public function get_code2(){
        $db_where = [
            'ORDER' => 'code DESC',
            'LIMIT' => '1',
            'code[~]' => $this->code_pre.'%'
        ];
        $res = $this->app->db->select($this->tablename, '*', $db_where);
        if($res){
            $code = $res[0]['code']+1;
        } else {
            $code = $this->code_pre.'000001';
        }
        return $code;
    }

    /**
     * get code
     */
    public function get_code(){
        $db_where = [
            'ORDER' => 'code DESC',
            'LIMIT' => '1',
            //'code[~]' => $this->code_pre.'%'
        ];
        $res = $this->app->db->select($this->tablename, '*', $db_where);
        if($res){
            $code = $res[0]['code']+1;
        } else {
            $code = $this->code_pre.'000001';
        }
        return $code;
    }

    //通过ID获取名称
    public function get_name_by_id($tablename, $id){
        $res = $this->app->db->select($tablename, '*', ['id'=>$id]);
        if(!$res){
            error(1210);
        }
        return $res[0]['name'];
    }

    //通过批量ID获取批量名称
    public function get_names_by_ids($tablename, $ids){
        $id_list = explode(',', $ids);
        $id_list2 = [];
        foreach($id_list as $val){
            if($val){
                $id_list2[] = $val;
            }
        }
        if(!$id_list2){
            return '无';
        }
        $names = [];
        $res = $this->app->db->select($tablename, '*', ['id'=>$id_list2]);
        $res2 = [];
        foreach($res as $val){
            $res2[$val['id']] = $val['name'];
        }
        foreach($id_list2 as $val){
            $names[] = get_value($res2, $val, '无');
        }
        $names = implode(',', $names);
        return $names;
    }

}



