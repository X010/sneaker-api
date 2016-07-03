<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * visit
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class DbOrder extends Object2{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['super_order_id','erp_order_id','uid'];

	protected $list_data = ['id','receipt'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('db_order', $id);
	}
	
}

