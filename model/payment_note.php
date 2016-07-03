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

class PaymentNote extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['*cid','*cname','*dcid','*dcname','status','amount_price','uid','uname','cuid','cuname',
		'checktime','negative_id','pay_type'];

	//搜索字段
	protected $search_data = ['id'];

	//需要分和元转换的金额字段
	protected $amount_data = ['amount_price'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_payment_note', $id);
	}

	/**
	 * 创建付款单
	 *
	 * @param array $data 单据字段列表
	 * @return int 付款单号
     */
	public function my_create($data){

		start_action();
		$account_list = json_decode($data['account_list'], True);
		$account_config = $this->app->config('config_account');

		$amount_price = 0;
		foreach($account_list as $val){
			$amount_price = price_add($amount_price, $val['amount_price']);
		}
		$data['amount_price'] = $amount_price;

		$payment_id = $this->create($data);
		foreach($account_list as $key=>$val){
			$account_list[$key]['payment_id'] = $payment_id;
			$account_list[$key]['account_name'] = get_value($account_config, $val['account_id'], '未知科目');
		}

		$pnd_model = new PaymentNoteDetail();
		$pnd_model->create_batch($account_list, 0);

		return $payment_id;
	}

	/**
	 * 更新付款单
	 *
	 * @param array $data 单据字段列表
	 * @return int 付款单号
     */
	public function my_update($data){

		start_action();
		$account_list = json_decode($data['account_list'], True);
		$account_config = $this->app->config('config_account');

		$amount_price = 0;
		foreach($account_list as $val){
			$amount_price = price_add($amount_price, $val['amount_price']);
		}
		$data['amount_price'] = $amount_price;

		$this->update_by_id($data);

		foreach($account_list as $key=>$val){
			$account_list[$key]['payment_id'] = $this->id;
			$account_list[$key]['account_name'] = get_value($account_config, $val['account_id'], '未知科目');
		}

		$pnd_model = new PaymentNoteDetail();
		$pnd_model->delete_by_noteid($this->id);
		$pnd_model->create_batch($account_list, 0);

		return $this->id;

	}

	/**
	 * 审核付款单
	 *
	 * @param array $data 单据字段
	 * @param string $type 类型，新增或者修改
	 * @return int 付款单号
     */
	public function my_check($data, $type){
		if($type == 'create'){
			$id = $this->my_create($data);
		}
		elseif($type == 'update'){
			$id = $this->my_update($data);
		}

		return $id;
	}

	/**
	 * 读取单据详情和明细
	 *
	 * @return array
     */
	public function my_read(){
		$res = $this->read_by_id();

		$pnd_model = new PaymentNoteDetail();
		$dnd_res = $pnd_model->read_list([
			'payment_id'=>$this->id,
			'orderby'=>'id^ASC'
		]);

		$res[0]['account_list'] = $dnd_res['data'];
		return $res;

	}

	/**
	 * 单据权限和状态检测
	 *
	 * @param int $id 付款单号
	 * @param int $status 状态，为0时不检测
	 * @return array 单据字段
     */
	public function my_power($id, $status){
		$res = $this->read_by_id($id);
		if(!$res){
			error(3502);
		}
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(3503);
		}
		if($status && $res[0]['status'] != $status){
			error(3504);
		}
		return $res[0];
	}

	/**
	 * 冲正单据
	 *
	 * @return int 冲正单号
     */
	public function my_flush(){
		//冲正
		$res = $this->read_by_id();
		//先生成一个负单
		$data = $res[0];
		$data['amount_price'] = price_neg($data['amount_price']);
		unset($data['id']);
		$data['status'] = 11;

		Power::set_oper($data);
		Power::set_oper($data, 'cuid', 'cuname');

		start_action();
		$negative_id = $this->create($data);

		//修改原单为已冲正
		$db_set = [
			'negative_id'=>$negative_id,
			'status'=>10
		];
		$this->update_by_id($db_set);

		//生成完全相反的明细表
		$pnd_model = new PaymentNoteDetail();
		$pnd_res = $pnd_model->read_list([
			'payment_id'=>$this->id
		]);
		$pnd_data = $pnd_res['data'];
		foreach($pnd_data as $key=>$val){
			unset($pnd_data[$key]['id']);
			$pnd_data[$key]['payment_id'] = $negative_id;
			$pnd_data[$key]['amount_price'] = price_neg($val['amount_price']);
		}
		$pnd_model->create_batch($pnd_data, 0);

		return $negative_id;
	}
	
}

