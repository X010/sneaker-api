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

class FSellCustomer extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['cid','sid','ccid','ccname','py_name','gid','gname','gcode','gbarcode','gunit','gspec',
		'gpy_name','date','sell_total','sell_amount','return_total','return_amount', 'sell_cost_amount','return_cost_amount'];

	//需要分和元转换的金额字段
	protected $amount_data = ['sell_amount','return_amount','sell_cost_amount','return_cost_amount'];

	protected $search_data = ['scname','py_name'];
	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('f_sell_customer', $id);
	}

	//写入库日报
	public function write($cid, $sid, $ccid, $ccname, $gid, $data){
		$my_date = date('Y-m-d');
		$db_where = [
			'cid'=>$cid,
			'sid'=>$sid,
			'ccid'=>$ccid,
			'gid'=>$gid,
			'date'=>$my_date
		];
		$res = $this->has($db_where);
		$sell_total = get_value($data, 'sell_total', 0);
		$sell_amount = get_value($data, 'sell_amount', 0);
		$return_total = get_value($data, 'return_total', 0);
		$return_amount = get_value($data, 'return_amount', 0);
		$sell_cost_amount = get_value($data, 'sell_cost_amount', 0);
		$return_cost_amount = get_value($data, 'return_cost_amount', 0);
		if($res){
			//如果已经有当天日报，则进行修改
			$db_set = [
				'sell_total[+]'=>$sell_total,
				'sell_amount[+]'=>$sell_amount,
				'return_total[+]'=>$return_total,
				'return_amount[+]'=>$return_amount,
				'sell_cost_amount[+]'=>$sell_cost_amount,
				'return_cost_amount[+]'=>$return_cost_amount,
			];
			$this->app->db->update($this->tablename, $db_set, ['AND'=>$db_where]);
		}
		else{
			//如果没有，则新建
			$py_name = '';
			if($ccname){
				require_once 'core/pinyin.php';
				$py_name = pinyin($ccname);
			}
			$cg_model = new CompanyGoods();
			$cg_res = $cg_model->get_one_goods($cid, $gid);

			$db_set = [
				'cid'=>$cid,
				'sid'=>$sid,
				'date'=>$my_date,
				'ccid'=>$ccid,
				'ccname'=>$ccname,
				'py_name'=>$py_name,
				'gid'=>$gid,
				'gname'=>$cg_res['gname'],
				'gcode'=>$cg_res['gcode'],
				'gbarcode'=>$cg_res['gbarcode'],
				'gunit'=>$cg_res['gunit'],
				'gspec'=>$cg_res['gspec'],
				'gpy_name'=>$cg_res['gpyname'],
				'sell_total'=>$sell_total,
				'sell_amount'=>$sell_amount,
				'return_total'=>$return_total,
				'return_amount'=>$return_amount,
				'sell_cost_amount'=>$sell_cost_amount,
				'return_cost_amount'=>$return_cost_amount,
			];
			$this->app->db->insert($this->tablename, $db_set);
		}
		return True;
	}

	public function my_form($data){
		//sids,cctypes,begin_date,end_date,suid
		$cctypes = get_value($data, 'cctypes');
		$suid = get_value($data, 'suid');
		$cid = $data['cid'];
		$ccids = [];
		$c_data = [];
		if($cctypes){
			$c_data['cctype'] = explode(',',$cctypes);
		}
		if($suid){
			$c_data['suid'] = $suid;
		}
		if($c_data){
			$c_data['cid'] = $cid;
			$c_model = new Customer();
			$c_res = $c_model->read_list_nopage($c_data);
			foreach($c_res as $val){
				$ccids[] = $val['ccid'];
			}
			if(!$ccids){
				$ccids[] = -1;
			}
		}

		$where_db = " cid=$cid";
		if(get_value($data, 'sids')){
			$where_db .= " and sid in (". $data['sids']. ")";
		}
		if($ccids){
			$where_db .= " and ccid in (". implode(',', $ccids). ")";
		}
		if(get_value($data, 'begin_date')){
			$where_db .= " and date>='". $data['begin_date']. "'";
		}
		if(get_value($data, 'end_date')){
			$where_db .= " and date<='". $data['end_date']. "'";
		}

		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 200);

		$count_sql = "select count(distinct ccid) as val,sum(sell_total) as sell_total,sum(sell_amount) as sell_amount,sum(sell_cost_amount) as sell_cost_amount,".
			"sum(sell_amount-sell_cost_amount) as sell_profit_amount, sum(return_total) as return_total,sum(return_amount) as return_amount,".
			"sum(return_cost_amount) as return_cost_amount,sum(return_amount-return_cost_amount) as return_profit_amount from `f_sell_customer` where ". $where_db;
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$all_count = $count_res[0]['val'];

		if($count_res[0]['sell_amount']){
			$sell_profit_percent = num2per($count_res[0]['sell_profit_amount']/$count_res[0]['sell_amount']);
		}
		else{
			$sell_profit_percent = num2per(0);
		}

		$add_up = [
			'sell_amount'=>fen2yuan($count_res[0]['sell_amount']),
			'sell_cost_amount'=>fen2yuan($count_res[0]['sell_cost_amount']),
			'sell_profit_amount'=>fen2yuan($count_res[0]['sell_profit_amount']),
			'sell_total'=>$count_res[0]['sell_total'],
			'return_amount'=>fen2yuan($count_res[0]['return_amount']),
			'return_cost_amount'=>fen2yuan($count_res[0]['return_cost_amount']),
			'return_profit_amount'=>fen2yuan($count_res[0]['return_profit_amount']),
			'return_total'=>$count_res[0]['return_total'],
			'sell_profit_percent'=>$sell_profit_percent
		];
		$all_page = intval($all_count/$page_num);
		if($all_count%$page_num!=0){
			$all_page ++;
		}

		$sql = "select ccid,ccname,sum(sell_total) as sell_total,sum(sell_amount) as sell_amount,sum(sell_cost_amount) as sell_cost_amount,".
			"sum(sell_amount-sell_cost_amount) as sell_profit_amount, sum(return_total) as return_total,sum(return_amount) as return_amount,".
			"sum(return_cost_amount) as return_cost_amount,sum(return_amount-return_cost_amount) as return_profit_amount ".
			"from `f_sell_customer` where  ". $where_db;
		$orderby = get_value($data, 'orderby');
		if($orderby){
			$orderby = str_replace('^', ' ', $orderby);
		}
		else{
			$orderby = 'sell_amount desc';
		}

		$sql .= ' group by ccid order by '. $orderby;

		$start_count = ($page - 1) * $page_num;
		$sql .= ' limit '. $start_count. ','. $page_num;
		$r_res = $this->app->db->query($sql)->fetchAll();

		$result = [];
		foreach($r_res as $val){
			if($val['sell_amount']){
				$sell_profit_percent = num2per($val['sell_profit_amount']/$val['sell_amount']);
			}
			else{
				$sell_profit_percent = num2per(0);
			}

			if($val['ccid'] == -1){
				$val['ccname'] = '临时客户';
			}

			$result[] = [
				'ccid'=>$val['ccid'],
				'ccname'=>$val['ccname'],
				'sell_total'=>$val['sell_total'],
				'sell_amount'=>fen2yuan($val['sell_amount']),
				'sell_cost_amount'=>fen2yuan($val['sell_cost_amount']),
				'sell_profit_amount'=>fen2yuan($val['sell_profit_amount']),
				'sell_profit_percent'=>$sell_profit_percent,
				'return_total'=>$val['return_total'],
				'return_amount'=>fen2yuan($val['return_amount']),
				'return_cost_amount'=>fen2yuan($val['return_cost_amount']),
				'return_profit_amount'=>fen2yuan($val['return_profit_amount'])
			];
		}

		return [
			'count'=>$all_count,
			'page_count'=>$all_page,
			'data'=>$result,
			'add_up'=>$add_up
		];
	}

	public function my_goods_form($data){
		//sids,begin_date,end_date
		//ccid

		$cid = $data['cid'];
		$gtids = get_value($data, 'gtids');
		$gids = [];
		if($gtids){
			$cgt_model = new CompanyGoodsType();
			$gtid_list = $cgt_model->get_ids_by_fids($gtids);

			$cg_model = new CompanyGoods();
			$cg_res = $cg_model->read_list_nopage([
				'in_cid'=>$cid,
				'gtid'=>$gtid_list
			]);

			foreach($cg_res as $val){
				$gids[] = $val['gid'];
			}
		}

		$where_db = " cid=$cid";
		if(get_value($data, 'sids')){
			$where_db .= " and sid in (". $data['sids']. ")";
		}
		if($gids){
			$where_db .= " and gid in (". implode(',', $gids). ")";
		}
		if(get_value($data, 'begin_date')){
			$where_db .= " and date>='". $data['begin_date']. "'";
		}
		if(get_value($data, 'end_date')){
			$where_db .= " and date<='". $data['end_date']. "'";
		}
		if(get_value($data, 'ccid')){
			$where_db .= " and ccid=". $data['ccid'];
		}

		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 200);

		$count_sql = "select count(distinct gid) as val,sum(sell_total) as sell_total,sum(sell_amount) as sell_amount,sum(sell_cost_amount) as sell_cost_amount,".
			"sum(sell_amount-sell_cost_amount) as sell_profit_amount, sum(return_total) as return_total,sum(return_amount) as return_amount,".
			"sum(return_cost_amount) as return_cost_amount,sum(return_amount-return_cost_amount) as return_profit_amount from `f_sell_customer` where ". $where_db;
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$all_count = $count_res[0]['val'];
		if($count_res[0]['sell_amount']){
			$sell_profit_percent = num2per($count_res[0]['sell_profit_amount']/$count_res[0]['sell_amount']);
		}
		else{
			$sell_profit_percent = num2per(0);
		}
		$add_up = [
			'sell_amount'=>fen2yuan($count_res[0]['sell_amount']),
			'sell_cost_amount'=>fen2yuan($count_res[0]['sell_cost_amount']),
			'sell_profit_amount'=>fen2yuan($count_res[0]['sell_profit_amount']),
			'sell_total'=>$count_res[0]['sell_total'],
			'return_amount'=>fen2yuan($count_res[0]['return_amount']),
			'return_cost_amount'=>fen2yuan($count_res[0]['return_cost_amount']),
			'return_profit_amount'=>fen2yuan($count_res[0]['return_profit_amount']),
			'return_total'=>$count_res[0]['return_total'],
			'sell_profit_percent'=>$sell_profit_percent
		];
		$all_page = intval($all_count/$page_num);
		if($all_count%$page_num!=0){
			$all_page ++;
		}

		$sql = "select gid,gname,gcode,gbarcode,gunit,gspec,sum(sell_total) as sell_total,sum(sell_amount) as sell_amount,sum(sell_cost_amount) as sell_cost_amount,".
			"sum(sell_amount-sell_cost_amount) as sell_profit_amount, sum(return_total) as return_total,sum(return_amount) as return_amount,".
			"sum(return_cost_amount) as return_cost_amount,sum(return_amount-return_cost_amount) as return_profit_amount ".
			"from `f_sell_customer` where  ". $where_db;

		$orderby = get_value($data, 'orderby');
		if($orderby){
			$orderby = str_replace('^', ' ', $orderby);
		}
		else{
			$orderby = 'sell_amount desc';
		}

		$sql .= ' group by gid order by '. $orderby;

		$start_count = ($page - 1) * $page_num;
		$sql .= ' limit '. $start_count. ','. $page_num;
		$r_res = $this->app->db->query($sql)->fetchAll();

		$result = [];
		foreach($r_res as $val){
			if($val['sell_amount']){
				$sell_profit_percent = num2per($val['sell_profit_amount']/$val['sell_amount']);
			}
			else{
				$sell_profit_percent = num2per(0);
			}
			$result[] = [
				'gid'=>$val['gid'],
				'gname'=>$val['gname'],
				'gcode'=>$val['gcode'],
				'gbarcode'=>$val['gbarcode'],
				'gunit'=>$val['gunit'],
				'gspec'=>$val['gspec'],
				'sell_total'=>$val['sell_total'],
				'sell_amount'=>fen2yuan($val['sell_amount']),
				'sell_cost_amount'=>fen2yuan($val['sell_cost_amount']),
				'sell_profit_amount'=>fen2yuan($val['sell_profit_amount']),
				'sell_profit_percent'=>$sell_profit_percent,
				'return_total'=>$val['return_total'],
				'return_amount'=>fen2yuan($val['return_amount']),
				'return_cost_amount'=>fen2yuan($val['return_cost_amount']),
				'return_profit_amount'=>fen2yuan($val['return_profit_amount'])
			];
		}

		return [
			'count'=>$all_count,
			'page_count'=>$all_page,
			'data'=>$result,
			'add_up'=>$add_up
		];
	}

	public function my_form_balance($data){
		//date
		$cid = $data['cid'];
		$date = $data['date'];
		$where_db = " cid=$cid and date='$date'";

		//找出所有的仓库，进行日对账
		$s_model = new Store();
		$s_res = $s_model->read_list_nopage([
			'cid'=>$cid
		]);
		$sids = [];
		$snames = [];
		foreach($s_res as $val){
			$sids[] = $val['id'];
			$snames[$val['id']] = $val['name'];
		}

		$sql = "select sid,sum(sell_total) as sell_total,sum(sell_amount) as sell_amount,sum(return_total) as return_total,";
		$sql .= "sum(return_amount) as return_amount,sum(sell_cost_amount) as sell_cost_amount,sum(return_cost_amount) as return_cost_amount from `f_sell_customer` where  ". $where_db;
		$sql .= ' group by sid';

		$r_res = $this->app->db->query($sql)->fetchAll();

		$result = [];
		$add_up = [
			'sell_amount'=>0,
			'sell_cost_amount'=>0,
			'sell_profit_amount'=>0,
			'sell_total'=>0,
			'return_amount'=>0,
			'return_cost_amount'=>0,
			'return_profit_amount'=>0,
			'return_total'=>0,
		];
		foreach($r_res as $val){

			$sell_amount = fen2yuan($val['sell_amount']);
			$sell_cost_amount = fen2yuan($val['sell_cost_amount']);
			$sell_profit_amount = fen2yuan($val['sell_amount']-$val['sell_cost_amount']);
			if(yuan2fen($sell_amount)){
				$sell_profit_percent = num2per($sell_profit_amount/$sell_amount);
			}
			else{
				$sell_profit_percent = num2per(0);
			}


			$return_amount = fen2yuan($val['return_amount']);
			$return_cost_amount = fen2yuan($val['return_cost_amount']);
			$return_profit_amount = fen2yuan($val['return_amount']-$val['return_cost_amount']);

			$result[] = [
				'sid'=>$val['sid'],
				'sname'=>$snames[$val['sid']],
				'sell_amount'=>$sell_amount,
				'sell_cost_amount'=>$sell_cost_amount,
				'sell_profit_amount'=>$sell_profit_amount,
				'sell_total'=>$val['sell_total'],
				'sell_profit_percent'=>$sell_profit_percent,
				'return_amount'=>$return_amount,
				'return_cost_amount'=>$return_cost_amount,
				'return_profit_amount'=>$return_profit_amount,
				'return_total'=>$val['return_total'],
			];
			$add_up['sell_amount'] = price_add($add_up['sell_amount'],$sell_amount);
			$add_up['sell_cost_amount'] = price_add($add_up['sell_cost_amount'],$sell_cost_amount);
			$add_up['sell_profit_amount'] = price_add($add_up['sell_profit_amount'],$sell_profit_amount);
			$add_up['sell_total'] += $val['sell_total'];
			$add_up['return_amount'] = price_add($add_up['return_amount'],$return_amount);
			$add_up['return_cost_amount'] = price_add($add_up['return_cost_amount'],$return_cost_amount);
			$add_up['return_profit_amount'] = price_add($add_up['return_profit_amount'],$return_profit_amount);
			$add_up['return_total'] += $val['return_total'];
		}

		if(yuan2fen($add_up['sell_amount'])){
			$sell_profit_percent = num2per($add_up['sell_profit_amount']/$add_up['sell_amount']);
		}
		else{
			$sell_profit_percent = num2per(0);
		}
		$add_up['sell_profit_percent'] = $sell_profit_percent;

		return [
			'data'=>$result,
			'add_up'=>$add_up
		];
	}


}

