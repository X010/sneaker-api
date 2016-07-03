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

class InventoryPhy extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*sys_id','*cid','*cname','*sid','*sname','status','checktime','uid','uname','cuid','cuname',
		'memo'];

	//搜索字段
    protected $search_data = ['id'];

	//可排序的字段
	protected $order_data = ['id','status'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_inventory_phy', $id);
	}

	/**
	 * 创建实盘
	 *
	 * @param array $data 插入字段列表
	 * @return int 实盘单号
     */
	public function my_create($data){
		$cid = $data['cid'];

		//获取当前有效的帐盘
		$is_model = new InventorySys();
		$sys_id = $is_model->get_now_id($data['sid']);
		if(!$sys_id){
			error(3301);
		}

		$goods_list = json_decode($data['goods_list'], True);

		start_action();
		$data['sys_id'] = $sys_id;
		$phy_id = $this->create($data);

		$cg_model = new CompanyGoods();

		foreach($goods_list as $key=>$val){
			if(get_value($val, 'gid')){
				$cg_res = $cg_model->get_one_goods($cid, $val['gid']);
				if(!$cg_res){
					error(1423);
				}
			}
			else{
				$cg_res = $cg_model->read_one([
					'in_cid'=>$cid,
					'gbarcode'=>$val['gbarcode']
				]);
			}
			if(!$cg_res){
				error(1423);
			}
			$goods_list[$key]['gid'] = $cg_res['gid'];
			$goods_list[$key]['gname'] = $cg_res['gname'];
			$goods_list[$key]['gcode'] = $cg_res['gcode'];
			$goods_list[$key]['gbarcode'] = $cg_res['gbarcode'];
			$goods_list[$key]['gisbind'] = $cg_res['gisbind'];
			$goods_list[$key]['gspec'] = $cg_res['gspec'];
			$goods_list[$key]['gunit'] = $cg_res['gunit'];

		}

		//开始处理捆绑转换，将所有大商品转换成小商品
		$si_model = new StockIn();
		$goods_list = $si_model -> get_sbind($goods_list, $data['cid'], $data['sid']);

		foreach($goods_list as $key=>$val){
			$goods_list[$key]['sys_id'] = $sys_id;
			$goods_list[$key]['phy_id'] = $phy_id;
		}


		//生成明细表
		$ipg_model = new InventoryPhyGlist();
		$ipg_model->create_batch($goods_list, 0);

		return $phy_id;
	}

	/**
	 * 修改实盘
	 *
	 * @param array $data 修改字段列表
	 * @return int 实盘单号
     */
	public function my_update($data){
		$cid = $data['cid'];

		//获取当前有效的帐盘
		$is_model = new InventorySys();
		$sys_id = $is_model->get_now_id($data['sid']);

		start_action();
		$this->update_by_id($data);

		$ipg_model = new InventoryPhyGlist();
		$ipg_model->delete_by_phyid($this->id);

		$cg_model = new CompanyGoods();

		$goods_list = json_decode($data['goods_list'], True);

		foreach($goods_list as $key=>$val){
			$cg_res = $cg_model->get_one_goods($cid, $val['gid']);
			if(!$cg_res){
				error(1423);
			}
			$goods_list[$key]['gname'] = $cg_res['gname'];
			$goods_list[$key]['gcode'] = $cg_res['gcode'];
			$goods_list[$key]['gbarcode'] = $cg_res['gbarcode'];
			$goods_list[$key]['gisbind'] = $cg_res['gisbind'];
			$goods_list[$key]['gspec'] = $cg_res['gspec'];
			$goods_list[$key]['gunit'] = $cg_res['gunit'];
		}

		//开始处理捆绑转换，将所有大商品转换成小商品
		$si_model = new StockIn();
		$goods_list = $si_model -> get_sbind($goods_list, $data['cid'], $data['sid']);

		foreach($goods_list as $key=>$val){
			$goods_list[$key]['sys_id'] = $sys_id;
			$goods_list[$key]['phy_id'] = $this->id;
		}

		//生成明细表
		$ipg_model->create_batch($goods_list, 0);

		return $this->id;
	}

	/**
	 * 审核实盘
	 *
	 * @param array $data 字段列表
	 * @param string $type 类型，新增或修改
	 * @return int 实盘单号
     */
	public function my_check($data, $type){
		if($type == 'create'){
			$phy_id = $this->my_create($data);
		}
		elseif($type == 'update'){
			$phy_id = $this->my_update($data);
		}
		$is_model = new InventorySys();
		$sys_id = $is_model->get_now_id($data['sid']);

		//开始更新帐盘，按照实盘商品清单更新帐盘商品清单
		$isg_model = new InventorySysGlist();
		$ipg_model = new InventoryPhyGlist();

		$ipg_res = $ipg_model->read_by_phyid($phy_id);
		$goods_list = $ipg_res['data'];
		//$goods_list = json_decode($data['goods_list'], True);
		foreach($goods_list as $val){
			$isg_model -> update_phy($sys_id, $val['gid'], $val['total'], $data['sid']);
		}

		return $phy_id;
	}

	/**
	 * 读取实盘详情
	 *
	 * @return array
     */
	public function my_read(){
		//主表信息
		$res = $this -> read_by_id();
		//明细表信息
		$ipg_model = new InventoryPhyGlist();
		$ipg_data = $ipg_model -> read_by_phyid($this->id);

		$res[0]['goods_list'] = $ipg_data['data'];
		return $res;

	}

	/**
	 * 实盘权限检测
	 *
	 * @param int $id 实盘单号
	 * @param int $status 检测状态，为0时不检测
	 * @return array 单据字段列表
     */
	public function my_power($id, $status){
		$res = $this->app->db->select($this->tablename, '*', ['id' => $id]);
		if(!$res){
			error(3302);
		}
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(3303);
		}
		if($status && $res[0]['status'] != $status){
			error(3304);
		}
		return $res[0];
	}


}

