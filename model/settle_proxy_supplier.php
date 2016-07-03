<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * price
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class SettleProxySupplier extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*cid','*cname','*sid','*sname','*scid','*scname','uid','uname','cuid',
		'cuname','checktime','last_rest_total','last_rest_amount','current_sell_total','current_expect_total',
		'current_expect_amount','current_real_total','current_real_amount','current_after_discount_amount',
		'discount','current_rest_total','current_rest_amount','status','settle_date','last_settle_date',
		'pay_type','memo','current_sell_amount','negative_id'];
	
	//搜索字段
	protected $search_data = ['id'];

	//可排序的字段
	protected $order_data = ['id','status'];

	protected $amount_data = ['last_rest_amount','current_expect_amount','current_real_amount','current_after_discount_amount',
		'current_rest_amount','current_sell_amount'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_settle_proxy_supplier', $id);
	}

	public function my_create($data){

		$sid = $data['sid'];
		$cid = $data['cid'];
		$scid = $data['scid'];
		$current_settle_date = $data['date'];
		$discount = $data['discount'];

		$goods_list = json_decode($data['goods_list'], True);
		$gids = [];
		foreach($goods_list as $val){
			$gids[] = $val['gid'];
		}
		$cg_model = new CompanyGoods();
		$cg_res = $cg_model->read_list_nopage([
			'in_cid'=>$cid,
			'gid'=>$gids
		]);
		$gdata = [];
		foreach($cg_res as $val){
			$gdata[$val['gid']] = $val;
		}

		$result = [];
		//初始化要插入的信息
		foreach($goods_list as $val){
			$gid = $val['gid'];
			$temp = get_value($gdata, $gid);
			if(!$temp){
				error(1423);
			}

			$result[$gid] = [
				'gid'=>$gid,
				'gcode'=>$temp['gcode'],
				'gbarcode'=>$temp['gbarcode'],
				'gname'=>$temp['gname'],
				'gunit'=>$temp['gunit'],
				'gspec'=>$temp['gspec'],
				'gtax_rate'=>$temp['gtax_rate'],
				'last_rest_total'=>0,
				'last_rest_amount'=>0,
				'current_sell_total'=>0,
				'current_sell_amount'=>0,
				'current_expect_total'=>0,
				'proxy_amount'=>0,
				'current_expect_amount'=>0,
				'current_real_total'=>$val['current_real_total'],
				'current_real_amount'=>0,
				'current_after_discount_amount'=>0,
				'discount'=>$discount,
				'current_rest_total'=>0,
				'current_rest_amount'=>0
			];
		}

		//找到上一次的代销结算单，以计算上期结余和上期时间
		$last_res = $this->read_one([
			'sid'=>$sid,
			'cid'=>$cid,
			'scid'=>$scid,
			'status'=>2
		]);
		if($last_res){
			$last_settle_id = $last_res['id'];
			$last_settle_date = $last_res['settle_date'];  //上期结算时间
			$spg_model = new SettleProxyGlist();
			$spg_res = $spg_model->read_list_nopage([
				'settle_id'=>$last_settle_id
			]);
			//写入上期结余
			foreach($spg_res as $val){
				$gid = $val['gid'];
				$result[$gid]['last_rest_total'] = $val['current_rest_total'];
				$result[$gid]['last_rest_amount'] = $val['current_rest_amount'];
				if($val['current_rest_total']){
					$result[$gid]['proxy_amount'] = format_yuan($val['current_rest_amount']/$val['current_rest_total']);
				}
			}
		}
		else{
			//如果没有找到上期结算单，说明是第一次，自动写一个比较早的时间
			$last_settle_date = '2016-01-01';
		}

		if($last_settle_date>$current_settle_date){
			error(3411);
		}
		$data['settle_date'] = $current_settle_date;
		$data['last_settle_date'] = $last_settle_date;

		//获取本期销售额，从供应商销售日报读取
		$sql = "select gid,sum(sell_total-return_total) as sell_total,sum(sell_amount-return_amount) as sell_amount,".
			"sum(sell_cost_amount-return_cost_amount) as sell_cost_amount from `f_sell_supplier` where cid=$cid and ".
			"sid=$sid and scid=$scid and date<='$current_settle_date' and date>'$last_settle_date' and gid in (". implode(',',$gids).") group by gid";

		$fss_res = $this->app->db->query($sql)->fetchAll();
		foreach($fss_res as $val){
			$gid = $val['gid'];
			$result[$gid]['current_sell_total'] = $val['sell_total'];
			$result[$gid]['current_sell_amount'] = fen2yuan($val['sell_amount']);
			if($val['sell_total']){
				$result[$gid]['proxy_amount'] = fen2yuan($val['sell_cost_amount']/$val['sell_total']);
			}
		}

		foreach($result as $gid=>$val){
			//应结数量和金额
			$result[$gid]['current_expect_total'] = $val['last_rest_total']+$val['current_sell_total'];
			$result[$gid]['current_expect_amount'] = sprintf('%.2f', $result[$gid]['current_expect_total']*$val['proxy_amount']);
			//实结金额和折扣后金额
			$result[$gid]['current_real_amount'] = sprintf('%.2f', $result[$gid]['current_real_total']*$val['proxy_amount']);
			$result[$gid]['current_after_discount_amount'] = sprintf('%.2f', $result[$gid]['current_real_amount']*$val['discount']);
			//本期结余
			$result[$gid]['current_rest_total'] = $result[$gid]['current_expect_total']-$val['current_real_total'];
			$result[$gid]['current_rest_amount'] = sprintf('%.2f', $result[$gid]['current_rest_total']*$val['proxy_amount']);
		}

		$last_rest_total = 0;
		$last_rest_amount = 0;
		$current_sell_total = 0;
		$current_sell_amount = 0;
		$current_expect_total = 0;
		$current_expect_amount = 0;
		$current_real_total = 0;
		$current_real_amount = 0;
		$current_after_discount_amount = 0;
		$current_rest_total = 0;
		$current_rest_amount = 0;
		//开始计算总表总计数据
		foreach($result as $gid=>$val){
			$last_rest_total += $val['last_rest_total'];
			$last_rest_amount = price_add($last_rest_amount, $val['last_rest_amount']);
			$current_sell_total += $val['current_sell_total'];
			$current_sell_amount = price_add($current_sell_amount, $val['current_sell_amount']);
			$current_expect_total += $val['current_expect_total'];
			$current_expect_amount = price_add($current_expect_amount, $val['current_expect_amount']);
			$current_real_total += $val['current_real_total'];
			$current_real_amount = price_add($current_real_amount, $val['current_real_amount']);
			$current_after_discount_amount = price_add($current_after_discount_amount, $val['current_after_discount_amount']);
			$current_rest_total += $val['current_rest_total'];
			$current_rest_amount = price_add($current_rest_amount, $val['current_rest_amount']);
		}
		$data['last_rest_total'] = $last_rest_total;
		$data['last_rest_amount'] = $last_rest_amount;
		$data['current_sell_total'] = $current_sell_total;
		$data['current_sell_amount'] = $current_sell_amount;
		$data['current_expect_total'] = $current_expect_total;
		$data['current_expect_amount'] = $current_expect_amount;
		$data['current_real_total'] = $current_real_total;
		$data['current_real_amount'] = $current_real_amount;
		$data['current_after_discount_amount'] = $current_after_discount_amount;
		$data['current_rest_total'] = $current_rest_total;
		$data['current_rest_amount'] = $current_rest_amount;

		//开始事务，写入数据
		start_action();
		$settle_id = $this->create($data);

		foreach($result as $key=>$val){
			$result[$key]['settle_id'] = $settle_id;
		}
		$spg_model = new SettleProxyGlist();
		$result = dict2list($result);
		$spg_model->create_batch($result, 0);

		return $settle_id;
	}

	//查询供应商代销商品明细
	public function read_proxy_goods($data){
		$cid = $data['cid'];
		$sid = $data['sid'];
		$scid = $data['scid'];
		$current_settle_date = $data['date'];

		$cg_model = new CompanyGoods();
		//第一步，先找到公司下的所有代销商品
		$cg_res = $cg_model->read_list_nopage([
			'in_cid'=>$cid,
			'business'=>2
		]);
		//再找到这些代销商品，哪些是属于这个供应商的
		$gids = [];
		$gdata = [];
		foreach($cg_res as $val){
			$gids[] = $val['gid'];
			$gdata[$val['gid']] = $val;
		}
		$gs_model = new GoodsSupplier();
		$gs_res = $gs_model->read_list_nopage([
			'cid'=>$cid,
			'scid'=>$scid,
			'gid'=>$gids
		]);
		//最终得到这个供应商下的所有代销商品（目前）
		$gids2 = [];
		foreach($gs_res as $val){
			$gids2[] = $val['gid'];
		}
		if(!$gids2){
			return [];
		}

		$result = [];
		//初始化要返回的信息
		foreach($gids2 as $gid){
			$temp = $gdata[$gid];
			$result[$gid] = [
				'gid'=>$gid,
				'gcode'=>$temp['gcode'],
				'gbarcode'=>$temp['gbarcode'],
				'gname'=>$temp['gname'],
				'gunit'=>$temp['gunit'],
				'gspec'=>$temp['gspec'],
				'gtax_rate'=>$temp['gtax_rate'],
				'last_rest_total'=>0,
				'last_rest_amount'=>'0.00',
				'current_sell_total'=>0,
				'current_sell_amount'=>'0.00',
				'current_expect_total'=>0,
				'proxy_amount'=>'0.00',
				'current_expect_amount'=>'0.00'
			];
		}

		//找到上一次的代销结算单，以计算上期结余和上期时间
		$last_res = $this->read_one([
			'sid'=>$sid,
			'cid'=>$cid,
			'scid'=>$scid,
			'status'=>2
		]);
		if($last_res){
			$last_settle_id = $last_res['id'];
			$last_settle_date = $last_res['settle_date'];  //上期结算时间
			$spg_model = new SettleProxyGlist();
			$spg_res = $spg_model->read_list_nopage([
				'settle_id'=>$last_settle_id
			]);
			//写入上期结余
			foreach($spg_res as $val){
				$gid = $val['gid'];
				$result[$gid]['last_rest_total'] = $val['current_rest_total'];
				$result[$gid]['last_rest_amount'] = $val['current_rest_amount'];
				if($val['current_rest_total']){
					$result[$gid]['proxy_amount'] = format_yuan($val['current_rest_amount']/$val['current_rest_total']);
				}
			}
		}
		else{
			//如果没有找到上期结算单，说明是第一次，自动写一个比较早的时间
			$last_settle_date = '2016-01-01';
		}
		if($last_settle_date>$current_settle_date){
			error(3411);
		}

		//获取本期销售额，从供应商销售日报读取
		$sql = "select gid,sum(sell_total-return_total) as sell_total,sum(sell_amount-return_amount) as sell_amount,".
			"sum(sell_cost_amount-return_cost_amount) as sell_cost_amount from `f_sell_supplier` where cid=$cid and ".
			"sid=$sid and scid=$scid and date<='$current_settle_date' and date>'$last_settle_date' and gid in (". implode(',',$gids2).") group by gid";

		$fss_res = $this->app->db->query($sql)->fetchAll();
		foreach($fss_res as $val){
			$gid = $val['gid'];
			$result[$gid]['current_sell_total'] = $val['sell_total'];
			$result[$gid]['current_sell_amount'] = fen2yuan($val['sell_amount']);
			if($val['sell_total']){
				$result[$gid]['proxy_amount'] = fen2yuan($val['sell_cost_amount']/$val['sell_total']);
			}
		}

		foreach($result as $gid=>$val){
			$result[$gid]['current_expect_total'] = $val['last_rest_total']+$val['current_sell_total'];
			$result[$gid]['current_expect_amount'] = sprintf('%.2f', $result[$gid]['current_expect_total']*$val['proxy_amount']);
		}

		//获取当前库存
		$r_model = new Reserve();
		$r_res = $r_model -> get_reserve_amount($cid, $sid, $gids2);
		foreach($result as $key=>$val){
			$temp = get_value($r_res, $val['gid'], []);
			$result[$key]['reserve'] = get_value($temp, 'total', 0);
			$result[$key]['reserve_amount'] = get_value($temp, 'amount', '0.00');
		}

		$result = dict2list($result);
		return $result;
	}

	/**
	 * 查询代销结算单详情
	 *
	 * @return array
	 */
	public function my_read(){
		$res = $this->read_by_id();

		$spg_model = new SettleProxyGlist();
		$spg_res = $spg_model->read_list_nopage([
			'settle_id'=>$this->id,
			'orderby'=>'id^asc'
		]);

		$gids = [];
		foreach($spg_res as $val){
			$gids[] = $val['gid'];
		}

		$cid = $this->app->Sneaker->cid;
		$sid = $res[0]['sid'];

		//获取当前库存
		$r_model = new Reserve();
		$r_res = $r_model -> get_reserve_amount($cid, $sid, $gids);
		foreach($spg_res as $key=>$val){
			$temp = get_value($r_res, $val['gid'], []);
			$spg_res[$key]['reserve'] = get_value($temp, 'total', 0);
			$spg_res[$key]['reserve_amount'] = get_value($temp, 'amount', '0.00');
		}

		$res[0]['goods_list'] = $spg_res;

		$tax_group = $spg_model->get_tax_group($this->id);
		$res[0]['tax_group'] = $tax_group;

		return $res;

	}

	public function my_update($data){
		$sid = $data['sid'];
		$cid = $data['cid'];
		$scid = $data['scid'];
		$current_settle_date = $data['date'];
		$discount = $data['discount'];

		$goods_list = json_decode($data['goods_list'], True);
		$gids = [];
		foreach($goods_list as $val){
			$gids[] = $val['gid'];
		}
		$cg_model = new CompanyGoods();
		$cg_res = $cg_model->read_list_nopage([
			'in_cid'=>$cid,
			'gid'=>$gids
		]);
		$gdata = [];
		foreach($cg_res as $val){
			$gdata[$val['gid']] = $val;
		}

		$result = [];
		//初始化要插入的信息
		foreach($goods_list as $val){
			$gid = $val['gid'];
			$temp = get_value($gdata, $gid);
			if(!$temp){
				error(1423);
			}

			$result[$gid] = [
				'gid'=>$gid,
				'gcode'=>$temp['gcode'],
				'gbarcode'=>$temp['gbarcode'],
				'gname'=>$temp['gname'],
				'gunit'=>$temp['gunit'],
				'gspec'=>$temp['gspec'],
				'gtax_rate'=>$temp['gtax_rate'],
				'last_rest_total'=>0,
				'last_rest_amount'=>0,
				'current_sell_total'=>0,
				'current_sell_amount'=>0,
				'current_expect_total'=>0,
				'proxy_amount'=>0,
				'current_expect_amount'=>0,
				'current_real_total'=>$val['current_real_total'],
				'current_real_amount'=>0,
				'current_after_discount_amount'=>0,
				'discount'=>$discount,
				'current_rest_total'=>0,
				'current_rest_amount'=>0
			];
		}

		//找到上一次的代销结算单，以计算上期结余和上期时间
		$last_res = $this->read_one([
			'sid'=>$sid,
			'cid'=>$cid,
			'scid'=>$scid,
			'status'=>2
		]);
		if($last_res){
			$last_settle_id = $last_res['id'];
			$last_settle_date = $last_res['settle_date'];  //上期结算时间
			$spg_model = new SettleProxyGlist();
			$spg_res = $spg_model->read_list_nopage([
				'settle_id'=>$last_settle_id
			]);
			//写入上期结余
			foreach($spg_res as $val){
				$gid = $val['gid'];
				$result[$gid]['last_rest_total'] = $val['current_rest_total'];
				$result[$gid]['last_rest_amount'] = $val['current_rest_amount'];
				if($val['current_rest_total']){
					$result[$gid]['proxy_amount'] = format_yuan($val['current_rest_amount']/$val['current_rest_total']);
				}
			}
		}
		else{
			//如果没有找到上期结算单，说明是第一次，自动写一个比较早的时间
			$last_settle_date = '2016-01-01';
		}
		if($last_settle_date>$current_settle_date){
			error(3411);
		}
		$data['settle_date'] = $current_settle_date;
		$data['last_settle_date'] = $last_settle_date;

		//获取本期销售额，从供应商销售日报读取
		$sql = "select gid,sum(sell_total-return_total) as sell_total,sum(sell_amount-return_amount) as sell_amount,".
			"sum(sell_cost_amount-return_cost_amount) as sell_cost_amount from `f_sell_supplier` where cid=$cid and ".
			"sid=$sid and scid=$scid and date<='$current_settle_date' and date>'$last_settle_date' group by gid";

		$fss_res = $this->app->db->query($sql)->fetchAll();
		foreach($fss_res as $val){
			$gid = $val['gid'];
			$result[$gid]['current_sell_total'] = $val['sell_total'];
			$result[$gid]['current_sell_amount'] = fen2yuan($val['sell_amount']);
			if($val['sell_total']){
				$result[$gid]['proxy_amount'] = fen2yuan($val['sell_cost_amount']/$val['sell_total']);
			}
		}

		foreach($result as $gid=>$val){
			//应结数量和金额
			$result[$gid]['current_expect_total'] = $val['last_rest_total']+$val['current_sell_total'];
			$result[$gid]['current_expect_amount'] = sprintf('%.2f', $result[$gid]['current_expect_total']*$val['proxy_amount']);
			//实结金额和折扣后金额
			$result[$gid]['current_real_amount'] = sprintf('%.2f', $result[$gid]['current_real_total']*$val['proxy_amount']);
			$result[$gid]['current_after_discount_amount'] = sprintf('%.2f', $result[$gid]['current_real_amount']*$val['discount']);
			//本期结余
			$result[$gid]['current_rest_total'] = $result[$gid]['current_expect_total']-$val['current_real_total'];
			$result[$gid]['current_rest_amount'] = sprintf('%.2f', $result[$gid]['current_rest_total']*$val['proxy_amount']);
		}

		$last_rest_total = 0;
		$last_rest_amount = 0;
		$current_sell_total = 0;
		$current_sell_amount = 0;
		$current_expect_total = 0;
		$current_expect_amount = 0;
		$current_real_total = 0;
		$current_real_amount = 0;
		$current_after_discount_amount = 0;
		$current_rest_total = 0;
		$current_rest_amount = 0;
		//开始计算总表总计数据
		foreach($result as $gid=>$val){
			$last_rest_total += $val['last_rest_total'];
			$last_rest_amount = price_add($last_rest_amount, $val['last_rest_amount']);
			$current_sell_total += $val['current_sell_total'];
			$current_sell_amount = price_add($current_sell_amount, $val['current_sell_amount']);
			$current_expect_total += $val['current_expect_total'];
			$current_expect_amount = price_add($current_expect_amount, $val['current_expect_amount']);
			$current_real_total += $val['current_real_total'];
			$current_real_amount = price_add($current_real_amount, $val['current_real_amount']);
			$current_after_discount_amount = price_add($current_after_discount_amount, $val['current_after_discount_amount']);
			$current_rest_total += $val['current_rest_total'];
			$current_rest_amount = price_add($current_rest_amount, $val['current_rest_amount']);
		}
		$data['last_rest_total'] = $last_rest_total;
		$data['last_rest_amount'] = $last_rest_amount;
		$data['current_sell_total'] = $current_sell_total;
		$data['current_sell_amount'] = $current_sell_amount;
		$data['current_expect_total'] = $current_expect_total;
		$data['current_expect_amount'] = $current_expect_amount;
		$data['current_real_total'] = $current_real_total;
		$data['current_real_amount'] = $current_real_amount;
		$data['current_after_discount_amount'] = $current_after_discount_amount;
		$data['current_rest_total'] = $current_rest_total;
		$data['current_rest_amount'] = $current_rest_amount;

		//开始事务，写入数据
		start_action();

		$this->update_by_id($data);

		foreach($result as $key=>$val){
			$result[$key]['settle_id'] = $this->id;
		}
		$spg_model = new SettleProxyGlist();
		$result = dict2list($result);
		$spg_model->delete([
			'settle_id'=>$this->id
		]);
		$spg_model->create_batch($result, 0);

		return $this->id;
	}

	public function my_check($data, $type){
		if($type == 'create'){
			$this->id = $this->my_create($data);
		}
		elseif($type == 'update'){
			$this->my_update($data);
		}

		return $this->id;
	}

	/**
	 * 权限检测
	 *
	 * @param int $id 单据ID
	 * @param int $status 状态，为0时不检测
	 * @return array 字段列表
	 */
	public function my_power($id, $status){
		$res = $this->read_by_id($id);
		if(!$res){
			error(3402);
		}
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(3403);
		}
		if($status && $res[0]['status'] != $status){
			error(3404);
		}
		return $res[0];
	}

}

