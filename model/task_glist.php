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

class TaskGlist extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['task_id','gid','gname','gcode','gbarcode','gspec','gunit','val_all',
		'val1','val2','val3','val4','val5','val6','val7','val8','val9','val10','val11','val12'];

	//搜索字段
	protected $search_data = ['gname', 'gcode', 'gbarcode'];

	protected $order_data = ['gid'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_task_glist', $id);
	}

	
}

