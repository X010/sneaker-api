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

class SettleGlist extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['settle_id','type','order_id','stock_id','stock_type','gid','gname','gcode','gbarcode',
		'gspec','gunit','gtax_rate','unit_price','amount_price','tax_price','total'];

	//需要分和元转换的金额字段
	protected $amount_data = ['unit_price','amount_price','tax_price'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_settle_glist', $id);
	}

	public function my_create($settle_id, $stock_list, $type){
		$my_data = [];

		$stock_list = explode(',', $stock_list);
		foreach($stock_list as $stock_id){
			if($stock_id[0] == '2'){
				//出库
				$so_model = new StockOut($stock_id);
				$so_res = $so_model->read_all_by_id();
				foreach($so_res[0]['goods_list'] as $val){
					$val['settle_id'] = $settle_id;
					$val['type'] = $type;
					$val['order_id'] = $so_res[0]['order_id'];
					$val['stock_id'] = $stock_id;
					$val['stock_type'] = 1;
					$my_data[] = $val;
				}
			}
			elseif($stock_id[0] == '3'){
				//入库
				//出库
				$si_model = new StockIn($stock_id);
				$si_res = $si_model->read_all_by_id();
				foreach($si_res[0]['goods_list'] as $val){
					$val['settle_id'] = $settle_id;
					$val['type'] = $type;
					$val['order_id'] = $si_res[0]['order_id'];
					$val['stock_id'] = $stock_id;
					$val['stock_type'] = 2;
					$my_data[] = $val;
				}
			}
		}
		start_action();
		$this->create_batch($my_data, 0);
		return True;
	}

	public function my_update($settle_id, $stock_list, $type){
		//先清除，后增加
		start_action();
		$this->delete([
			'AND'=>[
				'settle_id'=>$settle_id,
				'type'=>$type
			]
		]);
		$this->my_create($settle_id, $stock_list, $type);
		return True;
	}

	public function get_tax_group($settle_id, $type){
		$settle_id = strval($settle_id);
		//客户结算单
		if($type == 1){
			$sql = "select gtax_rate as tax_rate,sum(case when stock_type=1 then amount_price else 0-amount_price end) as amount";
			$sql .= ", sum(case when stock_type=1 then tax_price else 0-tax_price end) as tax from `b_settle_glist`";
			$sql .= "where settle_id=". $settle_id. " group by gtax_rate order by gtax_rate desc";
		}
		//供应商结算单
		elseif($type == 2){
			$sql = "select gtax_rate as tax_rate,sum( case when stock_type=2 then amount_price else 0-amount_price end) as amount";
 			$sql .= ", sum(case when stock_type=2 then tax_price else 0-tax_price end) as tax from `b_settle_glist`";
			$sql .= "where settle_id=". $settle_id. " group by gtax_rate order by gtax_rate desc";
		}
		else{
			error(0);
		}
		//var_dump($sql);
		$r_res = $this->app->db->query($sql)->fetchAll();
		$result = [];
		foreach($r_res as $val){
			$result[] = [
				'tax_rate'=>$val['tax_rate'],
				'amount_price'=>fen2yuan($val['amount']),
				'tax_price'=>fen2yuan($val['tax'])
			];
		}
		return $result;
	}
	
}

