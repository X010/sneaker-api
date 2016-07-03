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

/**
 * TODO:
 */
class DebitNote extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['*cid','*cname','*dcid','*dcname','status','amount_price','uid','uname','cuid','cuname',
		'checktime','negative_id','pay_type','hash'];

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
		parent::__construct('b_debit_note', $id);
	}

	/**
	 * 创建单据
	 *
	 * @param array $data 插入字段列表
	 * @return int 收款单号
     */
	public function my_create($data){
		//开启事务
		start_action();
		$account_list = json_decode($data['account_list'], True);
		$account_config = $this->app->config('config_account');

		$amount_price = 0;
		//计算总价格，等于各科目价格相加
		foreach($account_list as $val){
			$amount_price = price_add($amount_price, $val['amount_price']);
		}
		$data['amount_price'] = $amount_price;
		//创建收款单
		$debit_id = $this->create($data);

		//补充收款科目名称
		foreach($account_list as $key=>$val){
			$account_list[$key]['debit_id'] = $debit_id;
			$account_list[$key]['account_name'] = get_value($account_config, $val['account_id'], '未知科目');
		}

		//插入收款明细
		$dnd_model = new DebitNoteDetail();
		$dnd_model->create_batch($account_list, 0);

		return $debit_id;
	}

	/**
	 * 修改单据
	 *
	 * @param array $data 更新的字段列表
	 * @return int 收款单号
     */
	public function my_update($data){
		//开启事务
		start_action();
		$account_list = json_decode($data['account_list'], True);
		$account_config = $this->app->config('config_account');

		$amount_price = 0;
		//计算总价格，等于各科目价格相加
		foreach($account_list as $val){
			$amount_price = price_add($amount_price, $val['amount_price']);
		}
		$data['amount_price'] = $amount_price;
		//修改收款单
		$this->update_by_id($data);
		//补充收款科目名称
		foreach($account_list as $key=>$val){
			$account_list[$key]['debit_id'] = $this->id;
			$account_list[$key]['account_name'] = get_value($account_config, $val['account_id'], '未知科目');
		}

		$dnd_model = new DebitNoteDetail();
		//删除原有科目明细
		$dnd_model->delete_by_noteid($this->id);
		//插入收款明细
		$dnd_model->create_batch($account_list, 0);

		return $this->id;

	}

	/**
	 * 审核单据
	 *
	 * @param array $data 字段列表
	 * @param string $type 类型，新增或修改
	 * @return int 收款单号
     */
	public function my_check($data, $type){
		$id = Null;
		if($type == 'create'){
			//创建并审核
			$id = $this->my_create($data);
		}
		elseif($type == 'update'){
			//修改并审核
			$id = $this->my_update($data);
		}

		return $id;
	}

	/**
	 * 读取单据详情和列表
	 *
	 * @return array
     */
	public function my_read(){
		$res = $this->read_by_id();

		//读取收款单明细列表
		$dnd_model = new DebitNoteDetail();
		$dnd_res = $dnd_model->read_list_nopage([
			'debit_id'=>$this->id,
			'orderby'=>'id^ASC'
		]);

		$res[0]['account_list'] = $dnd_res;
		return $res;

	}

	/**
	 * 检测单据权限状态
	 *
	 * @param int $id 单据ID
	 * @param int $status 检测状态，为0代表不检测单据状态
	 * @return array 返回单据字段
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
		$res = $this->read_by_id();

		$data = $res[0];
		$data['amount_price'] = price_neg($data['amount_price']);
		unset($data['id']);
		$data['status'] = 11;

		Power::set_oper($data);
		Power::set_oper($data, 'cuid', 'cuname');

		start_action();
		//先生成一个负单
		$negative_id = $this->create($data);

		//修改原单为已冲正
		$db_set = [
			'negative_id'=>$negative_id,
			'status'=>10
		];
		$this->update_by_id($db_set);

		//生成完全相反的明细表
		$dnd_model = new DebitNoteDetail();
		$dnd_data = $dnd_model->read_list_nopage([
			'debit_id'=>$this->id
		]);
		foreach($dnd_data as $key=>$val){
			unset($dnd_data[$key]['id']);
			$dnd_data[$key]['debit_id'] = $negative_id;
			$dnd_data[$key]['amount_price'] = price_neg($val['amount_price']);
		}
		$dnd_model->create_batch($dnd_data, 0);

		return $negative_id;
	}
	
}

