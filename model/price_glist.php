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

class PriceGlist extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['*id','*price_id','*gid','*gname','*gcode','*gbarcode','gspec','gunit','*in_price',
		'*out_price1', '*out_price2', '*out_price3','*out_price4','*old_in_price','*old_out_price1','*old_out_price2',
		'*old_out_price3','*old_out_price4'];

	//可排序的字段
	protected $order_data = ['id','gid'];

	//需要分和元转换的金额字段
	protected $amount_data = ['in_price','out_price1','out_price2','out_price3','out_price4','old_in_price',
		'old_out_price1','old_out_price2','old_out_price3','old_out_price4'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_price_glist', $id);
	}

	public function delete_by_priceid($id){
		return $this->delete(['price_id'=>$id]);
	}

	public function read_by_priceid($id){
		return $this->read_list([
			'price_id'=>$id,
			'orderby' => 'id^asc'
		]);
	}
	
}

