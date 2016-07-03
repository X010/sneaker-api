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

/**
 * TODO:
 */
class FAdjust extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['cid','sid','gid','gname','gcode','gbarcode','gspec','gunit','gtid','gtname','date','return_amount',
		'flush_amount','transfer_amount','overloss_total', 'overloss_amount','inventory_total','inventory_amount'];

	//需要分和元转换的金额字段
	protected $amount_data = ['return_amount','flush_amount','transfer_amount','overloss_amount','inventory_amount'];

	protected $search_data = ['gname','gcode','gbarcode'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('f_adjust', $id);
	}

	//写调整日报
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
		$return_amount = get_value($data, 'return_amount', 0);
		$flush_amount = get_value($data, 'flush_amount', 0);
		$transfer_amount = get_value($data, 'transfer_amount', 0);
		$overloss_total = get_value($data, 'overloss_total', 0);
		$overloss_amount = get_value($data, 'overloss_amount', 0);
		$inventory_total = get_value($data, 'inventory_total', 0);
		$inventory_amount = get_value($data, 'inventory_amount', 0);
		if($res){
			//如果已经有当天日报，则进行修改
			$db_set = [
				'return_amount[+]'=>$return_amount,
				'flush_amount[+]'=>$flush_amount,
				'transfer_amount[+]'=>$transfer_amount,
				'overloss_total[+]'=>$overloss_total,
				'overloss_amount[+]'=>$overloss_amount,
				'inventory_total[+]'=>$inventory_total,
				'inventory_amount[+]'=>$inventory_amount,
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
				'return_amount'=>$return_amount,
				'flush_amount'=>$flush_amount,
				'transfer_amount'=>$transfer_amount,
				'overloss_total'=>$overloss_total,
				'overloss_amount'=>$overloss_amount,
				'inventory_total'=>$inventory_total,
				'inventory_amount'=>$inventory_amount,
			];
			$this->app->db->insert($this->tablename, $db_set);
		}
		return True;
	}

}

