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

class SortingGlist extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['sorting_id','stock_id','gid','gname','gcode','gbarcode',
		'gspec','gunit','total','ccname','weight'];

	//搜索字段
	protected $search_data = ['gname', 'gcode', 'gbarcode'];

	protected $order_data = ['gid'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_sorting_glist', $id);
	}

	
}

