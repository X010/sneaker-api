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

class FSellGoods extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['cid','sid','gid','gname','gcode','gbarcode','gunit','gspec','scname','py_name','date',
		'sell_total','sell_amount','return_total','return_amount','sell_cost_amount','return_cost_amount'];

	//需要分和元转换的金额字段
	protected $amount_data = ['sell_amount','return_amount','sell_cost_amount','return_cost_amount'];

	protected $search_data = ['gname','gcode','gbarcode','py_name'];
	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('f_sell_goods', $id);
	}

	//写入库日报
	public function write($cid, $sid, $gid, $data){
		$my_date = date('Y-m-d');
		$db_where = [
			'cid'=>$cid,
			'sid'=>$sid,
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
			$cg_model = new CompanyGoods();
			$cg_res = $cg_model->get_one_goods($cid, $gid);

			$db_set = [
				'cid'=>$cid,
				'sid'=>$sid,
				'date'=>$my_date,
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
		//sids,gtids,begin_date,end_date
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

		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 200);

		$count_sql = "select count(distinct gid) as val from `f_sell_goods` where ". $where_db;
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$all_count = $count_res[0]['val'];
		$all_page = intval($all_count/$page_num);
		if($all_count%$page_num!=0){
			$all_page ++;
		}

		$sql = "select gid,gname,gcode,gbarcode,gunit,gspec,sum(sell_total) as sell_total,sum(sell_amount) as sell_amount,sum(return_total) as return_total,";
		$sql .= "sum(return_amount) as return_amount,sum(sell_amount-return_amount) as real_amount,sum(sell_total-return_total) as real_total ".
			"from `f_sell_goods` where  ". $where_db;
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
			$result[] = [
				'gid'=>$val['gid'],
				'gname'=>$val['gname'],
				'gcode'=>$val['gcode'],
				'gbarcode'=>$val['gbarcode'],
				'gunit'=>$val['gunit'],
				'gspec'=>$val['gspec'],
				'sell_amount'=>fen2yuan($val['sell_amount']),
				'sell_total'=>$val['sell_total'],
				'return_amount'=>fen2yuan($val['return_amount']),
				'return_total'=>$val['return_total'],
				'real_amount'=>fen2yuan($val['real_amount']),
				'real_total'=>$val['real_total']
			];
		}

		return [
			'count'=>$all_count,
			'page_count'=>$all_page,
			'data'=>$result
		];
	}


}

