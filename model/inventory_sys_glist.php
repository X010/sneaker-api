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

class InventorySysGlist extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*sys_id','gid','gname','gcode','gbarcode','gtid','gbid','gspec','gunit','total_sys',
		'total_phy','flag','amount','pre_unit_price'];
	
	//搜索字段
    protected $search_data = ['sys_id','gname','gcode','gbarcode'];

	//可排序的字段
	protected $order_data = ['id','gid'];

	protected $amount_data = ['amount','pre_unit_price'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_inventory_sys_glist', $id);
	}

	/**
	 * 根据主表ID读取商品明细
	 *
	 * @param int $sysid 帐盘单号
	 * @return array
     */
	public function read_by_sysid($sysid){
		$res = $this->read_list([
			'sys_id' => $sysid,
			'orderby' => 'id^asc'
		]);
		return $res;
	}

	/**
	 * 更新实盘信息
	 *
	 * @param int $sys_id 帐盘单号
	 * @param int $gid 商品ID
	 * @param int $total 更新数目
	 * @return bool
     */
	public function update_phy($sys_id, $gid, $total, $sid){
		$db_set = [
			'total_phy[+]'=>$total
		];
		$db_where = [
			'AND' => [
				'sys_id'=>$sys_id,
				'gid'=>$gid
			]
		];
		$res = $this->app->db->update($this->tablename, $db_set, $db_where);
		if($res === 0){
			//如果没有更新到记录，表示帐盘中无此商品，需要增加一个帐盘数为0，实盘数为当前数的记录
			//首先从公司档案调出该商品
			$cid = $this->app->Sneaker->cid;
			$c_res = $this->app->db->select('o_company_goods','*',[
				'AND'=>[
					'in_cid'=>$cid,
					'gid'=>$gid
				]
			]);
			if($c_res){
				$goods = $c_res[0];
				$goods['total_sys']=0;
				$goods['total_phy']=$total;
				$goods['sys_id']=$sys_id;
				$goods['flag']=1;

				//去库存取成本价
				$r_model = new Reserve();
				$p_model = new Price();
				$r_res = $r_model->get_unit_price([['gid'=>$gid]], $sid);
				if(isset($r_res[$gid])){
					$price = $r_res[$gid];
				}
				else{
					$price = $p_model->get_price($gid, $cid, $sid, 'in_price');
				}
				$goods['pre_unit_price'] = $price;
				$this->create($goods, 0);
			}
		}

		//判断实盘数不能为负数
		$db_where = [
			'sys_id'=>$sys_id,
			'gid'=>$gid,
			'total_phy[<]'=>0
		];
		$res = $this->read_one($db_where);
		if($res){
			error(3306, $res['gname']);
		}
		return True;
	}

}

