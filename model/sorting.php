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

class Sorting extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['*cid','*cname','sid','sname','stock_list','uid','uname','status','car_id',
		'car_license','car_ton','areapro','areacity','areazone','areastreet','duid','duname','memo','ccid','ccname'];

	//搜索字段
	protected $search_data = ['id'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_sorting', $id);
	}

	public function my_create($data){
		$car_model = new Car($data['car_id']);
		$car_res = $car_model->read_by_id();
		start_action();

		//获取车辆信息写入主表
		$data['car_license'] = $car_res[0]['license'];
		$data['car_ton'] = $car_res[0]['ton'];

		$sorting_id = $this->create($data);

		//写明细表
		//先获取stock_list 商品详情
		$stock_list = explode(',', $data['stock_list']);

		$stock_res = $this->app->db->select('b_stock_out_glist','*',[
			'stock_out_id' => $stock_list,
			'ORDER'=> 'gid ASC'
		]);
		$sg_data = [];
		$cg_model = new CompanyGoods();
		$so_model = new StockOut();

		$so_res = $so_model->read_list_nopage(['id'=>$stock_list]);
		$ccnames = [];
		foreach($so_res as $val){
			$ccnames[$val['id']] = $val['in_cname'];
		}

		foreach($stock_res as $val){

			$cg_res = $cg_model->read_one([
				'in_cid'=>$data['cid'],
				'gid'=>$val['gid']]
			);
			$sg_data[] = [
				'sorting_id' => $sorting_id,
				'stock_id' => $val['stock_out_id'],
				'gid' => $val['gid'],
				'gcode' => $val['gcode'],
				'gname' => $val['gname'],
				'gbarcode' => $val['gbarcode'],
				'gunit' => $val['gunit'],
				'gspec' => $val['gspec'],
				'total' => $val['total'],
				'ccname' => get_value($ccnames, $val['stock_out_id']),
				'weight' => $cg_res['weight']
			];
		}
		$sg_model = new SortingGlist();
		$sg_model -> create_batch($sg_data, 0);

		//反写出库单，车辆数据
		$so_model->update(['sorting_id'=>$sorting_id,'car_license'=>$data['car_license']],
			['id'=>$stock_list]);

		//通知商城
		$o_model = new Order();
		$mall_model = new Mall();
		$u_model = new User($data['duid']);
		$u_res = $u_model->read_by_id();
		if($u_res){
			$data['duname'] .= '('. $u_res[0]['phone'].')';
		}

		$msg = "司机：".$data['duname']." 车牌号：".$data['car_license'];
		foreach($stock_list as $stock){
			$so_res = $so_model->read_by_id($stock);
			$order_id = $so_res[0]['order_id'];
			if($order_id){
				$o_res = $o_model->read_by_id($order_id);
				if($o_res && $o_res[0]['mall_orderno']){
					$mall_model->notice_order($o_res[0]['mall_orderno'], 11, $msg);
				}
			}
		}

		return $sorting_id;
	}

	public function my_read(){
		$res = $this->read_by_id();

		$sg_model = new SortingGlist();
		$sg_res = $sg_model->read_list_nopage([
			'sorting_id'=>$this->id,
			'orderby'=>'gid^asc'
		]);

		$res2 = [];
		$totals = [];
		foreach($sg_res as $key=>$val){
			$res2[$val['gid']][] = $val;

			if(!isset($totals[$val['gid']])){
				$totals[$val['gid']] = 0;
			}
			$totals[$val['gid']] += $val['total'];
		}

		foreach($res2 as $key=>$val){
			foreach($val as $key2=>$val2){
				$group_total = get_value($totals, $val2['gid']);
				$group_weight = ($group_total/$val2['gspec'])*$val2['weight'];
				$res2[$key][$key2]['group_total'] = $group_total;
				$res2[$key][$key2]['group_box_total'] = sprintf('%.4f', $group_total/$val2['gspec']);
				$res2[$key][$key2]['group_weight'] = sprintf('%.2f', $group_weight);
			}
		}


		$res[0]['sorting_glist'] = dict2list($res2);



		return $res[0];
	}

	public function read_stock($data){
		$so_model = new StockOut();
		$data['sorting_id'] = 'null';
		$data['status'] = 4;
		$data['type'] = 1;
		if(get_value($data, 'address')){
			$data['receipt[~]'] = '%'.$data['address'].'%';
		}
		$so_res = $so_model->read_list_nopage($data);

		$ccnames = [];
		foreach($so_res as $val){
			$ccnames[$val['id']] = $val['in_cname'];
		}

		$soids = [];
		foreach($so_res as $val){
			$soids[] = $val['id'];
		}
		if(!$soids){
			$soids = Null;
		}

		$need_data = ['gid','gname','gcode','gbarcode','gunit','gspec','gbid','gtid','stock_out_id','total'];

		$res = $this->app->db->select('b_stock_out_glist',$need_data,[
			'stock_out_id'=>$soids,
			'ORDER'=>'gid ASC'
		]);

		$cg_model = new CompanyGoods();

		$gids = [];
		foreach($res as $key=>$val){
			$gids[] = $val['gid'];
		}
		$cg_res = $cg_model->read_list_nopage([
			'in_cid'=>$data['cid'],
			'gid'=>$gids
		]);
		$cg_data = [];
		foreach($cg_res as $val){
			$cg_data[$val['gid']] = $val;
		}

		foreach($res as $key=>$val){
			$cg_res = get_value($cg_data, $val['gid']);
			if(!$cg_res){
				$cg_model->my_error(3008, $val['gid'], $data['cid']);
			}
			$res[$key]['weight'] = $cg_res['weight'];
			$stock_id = $val['stock_out_id'];
			$res[$key]['ccname'] = get_value($ccnames, $stock_id);
		}

		$res2 = [];
		$totals = [];
		foreach($res as $key=>$val){
			$res2[$val['gid']][] = $val;

			if(!isset($totals[$val['gid']])){
				$totals[$val['gid']] = 0;
			}
			$totals[$val['gid']] += $val['total'];
		}

		foreach($res2 as $key=>$val){
			foreach($val as $key2=>$val2){
				$group_total = get_value($totals, $val2['gid']);
				$group_weight = ($group_total/$val2['gspec'])*$val2['weight'];
				$res2[$key][$key2]['group_total'] = $group_total;
				$res2[$key][$key2]['group_box_total'] = sprintf('%.4f', $group_total/$val2['gspec']);
				$res2[$key][$key2]['group_weight'] = sprintf('%.2f', $group_weight);
			}
		}

		$result = [
			'data'=>dict2list($res2)
		];
		return $result;
	}

	public function my_delete(){

		$this->update_by_id([
			'status'=>9
		]);
		$so_model = new StockOut();
		$so_model->update(['sorting_id'=>NULL,'car_license'=>NULL],
			[
				'AND'=>[
					'sorting_id'=>$this->id,
					'cid'=>$this->app->Sneaker->cid
				]
			]);

		$res = $this->read_by_id();
		$stock_list = explode(',', $res[0]['stock_list']);

		//通知商城
		$o_model = new Order();
		$mall_model = new Mall();
		if($this->app->Sneaker->uname){
			$msg = "操作员：". $this->app->Sneaker->uname;
		}
		else{
			$msg = "";
		}
		foreach($stock_list as $stock){
			$so_res = $so_model->read_by_id($stock);
			$order_id = $so_res[0]['order_id'];
			if($order_id){
				$o_res = $o_model->read_by_id($order_id);
				if($o_res && $o_res[0]['mall_orderno']){
					$mall_model->notice_order($o_res[0]['mall_orderno'], 12, $msg);
				}
			}
		}

		return True;

	}
	
}

