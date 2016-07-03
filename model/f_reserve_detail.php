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
class FReserveDetail extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['cid','sid','gid','date','amount_begin','amount_end','total_begin','total_end','gcode',
		'gname','gbarcode','gspec','gunit','gtid','gtname'];

	//需要分和元转换的金额字段
	protected $amount_data = ['amount_begin','amount_end'];

	protected $search_data = ['gname','gcode','gbarcode'];

	protected $order_data = ['date'];
	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('f_reserve_detail', $id);
	}



}

