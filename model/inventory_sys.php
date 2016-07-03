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

class InventorySys extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*cid','*cname','*sid','*sname','status','checktime','uid','uname','cuid','cuname',
		'memo','amount','tids'];
	
	//搜索字段
    protected $search_data = ['id'];

	//可排序的字段
	protected $order_data = ['id','status'];

	protected $amount_data = ['amount'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_inventory_sys', $id);
	}

	/**
	 * 清除数据
	 *
	 * @param int $sid 仓库ID
	 * @return bool
     */
	public function clear($sid){
		//清除未审核的帐盘
		$this->update(['status'=>9],[
			'AND'=>[
				'status'=>1,
				'sid'=>$sid
			]
		]);
		//清除未审核的实盘
		$this->app->db->update('b_inventory_phy',
			['status'=>9],
			['AND'=>[
				'status'=>[1,2],
				'sid'=>$sid
			]
		]);
		return True;
	}

	/**
	 * 创建帐盘
	 *
	 * @param array $data 插入字段列表
	 * @return int 帐盘单号
     */
	public function my_create($data){

		//首先要找到商品列表，从档案读取或者从库存读取
		$cg_model = new CompanyGoods();

		$cg_data = [
			'in_cid'=>$data['cid'],
			'gisbind'=>'0'
		];
		//如果传了类型ID，找到类型下的所有子类型节点，做数据过滤
		if(get_value($data, 'tids')){
			$cgt_model = new CompanyGoodsType();
			$cg_data['gtid'] = $cgt_model->get_ids_by_fids($data['tids']);
		}
		//先获取档案的商品列表
		$cg_res = $cg_model->read_list($cg_data);
		if($data['type'] == 1){
			//盘点库存列表，过滤掉所有库存里没有的商品
			$r_res = $this->app->db->select('r_reserve','*',['sid'=>$data['sid']]);
			$r_gidlist = [];
			foreach($r_res as $val){
				$r_gidlist[] = $val['gid'];
			}

			$cg_res_data = $cg_res['data'];
			foreach($cg_res['data'] as $key=>$val){
				//如果库存表中不存在该商品ID，则过滤掉
				if(!in_array($val['gid'], $r_gidlist)){
					unset($cg_res_data[$key]);
				}
			}

			$cg_res['data'] = dict2list($cg_res_data);
		}
		$goods_list = $cg_res['data'];

		$gid_list = [];
		foreach($goods_list as $val){
			$gid_list[] = $val['gid'];
		}

		if(!$gid_list){
			error(3300);
		}

		$r_model = new Reserve();
		$r_res = $r_model -> get_reserve($data['cid'], $data['sid'], $gid_list);


		foreach($goods_list as $key=>$val){
			//初始帐盘数，有库存就写库存，没库存就写0
			if(get_value($r_res, $val['gid'])){
				$goods_list[$key]['total_sys'] = $r_res[$val['gid']];
			}
			else{
				$goods_list[$key]['total_sys'] = 0;
			}
			//初始实盘数全部为0
			$goods_list[$key]['total_phy'] = 0;
		}

		//获取商品列表的平均成本
		$pre_res = $this->get_pre_price($goods_list, $data['sid'], $data['cid']);

		start_action();
		//生成主表
		$sys_id = $this->create($data);

		foreach($goods_list as $key=>$val){
			$goods_list[$key]['sys_id'] = $sys_id;
			$goods_list[$key]['pre_unit_price'] = $pre_res[$val['gid']];
		}

		//生成明细表
		$isg_model = new InventorySysGlist();
		$isg_model->create_batch($goods_list, 0);

		return $sys_id;
	}

	/**
	 * 获取库存成本金额进行预盈亏计算
	 *
	 * @param array $goods_list
	 * @param int $sid
	 * @param int $cid
	 * @return array
     */
	public function get_pre_price($goods_list, $sid, $cid){
		$r_model = new Reserve();
		$p_model = new Price();
		$unit_res = $r_model->get_unit_price($goods_list, $sid);
		$result = [];
		foreach($goods_list as $goods){
			if(isset($unit_res[$goods['gid']])){
				$result[$goods['gid']] = $unit_res[$goods['gid']];
			}
			else{
				$result[$goods['gid']] = $p_model->get_price($goods['gid'], $cid, $sid, 'in_price');
			}
		}
		return $result;
	}

	/**
	 * 记账
	 *
	 * @param array $data 字段列表
	 * @return int 帐盘单号
     */
	public function my_check($data){
		$data['status'] = 2;
		$data['checktime'] = date('Y-m-d H:i:s');

		//开始对比实盘和帐盘，得出不一样的结果：盘盈列表和盘亏列表
		$py_list = $pk_list = [];
		$isg_model = new InventorySysGlist();
		$isg_data = $isg_model -> read_by_sysid($this->id);
		$goods_list = $isg_data['data'];

		$p_model = new Price();
		$r_model = new Reserve();
		foreach($goods_list as $val){
			//帐盘大于实盘，盘亏
			if($val['total_sys']>$val['total_phy']){
				$price = $r_model->read_old_price($val['gid'], $data['sid'], $data['cid']);
				$total = $val['total_sys']-$val['total_phy'];
				$pk_list[] = [
					'gid'=>$val['gid'],
					'total'=>$total,
					'unit_price'=>$price
				];
			}
			//实盘大于帐盘，盘盈
			elseif($val['total_sys']<$val['total_phy']){
				$price = $r_model->read_old_price($val['gid'], $data['sid'], $data['cid']);
				$total = $val['total_phy']-$val['total_sys'];
				$py_list[] = [
					'gid'=>$val['gid'],
					'total'=>$total,
					'unit_price'=>$price
				];
			}

			if($val['total_phy']<0){
				error(3305);
			}
		}

		start_action();


		$si_model = new StockIn();
		$so_model = new StockOut();

		$data['type'] = 5;
		Power::set_oper($data);
		$data['order_id'] = $this->id;


		$pd_res = [];
		//生成盘盈单
		if($py_list){
			$data['goods_list'] = json_encode($py_list);
			$si_id = $si_model->my_check($data, 'create', 5);

			$si_res = $this->app->db->select('b_stock_in_glist','*',[
				'stock_in_id'=>$si_id
			]);
			foreach($si_res as $val){
				$gid = $val['gid'];
				if(!isset($pd_res[$gid])){
					$pd_res[$gid] = 0;
				}
				$pd_res[$gid] += $val['cost_price'];
			}
		}

		//生成盘亏单
		if($pk_list){
			$data['status'] = 4;
			$data['goods_list'] = json_encode($pk_list);
			$so_model->my_check($data, 'create', 5);
			$so_id = $so_model->get_id();
			$so_res = $this->app->db->select('b_stock_out_glist','*',[
				'stock_out_id'=>$so_id
			]);
			foreach($so_res as $val){
				$gid = $val['gid'];
				if(!isset($pd_res[$gid])){
					$pd_res[$gid] = 0;
				}
				$pd_res[$gid] -= $val['cost_price'];
			}
		}

		foreach($pd_res as $key=>$val){
			$pd_res[$key] = fen2yuan($val);
		}

		$all_amount = 0;
		foreach($pd_res as $val){
			$all_amount = price_add($all_amount, $val);
		}
		$data['amount'] = $all_amount;

		//更新主表字段
		$this->update_by_id($data);

		//更新明细表实际盈亏值
		foreach($goods_list as $val){
			if(isset($pd_res[$val['gid']])){
				$amount = $pd_res[$val['gid']];
			}
			else{
				$amount = '0.00';
			}

			$db_set = [
				'amount'=>$amount
			];
			$db_where = [
				'AND' => [
					'sys_id'=>$this->id,
					'gid'=>$val['gid']
				]
			];
			$isg_model->update($db_set, $db_where, false, false);
		}

		//更新实盘单已记账状态
		$ip_model = new InventoryPhy();
		$ip_model->update(['status'=>3], ['sys_id'=>$this->id]);

		return $this->id;
	}

	/**
	 * 帐盘权限检测
	 *
	 * @param int $id 帐盘单号
	 * @param int $status 状态，为0时不检测状态
	 * @return array 帐盘详情
     */
	public function my_power($id, $status){
		$res = $this->read_by_id($id);
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

	/**
	 * 读取帐盘详情
	 *
	 * @return array
     */
	public function my_read(){
		//主表信息
		$res = $this -> read_by_id();
		//明细表信息
		$isg_model = new InventorySysGlist();
		$isg_data = $isg_model -> read_by_sysid($this->id);

		$all_amount = 0;
		foreach($isg_data['data'] as $key=>$val){
			$fen = yuan2fen($val['pre_unit_price'])*($val['total_phy']-$val['total_sys']);
			$all_amount += $fen;
			$isg_data['data'][$key]['pre_amount'] = fen2yuan($fen);
		}
		$res[0]['tnames'] = $this->get_names_by_ids('o_company_goods_type', $res[0]['tids']);
		$res[0]['pre_amount'] = fen2yuan(($all_amount));
		$res[0]['goods_list'] = $isg_data['data'];

		return $res;

	}

	/**
	 * 获取当前正在盘点的帐盘
	 *
	 * @param int $sid 仓库ID
	 * @return False-无 int-帐盘id
     */
	public function get_now_id($sid){
		$res = $this->read_one([
			'status'=>1,
			'sid'=>$sid
		]);
		if($res){
			return $res['id'];
		}
		else{
			return False;
		}
	}



}

