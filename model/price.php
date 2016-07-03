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

class Price extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*cid','*cname','sids','snames','type','status','checktime','uid','uname','cuid','cuname',
		'begintime','endtime','memo','isnow','jobname'];
	
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
		parent::__construct('b_price', $id);
	}

	/**
	 * 新建调价单
	 *
	 * @param array $data 字段列表
	 * @return int 调价单号
     */
	public function my_create($data){

		if($data['type'] == 1 || $data['type'] == 3){
			$type = 'in';
		}
		elseif($data['type'] == 2 || $data['type'] == 4){
			$type = 'out';
		}

		$glist = $this->get_glist($data, $type);

		start_action();

		//写入数据库
		$price_id = parent::create($data);
		foreach($glist as $key=>$val){
			$glist[$key]['price_id'] = $price_id;
		}

		$pg_model = new PriceGlist();
		$pg_model->create_batch($glist, 0);

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

		$pg_model = new PriceGlist();
		$pg_model -> delete_by_priceid($this->id);
		$pg_model -> create_batch($glist, 0);

		return $this->id;
	}

	/**
	 * 审核调价单
	 *
	 * @param array $data 字段列表
	 * @param string $type 类型，新增或修改
	 * @return int 调价单号
	 */
	public function my_check($data, $type){
		if($data['isnow'] == 1){
			$data['begintime'] = $data['checktime'];
		}

		if($type == 'create'){
			$price_id = $this->my_create($data);
		}
		elseif($type == 'update'){
			$price_id = $this->my_update($data);
		}

		$begintime = get_value($data, 'begintime');

		if($data['isnow'] != 1){
			//如果传了begin_time，是任务改价，否则是立即改价
			if($begintime < date('Y-m-d H:i:s')){
				error(3201);
			}
			$param = [
				'data' => json_encode($data),
			];
			$my_url = $this->app->config('my_url'). '/plan/change_price';
			$jobname = Plan::create('price_'. $price_id, $my_url, $param, $begintime);

			$this->update(['jobname'=>$jobname],[
				'id'=>$price_id
			]);
		}
		else{
			//立即执行
			$this->change_price($data);
		}

		return $price_id;

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

		$begintime = get_value($data, 'begintime');
		$endtime = get_value($data, 'endtime');

		//写入促销调价表
		$goods_list = json_decode($data['goods_list'], True);
		foreach($goods_list as $val){
			$tp_data[] = [
				'cid' => $this->app->Sneaker->cid,
				'sid' => $data['sid'],
				'gid' => $val['gid'],
				'begintime' => $begintime,
				'endtime' => $endtime,
				'in_price' => $val['in_price']
			];
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
		$pg_model = new PriceGlist();
		$pg_res = $pg_model -> read_by_priceid($p_res[0]['id']);

		$p_res[0]['goods_list'] = $pg_res['data'];
		return $p_res[0];
	}

	public function my_read_list($data){
		//如果传了search，需要按照商品来检索
		if(get_value($data, 'gid')){
			$pg_model = new PriceGlist();
			$pg_res = $pg_model->read_list($data);
			$ids_list = [];
			foreach($pg_res['data'] as $val){
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
	 * 调价
	 *
	 * @param array $data 字段列表
	 * @return bool
     */
	public function change_price($data){
		start_action();
		$goods_list = json_decode($data['goods_list'], True);
		$sids = get_value($data, 'sids');

		$sg_model = new StoreGoods();
		$cg_model = new CompanyGoods();

		if($data['type'] == 1){
			$type = 'in';
		}
		else{
			$type = 'out';
		}

		foreach($goods_list as $key=>$val){
			if($type == 'in'){
				$db_set = ['in_price'=>$val['in_price']];
			}
			elseif($type == 'out') {
				$db_set = [
					'out_price1'=>$val['out_price1'],
					'out_price2'=>$val['out_price2'],
					'out_price3'=>$val['out_price3'],
					'out_price4'=>$val['out_price4'],
				];
			}
			if($sids != -1){
				$sid_list =explode(',', $sids);
				foreach($sid_list as $sid){
					if($sid){
						//修改仓库级别价格
						$sg_res = $sg_model->read_one([
							'in_sid' => $sid,
							'gid' => $val['gid']
						]);
						if($sg_res){
							//如果存在，update
							$sg_model->update($db_set, ['id'=>$sg_res['id']]);
						}
						else{
							//如果不存在，create
							$db_set_temp = $db_set;
							$db_set_temp['in_sid'] = $sid;
							$db_set_temp['in_cid'] = $data['cid'];
							$db_set_temp['gid'] = $val['gid'];
							$sg_model->create($db_set_temp);
						}
//						$cg_model->update($db_set, [
//							'AND'=>[
//								'in_cid' => $data['cid'],
//								'gid' => $val['gid']
//							]
//						]);
					}
				}
			}
			else{
				$cg_model->update($db_set, [
					'AND'=>[
						'in_cid' => $data['cid'],
						'gid' => $val['gid']
					]
				]);
				$sg_model->delete([
					'AND'=>[
						'in_cid' => $data['cid'],
						'gid' => $val['gid']
					]
				]);
//				if($type == 'in'){
//					$db_set2 = ['in_price'=>0];
//				}
//				elseif($type == 'out') {
//					$db_set2 = [
//						'out_price1'=>0,
//						'out_price2'=>0,
//						'out_price3'=>0,
//						'out_price4'=>0,
//					];
//				}
//				$sg_model->update($db_set2, [
//					'AND'=>[
//						'in_cid' => $data['cid'],
//						'gid' => $val['gid']
//					]
//				]);
			}
		}

		return True;
	}

	/**
	 * 获取价格
	 *
	 * @param int $gid 商品ID
	 * @param int $cid 公司ID
	 * @param int $sid 仓库ID
	 * @param string $price_name 价格名称
	 * @param int $iserr 是否报错
	 * @return string 价格
     */
	public function get_price($gid, $cid, $sid, $price_name, $iserr = 1){
		$sg_model = new StoreGoods();
		$cg_model = new CompanyGoods();
		$res = $sg_model->read_one([
			'gid'=>$gid,
			'in_sid'=>$sid
		]);
		//如果仓库没有价格或者取出价格为0
		if(!$res || !$res[$price_name] || $res[$price_name] == '0.00'){
			$res2 = $cg_model->read_one([
				'gid'=>$gid,
				'in_cid'=>$cid
			]);
			if(!$res2){
				if($iserr){
					error(3008);
				}
				else{
					$price = '-2';
				}
			}
			else{
				$price = $res2[$price_name];
			}
		}
		else{
			$price = $res[$price_name];
		}
		return $price;
	}

	//批量获取商品价格
	public function get_prices($gids, $cid, $sid, $price_name){
		$sg_model = new StoreGoods();
		$cg_model = new CompanyGoods();
		$sg_res = $sg_model->read_list_nopage([
			'gid'=>$gids,
			'in_sid'=>$sid
		]);
		$sg_res2 = [];
		foreach($sg_res as $val){
			$sg_res2[$val['gid']] = $val[$price_name];
		}

		$cg_res = $cg_model->read_list_nopage([
			'gid'=>$gids,
			'in_cid'=>$cid
		]);
		$cg_res2 = [];
		foreach($cg_res as $val){
			$cg_res2[$val['gid']] = $val[$price_name];
		}

		$result = [];
		foreach($gids as $gid){
			$sg_temp = get_value($sg_res2, $gid);
			if(!$sg_temp || $sg_temp == '0.00'){
				$cg_temp = get_value($cg_res2, $gid);
				if(!$cg_temp){
					$price = -2;
				}
				else{
					$price = $cg_temp;
				}
			}
			else{
				$price = $sg_temp;
			}
			$result[$gid] = $price;
		}
		return $result;
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

