<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Basic class of bill
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     core
 */

/**
 * table: b_*_glist
 */
class Bill extends Object{
	
	/**
	 * tablename of bill
	 */
	protected $tablename;

	/**
	 * tablename of goods list 
	 */
	protected $tablename_glist;

	/**
	 * ID of the bill
	 */
	protected $id;

    /**
     * fields will write into bill table
     */
    protected $format_data = [];

    /**
     * fields will write into glist table
     */
    protected $format_data_glist = [];
    protected $amount_data_glist = ['unit_price','amount_price','tax_price','cost_price'];


	/**
     * constructor
     * @param  string   $tablename  bill tablename	
     * @param  int      $id 	    bill ID
     */
	public function __construct($tablename, $id = NULL){
        $tnames = ['b_order', 'b_stock_in', 'b_stock_out'];
        if (!in_array($tablename, $tnames)) error(9901);
		$this->tablename = $tablename;
		$this->tablename_glist = $tablename . '_glist';
		$this->id_name = substr($tablename, 2) . '_id';
		$this->id = NULL ? $id===NULL : intval($id);
		$this->app = \Slim\Slim::getInstance();
	}

	
	/**
	 * Now, here is the CRUD
	 */

    /**
     * create bill & glist
     *
     * @param  array    	$bill 		data for bill
     * @param  array    	$glist 		data for goods list
     * @return bool 
     */
	public function create_all($bill, $glist){
        $ret = $this->write('create', $bill, $glist);
        return $ret;
	}


	/**
     * update bill & glist
     *
     * @param  array    	$bill 		data for bill
     * @param  array    	$glist 		data for goods list
     * @return bool 
     */
	public function update_all($bill, $glist){
        //清空该bill的glist
	    if (False === $this->app->db->delete($this->tablename_glist, [$this->id_name => $this->id])){
            return False;
        } 
        //更新bill，重新批量插入glist
        if (False === $this->write('update', $bill, $glist)){
            return False;
        } 
        return True;
	}


	/**
     * read bill & glist by id
     *
     * @param  array     	$db_fields 	SQL:fields
     * @return array|False 	
     */
	public function read_all_by_id($db_fields = '*'){
        $ret = $this->read_by_id(Null, $db_fields);
        if(!$ret){
            error(1211);
        }
		$ret[0]['goods_list'] = $this->app->db->select($this->tablename_glist, '*', [$this->id_name => $this->id]);
        $ret[0]['outtax_amount'] = sprintf('%.2f', $ret[0]['amount'] - $ret[0]['tax_amount']);

        $gid_list = [];
        foreach($ret[0]['goods_list'] as $k=>$v){
            $ret[0]['goods_list'][$k]['outtax_price'] = fen2yuan($v['amount_price'] - $v['tax_price']);
            foreach($v as $key=>$val){
                if(in_array($key, $this->amount_data_glist)){
                    $ret[0]['goods_list'][$k][$key] = fen2yuan($ret[0]['goods_list'][$k][$key]);
                }
            }
            $gid_list[] = $v['gid'];
        }

        //获取当前库存
        $r_model = new Reserve();
        $cid = $this->app->Sneaker->cid;
        $r_res = $r_model -> get_reserve($cid, $ret[0][$this->sidname], $gid_list);
        foreach($ret[0]['goods_list'] as $key=>$val){
            $ret[0]['goods_list'][$key]['reserve'] = get_value($r_res, $val['gid'], 0);

            if($val['reserveid']){
                $rb_res = $this->app->db->select('r_reserve', '*', ['id'=>$val['reserveid']]);
                $batch = $rb_res[0]['batch'];
            }
            else{
                $batch = 0;
            }
            $ret[0]['goods_list'][$key]['batch'] = $batch;
        }

        $ret[0]['goods_list'] = Change::go($ret[0]['goods_list'], 'gbid', 'gbname', 'o_goods_brand');
        $ret[0]['goods_list'] = Change::go($ret[0]['goods_list'], 'gtid', 'gtname', 'o_company_goods_type');

        return $ret;
	}


    // Now, Utils 

	/**
     * DB:create & update
     *
     * @param  string    	$opt 		create / update
     * @param  array    	$bill 		data for bill
     * @param  array    	$glist 		data for goods list
     * @return int|False 	lastInsertId
     */
	private function write($opt, $bill, $glist){
        //单据表
        if($opt == 'create'){
            foreach($this->format_data as $key=>$val){
                if($val[0] == '*'){
                    $this->format_data[$key] = substr($val, 1);
                }
            }
        }
        foreach($bill as $key=>$val){
			if(!in_array($key, $this->format_data)){
				unset($bill[$key]);
			}
		}

        //glists
        foreach($this->format_data_glist as $key=>$val){
            if($val[0] == '*'){
                $this->format_data_glist[$key] = substr($val, 1);
            }
        }
        foreach($glist as $item=>$goods){
            foreach($goods as $key=>$val){
                if(!in_array($key, $this->format_data_glist)){
                    unset($glist[$item][$key]);
                }
                if(in_array($key, $this->amount_data_glist)){
                    $glist[$item][$key] = yuan2fen($glist[$item][$key]);
                }
            }
            //$glist[$item][$this->id_name] = isset($bill['id']) ? $bill['id'] : $this->id;
        }
        foreach($glist as &$val){
            ksort($val);
        }
        //db
        if ($bill && $glist){
            if ($opt == 'update'){
                $db_set['updatetime'] = date('Y-m-d H:i:s');
                $ret = $this->update_by_id($bill);
                $order_id = $this->id;
            } else {
                $bill['createtime'] = date('Y-m-d H:i:s');
                $ret = $this->create($bill);
                $order_id = $ret;
            }
            if ($ret === False) return False; //rollback
            foreach($glist as $item=>$goods){
                $glist[$item][$this->id_name] = $order_id;
            }
            $ret = $this->app->db->insert($this->tablename_glist, $glist);
            if ($ret === False){
                if (stripos($this->app->db->pdo->errorInfo()[2], 'Duplicate entry') === 0){ //记录已存在
                    error(1200);
                } else {
                    error(9900);
                }
            }
        }
		return $order_id;
	}

    public function get_sbind($glist, $cid, $sid){
        $new_glist = [];
        $cg_model = new CompanyGoods();
        $sg_model = new StoreGoods();
        foreach($glist as $goods){
            $gid = $goods['gid'];
            if($goods['gisbind']){
                //如果是捆绑商品，则进行捆绑关系转换
                $res = $this->app->db->select('r_bind', '*', [
                    'master_gid' => $gid
                ]);
                if(!$res){
                    error(1440);
                }
                $old_amount = 0;
                $temp_list = [];
                //第一次遍历，计算一些属性
                foreach($res as $key=>$val){
                    $gid = $val['slave_gid'];

                    $temp = [
                        'gid' => $gid,
                        'total' => $goods['total'] * $val['slave_count']
                    ];
                    $gres = $cg_model->read_one([
                        'gid' => $gid,
                        'in_cid' => $cid
                    ]);
                    if($gres){
                        $temp['gname'] = $gres['gname'];
                        $temp['gcode'] = $gres['gcode'];
                        $temp['gspec'] = $gres['gspec'];
                        $temp['gpyname'] = $gres['gpyname'];
                        $temp['gbarcode'] = $gres['gbarcode'];
                        $temp['gunit'] = $gres['gunit'];
                        $temp['gtax_rate'] = $gres['gtax_rate'];
                        $temp['gtid'] = $gres['gtid'];
                        $temp['gbid'] = $gres['gbid'];
                        $temp['gisbind'] = $gres['gisbind'];
                    }
                    else{
                        error(3003);
                    }
                    if(!$val['isfree']){
                        //如果不是赠品，计算价格加权
                        $sg_res = $sg_model->read_one([
                            'in_cid' => $cid,
                            'in_sid' => $sid,
                            'gid' => $gid
                        ]);
                        if(!$sg_res){
                            $price = $gres['in_price'];
                        }
                        else{
                            $price = $sg_res['in_price'];
                        }
                        $temp['unit_price'] = fen2yuan($price);
                        $temp['amount_price'] = fen2yuan($price*$temp['total']);
                        $tax = get_tax($temp['amount_price'], $temp['gtax_rate']);
                        $temp['tax_price'] = $tax['tax_price'];
                        $old_amount += $price*$temp['total'];
                    }
                    $temp_list[$key] = $temp;
                }
                $old_amount = fen2yuan($old_amount);

                //计算折扣
                if(get_value($goods, 'amount_price')){
                    $zk = $goods['amount_price']/$old_amount;
                    //第二次遍历，补充价格参数
                    foreach($res as $key=>$val){
                        if(!$val['isfree']){
                            $temp_list[$key]['unit_price'] *= $zk;
                            $temp_list[$key]['tax_price'] *= $zk;
                            $temp_list[$key]['amount_price'] *= $zk;
                        }
                        else{
                            //如果是赠品，价格全部设置为0
                            $temp_list[$key]['unit_price'] = '0.00';
                            $temp_list[$key]['tax_price'] = '0.00';
                            $temp_list[$key]['amount_price'] = '0.00';
                        }
                    }
                }

                foreach($temp_list as $val){
                    $new_glist[] = $val;
                }

            }
            else{
                $new_glist[] = $goods;
            }
        }
        return $new_glist;
    }

    public function my_hash($goods_list){
        $my_list = [];
        foreach($goods_list as $val){
            $my_list[$val['sn']] = [
                'gid' => $val['gid'],
                'total' => $val['total'],
                'unit_price' => yuan2fen($val['unit_price']),
                'amount_price' => yuan2fen($val['amount_price'])
            ];
        }
        ksort($my_list);
        $str = json_encode($my_list);
        $hash_str = strtolower(md5($str));
        return $hash_str;
    }

    public function get_sn($goods_list){
        $sn = 1;
        foreach($goods_list as $key=>$val){
            $goods_list[$key]['sn'] = $sn;
            $sn += 1;
        }
        return $goods_list;
    }

}



