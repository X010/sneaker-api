<?php 
/**
 * 收藏商品model 
 * 
 * @author      liyi <lyliyi2009@gmail.com>
 * @copyright   2015 liyi
 * @version     0.0.1
 * @package     model
 */
class DbFavorite extends Object2{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['uid','cid','mgid','create_time','status'];

	// protected $list_data = ['id','receipt'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('db_favorite', $id);
	}
	
}