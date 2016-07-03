<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * visit
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class Visit extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['cid','cname','order_id','ccid','ccname','uid','uname','type','score_service',
		'score_deliver','score_goods','score_salesman','score_activity','memo'];

	//可排序的字段
	protected $order_data = ['id','gid'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_visit', $id);
	}

	public function my_create($data){
		start_action();

		$id = $this->create($data);
		if($data['type'] == 2){
			$o_model = new Order();
			$o_model->update(['visit_status'=>2],['id'=>$data['order_id']]);
		}
		elseif($data['type'] == 1){
			$c_model = new Customer();
			$c_model->update(['last_visit_time'=>date('Y-m-d H:i:s')],[
				'AND'=>[
					'cid'=>$data['cid'],
					'ccid'=>$data['ccid']
				]
			]);
		}

		return $id;
	}

	public function my_power($id, $type){
		$res = $this->read_by_id($id);
		if(!$res){
			error(3910);
		}
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(3990);
		}
		if($type && $res[0]['type'] != $type){
			error(3911);
		}
		return $res[0];
	}
	
}

