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

class InventoryPhyGlist extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*sys_id','*phy_id','gid','gname','gcode','gbarcode','gspec','gunit','total'];

	//搜索字段
	protected $search_data = ['phy_id'];

	//可排序的字段
	protected $order_data = ['id','gid','sys_id','phy_id'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_inventory_phy_glist', $id);
	}

	/**
	 * 根据主表ID读取明细
	 *
	 * @param int $phyid 实盘单号
	 * @return array 商品清单明细列表
     */
	public function read_by_phyid($phyid){
		$res = $this->read_list([
			'phy_id' => $phyid,
			'orderby' => 'id^asc'
		]);
		return $res;
	}

	/**
	 * 根据主表ID删除所有明细
	 *
	 * @param int $phy_id 实盘单号
	 * @return int
     */
	public function delete_by_phyid($phy_id){
		$res = $this->delete([
			'phy_id'=>$phy_id
		]);
		return $res;
	}

}

