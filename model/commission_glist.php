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

class CommissionGlist extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['commission_id','order_id','stock_id','stock_type','gid','gname','gcode','gbarcode',
		'gspec','gunit','gtax_rate','unit_price','amount_price','total','commission_amount','commission_real_amount',
		'commission_rate','commission_unit_price','memo','is_activity'];

	//搜索字段
	protected $search_data = ['gname', 'gcode', 'gbarcode'];

	protected $amount_data = ['unit_price','amount_price','commission_amount','commission_real_amount','commission_unit_price'];

	protected $order_data = ['gid'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_commission_glist', $id);
	}

	
}

