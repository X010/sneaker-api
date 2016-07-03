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

class StoreArea extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['sid','areapro','areacity','areazone','areatype'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('r_store_area', $id);
	}

	public function delete_by_sid($sid){
		$this->delete(['sid'=>$sid]);
		return True;
	}
}

