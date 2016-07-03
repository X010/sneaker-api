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

class SettleSupplier extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*cid','*cname','*sids','*snames','*scid','*scname','stock_list','uid','uname','cuid',
		'cuname','checktime','status','amount_price','tax_price','negative_id','discount','memo','pay_type'];
	
	//搜索字段
	protected $search_data = ['id'];

	//可排序的字段
	protected $order_data = ['id','status'];

	protected $amount_data = ['amount_price','tax_price'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_settle_supplier', $id);
	}

	/**
	 * 新建供应商结算单
	 *
	 * @param array $data 字段列表
	 * @return int 结算单号
	 */
	public function my_create($data){
		//检测所传的单据号是否属于当前公司
		$stock_list = json_decode($data['stock_list'], True);

		$si_model = new StockIn();
		$so_model = new StockOut();

		$amount_price = 0;
		$tax_price = 0;
		$stock_ids = [];
		foreach($stock_list as $stock){
			if($stock['id'][0] == '2'){
				//出库
				$res = $so_model->my_power($stock['id'], [3,4], 2, 0);
				$amount_price = price_sub($amount_price, $res['amount']);
				$tax_price = price_sub($tax_price, $res['tax_amount']);
			}
			elseif($stock['id'][0] == '3'){
				//入库
				$res = $si_model->my_power($stock['id'], 2, 1, 0);
				$amount_price = price_add($amount_price, $res['amount']);
				$tax_price = price_add($tax_price, $res['tax_amount']);

			}
			if(in_array($stock['id'], $stock_ids)){
				error(3206);
			}
			$stock_ids[] = $stock['id'];
		}

		start_action();
		//先写主表
		$data['stock_list'] = implode(',', $stock_ids);
		$data['discount'] = get_value($data, 'discount', 1);
		if($data['discount']<0 || $data['discount']>1){
			$data['discount'] = 1;
		}
		$data['amount_price'] = $amount_price*$data['discount'];

		if($data['amount_price'] < 0){
			error(3406);
		}

		$data['tax_price'] = $tax_price*$data['discount'];
		$id = $this->create($data);
		//将结算单号字段更新写入出入库单中
		$db_set = [
			'settle_id'=>$id,
			'pay_type'=>$data['pay_type']
		];
		foreach($stock_list as $stock){
			if($stock['id'][0] == '2'){
				//出库
				$db_where = [
					'id'=>$stock['id']
				];
				$so_model->update($db_set, $db_where);
			}
			elseif($stock['id'][0] == '3'){
				//入库
				$db_where = [
					'id'=>$stock['id']
				];
				$si_model->update($db_set, $db_where);
			}
		}

		//写明细表
		$sg_model = new SettleGlist();
		$sg_model -> my_create($id, $data['stock_list'], 1);

		return $id;
	}

	/**
	 * 更新供应商结算单
	 *
	 * @param array $data 字段列表
	 * @return int 结算单号
	 */
	public function my_update($data){
		//检测所传的单据号是否属于当前公司
		$stock_list = json_decode($data['stock_list'], True);

		$si_model = new StockIn();
		$so_model = new StockOut();

		$amount_price = 0;
		$tax_price = 0;
		$stock_ids = [];
		foreach($stock_list as $stock){
			if($stock['id'][0] == '2'){
				//出库
				$res = $so_model->my_power($stock['id'], [3,4], 2, 0);
				$amount_price = price_sub($amount_price, $res['amount']);
				$tax_price = price_sub($tax_price, $res['tax_amount']);
			}
			elseif($stock['id'][0] == '3'){
				//入库
				$res = $si_model->my_power($stock['id'], 2, 1, 0);
				$amount_price = price_add($amount_price, $res['amount']);
				$tax_price = price_add($tax_price, $res['tax_amount']);
			}
			if(in_array($stock['id'], $stock_ids)){
				error(3206);
			}
			$stock_ids[] = $stock['id'];
		}

		start_action();
		//先写主表
		$data['stock_list'] = implode(',', $stock_ids);
		$data['discount'] = get_value($data, 'discount', 1);
		if($data['discount']<0 || $data['discount']>1){
			$data['discount'] = 1;
		}
		$data['amount_price'] = $amount_price*$data['discount'];

		if($data['amount_price'] < 0){
			error(3406);
		}

		$data['tax_price'] = $tax_price*$data['discount'];

		$this->update_by_id($data);
		//原先的结算单号全部设置为空
		$so_model->update(['settle_id'=>Null],['settle_id'=>$this->id]);
		$si_model->update(['settle_id'=>Null],['settle_id'=>$this->id]);

		//将结算单号字段更新写入出入库单中
		$db_set = [
			'settle_id'=>$this->id,
			'pay_type'=>$data['pay_type']
		];
		foreach($stock_list as $stock){
			if($stock['id'][0] == '2'){
				//出库
				$db_where = [
					'id'=>$stock['id']
				];
				$so_model->update($db_set, $db_where);
			}
			elseif($stock['id'][0] == '3'){
				//入库
				$db_where = [
					'id'=>$stock['id']
				];
				$si_model->update($db_set, $db_where);
			}
		}

		//写明细表
		$sg_model = new SettleGlist();
		$sg_model -> my_update($this->id, $data['stock_list'], 1);

		return $this->id;
	}


	/**
	 * 结算供应商结算单
	 *
	 * @param array $data 字段列表
	 * @param string $type 类型，新增或更新
	 * @return int 结算单号
	 */
	public function my_check($data, $type){
		$data['status'] = 2;
		$data['checktime'] = date('Y-m-d H:i:s');

		if($type == 'create'){
			$this->id = $this->my_create($data);
		}
		elseif($type == 'update'){
			$this->my_update($data);
		}

		//结算
		$stock_list = json_decode($data['stock_list'], True);

		$si_model = new StockIn();
		$so_model = new StockOut();
		//写结算状态和
		$db_set = [
			'settle_status'=>1,
			'settletime'=>date('Y-m-d H:i:s')
		];
		Power::set_oper($db_set, 'ruid', 'runame');

		foreach($stock_list as $stock){
			if($stock['id'][0] == '2'){
				//出库
				$db_where = [
					'id'=>$stock['id']
				];
				$so_model->update($db_set, $db_where);
			}
			elseif($stock['id'][0] == '3'){
				//入库
				$db_where = [
					'id'=>$stock['id']
				];
				$si_model->update($db_set, $db_where);
			}
		}

		return $this->id;
	}

	/**
	 * 查询客户结算单详情
	 *
	 * @return array
	 */
	public function my_read(){
		$res = $this->read_by_id();
		$stock_list = explode(',', $res[0]['stock_list']);

		$si_model = new StockIn();
		$so_model = new StockOut();

		$stock_list2 = [];
		foreach($stock_list as $stock_id){
			if($stock_id[0] == '2'){
				$so_res = $so_model->read_by_id($stock_id);
				$so_res[0]['stock_type'] = 1;
				$stock_list2[] = $so_res[0];

			}
			elseif($stock_id[0] == '3'){
				$si_res = $si_model->read_by_id($stock_id);
				$si_res[0]['stock_type'] = 2;
				$stock_list2[] = $si_res[0];
			}
		}

		$res[0]['stock_list'] = $stock_list2;
		$sg_model = new SettleGlist();
		$tax_group = $sg_model->get_tax_group($this->id, 2);
		$discount = $res[0]['discount'];
		if($discount<0 || $discount>1){
			$discount = 1;
		}
		foreach($tax_group as $key=>$val){
			$amount_temp = $val['amount_price'];
			$tax_group[$key]['amount_price'] = format_yuan($amount_temp*$discount);
			$tax_group[$key]['discount_price'] = price_sub($amount_temp, $amount_temp*$discount);
			$tax_group[$key]['tax_price'] = format_yuan($val['tax_price']*$discount);
		}
		$res[0]['tax_group'] = $tax_group;

		return $res;

	}

	/**
	 * 查询供应商结算单商品详情
	 *
	 * @return array
	 */
	public function my_read_detail(){
		$res = $this->read_by_id();

		$sg_model = new SettleGlist();
		$sg_res = $sg_model->read_list([
			'settle_id'=>$this->id,
			'type'=>1
		]);
		$res[0]['goods_list'] = $sg_res['data'];

		$tax_group = $sg_model->get_tax_group($this->id, 2);
		$discount = $res[0]['discount'];
		if($discount<0 || $discount>1){
			$discount = 1;
		}
		foreach($tax_group as $key=>$val){
			$amount_temp = $val['amount_price'];
			$tax_group[$key]['amount_price'] = format_yuan($amount_temp*$discount);
			$tax_group[$key]['discount_price'] = price_sub($amount_temp, $amount_temp*$discount);
			$tax_group[$key]['tax_price'] = format_yuan($val['tax_price']*$discount);
		}
		$res[0]['tax_group'] = $tax_group;

		return $res;
	}

	/**
	 * 读取单据列表
	 *
	 * @param array $data 检索字段列表
	 * @param string $type 类型，新增或更新
	 * @return int 结算单号
	 */
	public function read_stock($data){
		$si_model = new StockIn();
		$so_model = new StockOut();
		$result = [];
		//先找退货的单据
		//条件1-本公司单据 2-供应商 3审核时间在时间段内 4-未结算过的，已审核的 5-必须是退货单 6-仓库属性（可选）
		$so_data = [
			'cid'=>$this->app->Sneaker->cid,
			'in_cid'=>$data['scid'],
			'status'=>[3,4],
			'checktime[>=]'=>$data['begintime'],
			'checktime[<=]'=>$data['endtime'],
			'settle_id'=>'null',
			'type'=>2,
			'business'=>1
		];
		if(get_value($data, 'sids')){
			$so_data['sid'] = explode(',',$data['sids']);
		}
		$res = $so_model->read_list($so_data);
		if($res['count']){
			foreach($res['data'] as $key=>$val){
				$res['data'][$key]['stock_type'] = 1;
			}
		}

		$result = $res['data'];

		//再找进货的单据
		//条件1-本公司单据 2-供应商 3审核时间在时间段内 4-结算日在基准日之前 5-未结算过的，已审核的 6-必须是采购单 7-仓库属性（可选）
		$si_data = [
			'cid'=>$this->app->Sneaker->cid,
			'out_cid'=>$data['scid'],
			'status'=>2,
			'checktime[>=]'=>$data['begintime'],
			'checktime[<=]'=>$data['endtime'],
			'lastdate[<=]'=>$data['basedate'],
			'settle_id'=>'null',
			'type'=>1,
			'business'=>1
		];
		if(get_value($data, 'sids')){
			$si_data['sid'] = explode(',',$data['sids']);
		}
		$res = $si_model->read_list($si_data);
		if($res['count']){
			foreach($res['data'] as $key=>$val){
				$val['stock_type'] = 2;
				$result[] = $val;
			}
		}

		return $result;
	}

	/**
	 * 冲正单号
	 *
	 * @return int 冲正单号
	 */
	public function my_flush(){

		$res = $this->read_by_id();

		$sg_model = new SettleGlist();
		$sg_res = $sg_model->read_list_nopage([
			'settle_id'=>$this->id,
			'type'=>1
		]);

		//先生成一个负单
		$data = $res[0];
		$data['amount_price'] = price_neg($data['amount_price']);
		$data['tax_price'] = price_neg($data['tax_price']);
		unset($data['id']);
		$data['status'] = 11;

		Power::set_oper($data);
		Power::set_oper($data, 'cuid', 'cuname');
		$data['checktime'] = date('Y-m-d H:i:s');

		start_action();
		$negative_id = $this->create($data);

		//生成负单明细
		$sg_data = [];
		foreach($sg_res as $val){
			unset($val['id']);
			$val['total'] = 0-$val['total'];
			$val['amount_price'] = price_neg($val['amount_price']);
			$val['tax_price'] = price_neg($val['tax_price']);
			$val['settle_id'] = $negative_id;
			$sg_data[] = $val;
		}
		$sg_model->create_batch($sg_data, 0);

		//修改原单为已冲正
		$db_set = [
			'negative_id'=>$negative_id,
			'status'=>10
		];
		$this->update_by_id($db_set);

		//原单的出入库单，冲正单号清空，状态改成已审核，清空结算时间
		$so_model = new StockOut();
		$si_model = new StockIn();

		$db_set = [
			'settle_id'=>Null,
			'settletime'=>Null,
			'settle_status'=>0,
			'ruid'=>Null,
			'runame'=>Null
		];

		$so_model->update($db_set,['settle_id'=>$this->id]);
		$si_model->update($db_set,['settle_id'=>$this->id]);

		return $negative_id;
	}

	public function my_delete($data){
		start_action();
		$this->update_by_id($data);

		//更新单据结算单号为空
		//原单的出入库单，冲正单号清空，状态改成已审核，清空结算时间
		$so_model = new StockOut();
		$si_model = new StockIn();
		$so_model->update([
			'settle_id'=>Null,
		],['settle_id'=>$this->id]);
		$si_model->update([
			'settle_id'=>Null,
		],['settle_id'=>$this->id]);

		return True;
	}

	/**
	 * 权限检测
	 *
	 * @param int $id 单据ID
	 * @param int $status 状态，为0时不检测
	 * @return array 字段列表
	 */
	public function my_power($id, $status){
		$res = $this->read_by_id($id);
		if(!$res){
			error(3402);
		}
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(3403);
		}
		if($status && $res[0]['status'] != $status){
			error(3404);
		}
		return $res[0];
	}

}

