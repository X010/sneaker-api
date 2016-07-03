<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods_brand
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class GoodsBrand extends Object{
	
	/**
	 * 入库所需字段（必须）
	 */
	protected $format_data = ['name','code'];

	protected $search_data = ['name','code'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_goods_brand', $id);
	}

	public function my_create($data){
		$res = $this->has(['name'=>$data['name']]);
		if($res){
			error(1402);
		}
		$res = $this->create($data);
		return $res;
	}
	
	/**
	 * 删除商品品牌
	 *
	 */
	public function my_delete(){
		$app = \Slim\Slim::getInstance();
		$db_where = [
			'bid' => $this->id
		];
		//$res = $app->db->select('o_goods', 'id', $db_where);
		$res = $app->db->has('o_goods', $db_where); //faster
		if($res){
			//如果存在关联商品，不可被删除
			error(1400);
		}
		//品牌直接删除数据
		$res = parent::delete_by_id();
		return $res;
	}

}

