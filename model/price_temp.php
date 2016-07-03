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

class PriceTemp extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*cid','*cname','sids','snames','type','status','checktime','uid','uname','cuid','cuname',
		'memo'];

	
	//搜索字段
    protected $search_data = ['id'];

	//可排序的字段
	protected $order_data = ['id','status','type','begintime'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_price_temp', $id);
	}

	/**
	 * 新建调价单
	 *
	 * @param array $data 字段列表
	 * @return int 调价单号
     */
	public function my_create($data){

		if($data['type'] == 1){
			$type = 'in';
		}
		elseif($data['type'] == 2){
			$type = 'out';
		}

		$glist = $this->get_glist($data, $type);

		start_action();

		//写入数据库
		$price_id = parent::create($data);
		foreach($glist as $key=>$val){
			$glist[$key]['price_id'] = $price_id;
		}

		$ptg_model = new PriceTempGlist();
		$ptg_model->create_batch($glist, 0);

		return $price_id;
	}

	/**
	 * 修改调价单
	 *
	 * @param array $data 字段列表
	 * @return int 调价单号
	 */
	public function my_update($data){

		if($data['type'] == 1 || $data['type'] == 3){
			$type = 'in';
		}
		elseif($data['type'] == 2 || $data['type'] == 4){
			$type = 'out';
		}

		$glist = $this->get_glist($data, $type);
		foreach($glist as $key=>$val){
			$glist[$key]['price_id'] = $this->id;
		}

		start_action();
		$this->update_by_id($data);
		//先删除所有清单，重新生成

		$ptg_model = new PriceTempGlist();
		$ptg_model -> delete_by_priceid($this->id);
		$ptg_model -> create_batch($glist, 0);

		return $this->id;
	}

	/**
	 * 审核促销调价单
	 *
	 * @param array $data 字段列表
	 * @param string $type 类型，新增或修改
	 * @return int 调价单号
	 */
	public function my_check_temp($data, $type){
		if($type == 'create'){
			$price_id = $this->my_create($data);
		}
		elseif($type == 'update'){
			$price_id = $this->my_update($data);
		}

		//写入促销调价表
		$goods_list = json_decode($data['goods_list'], True);
		foreach($goods_list as $val){
			$begintime = get_value($val, 'begintime');
			$endtime = get_value($val, 'endtime');
			if($data['sids'] == -1){
				//如果是公司级别
				$tp_data[] = [
					'cid' => $this->app->Sneaker->cid,
					'sid' => $data['sids'],
					'gid' => $val['gid'],
					'begintime' => $begintime,
					'endtime' => $endtime,
					'in_price' => $val['in_price']
				];
			}
			else{
				//仓库级别
				$sids = explode(',', $data['sids']);
				foreach($sids as $sid){
					if($sid){
						$tp_data[] = [
							'cid' => $this->app->Sneaker->cid,
							'sid' => $sid,
							'gid' => $val['gid'],
							'begintime' => $begintime,
							'endtime' => $endtime,
							'in_price' => $val['in_price']
						];
					}
				}
			}
		}
		$tp_model = new TempPrice();
		$tp_model -> create_batch($tp_data);
		return $price_id;
	}

	/**
	 * 查询调价单详情
	 *
	 * @return array
     */
	public function my_read(){
		$p_res = $this->read_by_id();
		if(!$p_res){
			error(3202);
		}
		$ptg_model = new PriceTempGlist();
		$pg_res = $ptg_model -> read_by_priceid($p_res[0]['id']);

		$p_res[0]['goods_list'] = $pg_res['data'];
		return $p_res[0];
	}

	public function my_read_list($data){
		//如果传了search，需要按照商品来检索
		if(get_value($data, 'gid')){
			$ptg_model = new PriceTempGlist();
			$ptg_res = $ptg_model->read_list($data);
			$ids_list = [];
			foreach($ptg_res['data'] as $val){
				$ids_list[] = $val['price_id'];
			}
			if($ids_list){
				$data['id'] = $ids_list;
			}
			else{
				$data['id'] = 'null';
			}
		}
		$sid = get_value($data, 'sid');
		if($sid && $sid!=-1){
			$data['sids[~]'] = '%,'.$data['sid'].',%';
		}
		elseif($sid == -1){
			$data['sids'] = $data['sid'];
		}
		$res = $this->read_list($data);
		return $res;
	}

	/**
	 * 获取商品列表
	 *
	 * @param array $data 字段列表
	 * @param string $type in进货调价 out出货调价
	 * @return array
     */
//	public function get_glist($data, $type='in'){
//		$goods_list = json_decode($data['goods_list'], True);
//		$new_goods_list = [];
//		$cg_model = new CompanyGoods();
//		foreach($goods_list as $val){
//			$cg_res = $cg_model->get_one_goods($data['cid'], $val['gid']);
//			$val['gname'] = $cg_res['gname'];
//			$val['gcode'] = $cg_res['gcode'];
//			$val['gbarcode'] = $cg_res['gbarcode'];
//			$val['gspec'] = $cg_res['gspec'];
//			$val['gunit'] = $cg_res['gunit'];
//			$new_goods_list[] = $val;
//		}
//		$sid = get_value($data, 'sid');
//
//		$sg_model = new StoreGoods();
//		$cg_model = new CompanyGoods();
//
//		foreach($new_goods_list as $key=>$val){
//			if($sid){
//				$sg_res = $sg_model->read_one([
//					'in_sid' => $sid,
//					'gid' => $val['gid']
//				]);
//				if($sg_res){
//					$price = $sg_res;
//				}
//			}
//			//如果没有传sid或者仓库价格没有记录，取公司记录
//			if(!$sid || !$sg_res){
//				$cg_res = $cg_model->read_one([
//					'in_cid' => $data['cid'],
//					'gid' => $val['gid']
//				]);
//				if(!$cg_res){
//					error(3008);
//				}
//				$price = $cg_res;
//			}
//			if($type == 'in'){
//				$new_goods_list[$key]['old_in_price'] = $price['in_price'];
//			}
//			elseif($type == 'out'){
//				$new_goods_list[$key]['old_out_price1'] = $price['out_price1'];
//				$new_goods_list[$key]['old_out_price2'] = $price['out_price2'];
//				$new_goods_list[$key]['old_out_price3'] = $price['out_price3'];
//				$new_goods_list[$key]['old_out_price4'] = $price['out_price4'];
//			}
//
//		}
//
//		return $new_goods_list;
//	}
	public function get_glist($data, $type='in'){
		$goods_list = json_decode($data['goods_list'], True);

		$sid = get_value($data, 'sid');
		$sg_model = new StoreGoods();
		$cg_model = new CompanyGoods();
		foreach($goods_list as $key=>$val){
			$cg_res = $cg_model->get_one_goods($data['cid'], $val['gid']);
			$val['gname'] = $cg_res['gname'];
			$val['gcode'] = $cg_res['gcode'];
			$val['gbarcode'] = $cg_res['gbarcode'];
			$val['gspec'] = $cg_res['gspec'];
			$val['gunit'] = $cg_res['gunit'];
			$price = $cg_res;
			if($sid){
				$sg_res = $sg_model->read_one([
					'in_sid' => $sid,
					'gid' => $val['gid']
				]);
				if($sg_res){
					$price = $sg_res;
				}
			}
			if($type == 'in'){
				$val['old_in_price'] = $price['in_price'];
			}
			elseif($type == 'out'){
				$val['old_out_price1'] = $price['out_price1'];
				$val['old_out_price2'] = $price['out_price2'];
				$val['old_out_price3'] = $price['out_price3'];
				$val['old_out_price4'] = $price['out_price4'];
			}
			$goods_list[$key] = $val;
		}

		return $goods_list;
	}


	/**
	 * 通过仓库ID列表获取名称列表
	 *
	 * @param string $sids 仓库ID列表，逗号分隔
	 * @return string 仓库名称列表，逗号分隔
     */
	public function get_snames($sids){
		$sid_list = explode(',', $sids);
		$store_res = $this->app->db->select('o_store','*',['id'=>$sid_list]);
		$snames = [];
		if($store_res){
			foreach($sid_list as $sid){
				$flag = 1;
				if($sid){
					foreach($store_res as $store){
						if($store['id'] == $sid){
							$snames[] = $store['name'];
							$flag = 0;
							break;
						}
					}
					if($flag){
						$snames[] = '无';
					}
				}
			}
		}
		$snames = implode(',', $snames);
		return $snames;
	}

	/**
	 * 检查调价单的状态、类型、公司属性是否正确
	 *
	 * @param int $id 调价单号
	 * @param int $status 状态，为0时不检测
	 * @param string $type 类型，为0时不检测
	 * @return array 调价单字段
     */
	public function my_power($id, $status, $type){
		$res = $this->read_by_id($id);
		if(!$res){
			error(3202);
		}
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(3203);
		}
		if($status && $res[0]['status'] != $status){
			error(3204);
		}
		if($type && $res[0]['type'] != $type){
			error(3205);
		}
		return $res[0];
	}
}

