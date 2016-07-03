<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods_packing
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

/**
 * TODO:
 */
class GoodsPacking{
	
	public function __construct($id = NULL){
		$this -> id = $id;
	}
	
	/**
	 * 增加商品大小包装转换关系
	 * 
	 * @param int 	大包装商品id
	 * @param int 	基本包装商品id
	 * @param int 	倍率
	 */
	public function create($big_id, $base_id, $rate){
		$app = \Slim\Slim::getInstance();
		
		//判断大包装存在并有效，并且属性是大包装
		$res = $app->db->select('o_goods', 'ispkg', [
			'AND' => [
				'id' => $big_id,
				'status' => 1
			]
		]);
		if(!isset($res[0])){
			error(1430);
		}
		if($res[0] == 0){
			error(1431);
		}		
		
		//判断小包装存在并有效，并且属性是基本包装
		$res = $app->db->select('o_goods', 'ispkg', [
			'AND' => [
				'id' => $base_id,
				'status' => 1
			]
		]);
		if(!isset($res[0])){
			error(1432);
		}
		if($res[0] == 1){
			error(1433);
		}
		
		//判断大包装商品还没有绑定商品
		$db_where = [
			'big_gid' => $big_id,
		];
		$res = $app->db->has('r_packing', $db_where);
		if($res){
			error(1434);
		}
		
		//建立绑定关系
		$res = $app->db->insert('r_packing', [
			'big_gid' => $big_id,
			'base_gid' => $base_id,
			'rate' => $rate
		]);
		return $res;
	}	
	
	/**
	 * 修改商品大小包装转换倍率
	 *
	 * @param int 	倍率
	 */
	public function update($rate){
		$app = \Slim\Slim::getInstance();
		$res = $app->db->update('r_packing', [
			'rate' => $rate
		], [
			'id' => $this->id,
		]);
		return $res;
	}
	
	/**
	 * 读取商品大小包装详情
	 *
	 * @param int 	大包装id
	 */
	public function read_by_big($big_id){
		$app = \Slim\Slim::getInstance();
		$res = $app->db->select('r_packing', '*', [
			'big_gid' => $big_id,
		]);
		return $res;
	}
	
	/**
	 * 读取商品大小包装列表
	 *
	 */
	public function read_list($data){
		$app = \Slim\Slim::getInstance();
		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 50);
		$start_count = ($page - 1) * $page_num;
		
		$db_where = [
			'ORDER' => 'id DESC',
			'LIMIT' => [$start_count, $page_num]
		];
		
		$res = $app->db->select('r_packing', '*', $db_where);
		return $res;
	}
	
	/**
	 * 删除商品大小包装关系
	 */
	public function delete_by_id(){
		$app = \Slim\Slim::getInstance();
		$res = $app->db->delete('r_packing', [
			'id' => $this->id,
		]);
		return $res;
	}
	
}

