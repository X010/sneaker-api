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

class FStockIn extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['cid','sid','gid','gname','gcode','gbarcode','gspec','gunit','gtid','gtname','date','buy_total',
		'buy_amount','return_total','return_amount', 'transfer_total','transfer_amount'];

	//需要分和元转换的金额字段
	protected $amount_data = ['buy_amount','return_amount','transfer_amount'];

	protected $search_data = ['gname','gcode','gbarcode'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('f_stock_in', $id);
	}

	//写入库日报
	public function write($cid, $sid, $gid, $data){
		$cg_model = new CompanyGoods();
		$cgt_model = new CompanyGoodsType();
		$my_date = date('Y-m-d');
		$db_where = [
			'AND'=>[
				'cid'=>$cid,
				'sid'=>$sid,
				'gid'=>$gid,
				'date'=>$my_date
			]
		];
		$res = $this->read_one($db_where);
		$buy_total = get_value($data, 'buy_total', 0);
		$buy_amount = get_value($data, 'buy_amount', 0);
		$return_total = get_value($data, 'return_total', 0);
		$return_amount = get_value($data, 'return_amount', 0);
		$transfer_total = get_value($data, 'transfer_total', 0);
		$transfer_amount = get_value($data, 'transfer_amount', 0);
		if($res){
			//如果已经有当天日报，则进行修改
			$db_set = [
				'buy_total[+]'=>$buy_total,
				'buy_amount[+]'=>$buy_amount,
				'return_total[+]'=>$return_total,
				'return_amount[+]'=>$return_amount,
				'transfer_total[+]'=>$transfer_total,
				'transfer_amount[+]'=>$transfer_amount,
			];
			$this->app->db->update($this->tablename, $db_set, $db_where);
		}
		else{
			$cg_res = $cg_model->read_one([
				'in_cid' => $cid,
				'gid' => $gid
			]);
			$cgt_res = $cgt_model->read_by_id($cg_res['gtid']);
			if(!$cgt_res){
				error(1421);
			}
			//如果没有，则新建
			$db_set = [
				'cid'=>$cid,
				'sid'=>$sid,
				'gid'=>$gid,
				'date'=>$my_date,
				'gname'=>$cg_res['gname'],
				'gcode'=>$cg_res['gcode'],
				'gbarcode'=>$cg_res['gbarcode'],
				'gspec'=>$cg_res['gspec'],
				'gunit'=>$cg_res['gunit'],
				'gtid'=>$cg_res['gtid'],
				'gtname'=>$cgt_res[0]['name'],
			 	'buy_total'=>$buy_total,
				'buy_amount'=>$buy_amount,
				'return_total'=>$return_total,
				'return_amount'=>$return_amount,
				'transfer_total'=>$transfer_total,
				'transfer_amount'=>$transfer_amount,
			];
			$this->app->db->insert($this->tablename, $db_set);
		}
		return True;
	}


}

