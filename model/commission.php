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

class Commission extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['*cid','*cname','suid','suname','stock_list','uid','uname','cuid','cuname','status',
		'checktime','amount','negative_id','commission_amount','customer_count','commission_rate_amount','begin_date',
		'end_date','memo','commission_real_amount'];

	//搜索字段
	protected $search_data = ['id'];

	protected $amount_data = ['amount','commission_amount','commission_real_amount'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_commission', $id);
	}

	/**
	 * 新建提成结算单
	 *
	 * @param $data
	 * @return False|int
     */
	public function my_create($data){
		//$data['status'] = 1;
		$stock_list = explode(',', $data['stock_list']);

		$goods_list2 = json_decode($data['goods_list'], True);
		$goods_list = [];
		foreach($goods_list2 as $val){
			if($val['gid']){
				$goods_list[] = $val;
			}
		}

		//找出所有包含赠品的单据
		$res0 = $this->app->db->select('b_stock_out_glist','*',[
			'AND'=>[
				'stock_out_id'=>$stock_list,
				'unit_price'=>0
			]
		]);
		$stock_list0 = [];
		foreach($res0 as $val){
			$stock_list0[] = $val['stock_out_id'];
		}


		//找到提交单据里的商品列表（只找单价大于0的商品）
		$stock_res = $this->app->db->select('b_stock_out_glist','*',[
			'AND'=>[
				'stock_out_id'=>$stock_list,
				'unit_price[>]'=>0
			],
			'ORDER'=> 'gid ASC'
		]);
		$amount = 0;
		$commission_amount = 0;
		$commission_real_amount = 0;

		//计算商品总价格
		foreach($stock_res as $key=>$val){
			$amount = price_add($amount, fen2yuan($val['amount_price']));

			//判断商品同单据是否包含赠品
			if(in_array($val['stock_out_id'], $stock_list0)){
				$stock_res[$key]['is_activity'] = 1;
			}
			else{
				$stock_res[$key]['is_activity'] = 0;
			}
		}
		$data['amount'] = $amount;

		//分析提交数据中的总应提和总实提
		foreach($goods_list as $val){
			$commission_amount = price_add($commission_amount, $val['commission_amount']);
			$commission_real_amount = price_add($commission_real_amount, $val['commission_real_amount']);
		}
		$data['commission_amount'] = $commission_amount;
		$data['commission_real_amount'] = $commission_real_amount;

		//开启事务
		start_action();
		//创建提成单主表
		$commission_id = $this->create($data);

		$gid_dict = [];
		foreach($goods_list as $val){
			$gid_dict[$val['gid'].'_'.$val['is_activity']] = $val;
		}

		$sg_data = [];

		//创建明细表
		foreach($stock_res as $val){
			$gid_temp = get_value($gid_dict, $val['gid'].'_'.$val['is_activity'], []);
			$sg_data[] = [
				'commission_id' => $commission_id,
				'stock_id' => $val['stock_out_id'],
				'stock_type' => 1,
				'gid' => $val['gid'],
				'gcode' => $val['gcode'],
				'gname' => $val['gname'],
				'gbarcode' => $val['gbarcode'],
				'gunit' => $val['gunit'],
				'gspec' => $val['gspec'],
				'gtax_rate' => $val['gtax_rate'],
				'total' => $val['total'],
				'unit_price' => fen2yuan($val['unit_price']),
				'amount_price' => fen2yuan($val['amount_price']),
				'commission_amount' => get_value($gid_temp, 'commission_amount'),
				'commission_real_amount' => get_value($gid_temp, 'commission_real_amount'),
				'commission_rate' => get_value($gid_temp, 'commission_rate'),
				'commission_unit_price' => get_value($gid_temp, 'commission_unit_price'),
				'memo' => get_value($gid_temp, 'memo'),
				'is_activity' => $val['is_activity']
			];
		}
		$cg_model = new CommissionGlist();
		$cg_model -> create_batch($sg_data, 0);

		return $commission_id;
	}

	/**
	 * 更新客户结算单
	 *
	 * @param array $data 字段列表
	 * @return int 结算单号
	 */
	public function my_update($data){

		$stock_list = explode(',', $data['stock_list']);
		$goods_list2 = json_decode($data['goods_list'], True);
		$goods_list = [];
		foreach($goods_list2 as $val){
			if($val['gid']){
				$goods_list[] = $val;
			}
		}

		//找出所有包含赠品的单据
		$res0 = $this->app->db->select('b_stock_out_glist','*',[
			'AND'=>[
				'stock_out_id'=>$stock_list,
				'unit_price'=>0
			]
		]);
		$stock_list0 = [];
		foreach($res0 as $val){
			$stock_list0[] = $val['stock_out_id'];
		}

		//找到提交单据里的商品列表（只找单价大于0的商品）
		$stock_res = $this->app->db->select('b_stock_out_glist','*',[
			'AND'=>[
				'stock_out_id'=>$stock_list,
				'unit_price[>]'=>0
			],
			'ORDER'=> 'gid ASC'
		]);
		$amount = 0;
		$commission_amount = 0;
		$commission_real_amount = 0;

		//计算商品总价格
		foreach($stock_res as $key=>$val){
			$amount = price_add($amount, fen2yuan($val['amount_price']));

			//判断商品同单据是否包含赠品
			if(in_array($val['stock_out_id'], $stock_list0)){
				$stock_res[$key]['is_activity'] = 1;
			}
			else{
				$stock_res[$key]['is_activity'] = 0;
			}

		}
		$data['amount'] = $amount;

		//分析提交数据中的总应提和总实提
		foreach($goods_list as $val){
			$commission_amount = price_add($commission_amount, $val['commission_amount']);
			$commission_real_amount = price_add($commission_real_amount, $val['commission_real_amount']);
		}
		$data['commission_amount'] = $commission_amount;
		$data['commission_real_amount'] = $commission_real_amount;

		//开启事务
		start_action();
		//更新主表
		$this->update_by_id($data);

		$gid_dict = [];
		foreach($goods_list as $val){
			$gid_dict[$val['gid'].'_'.$val['is_activity']] = $val;
		}

		$sg_data = [];

		foreach($stock_res as $val){
			$gid_temp = get_value($gid_dict, $val['gid'].'_'.$val['is_activity'], []);
			$sg_data[] = [
				'commission_id' => $this->id,
				'stock_id' => $val['stock_out_id'],
				'stock_type' => 1,
				'gid' => $val['gid'],
				'gcode' => $val['gcode'],
				'gname' => $val['gname'],
				'gbarcode' => $val['gbarcode'],
				'gunit' => $val['gunit'],
				'gspec' => $val['gspec'],
				'gtax_rate' => $val['gtax_rate'],
				'total' => $val['total'],
				'unit_price' => fen2yuan($val['unit_price']),
				'amount_price' => fen2yuan($val['amount_price']),
				'commission_amount' => get_value($gid_temp, 'commission_amount'),
				'commission_real_amount' => get_value($gid_temp, 'commission_real_amount'),
				'commission_rate' => get_value($gid_temp, 'commission_rate'),
				'commission_unit_price' => get_value($gid_temp, 'commission_unit_price'),
				'memo' => get_value($gid_temp, 'memo'),
				'is_activity' => $val['is_activity']
			];
		}

		//先删除旧的明细表，在重新添加新明细
		$cg_model = new CommissionGlist();
		$cg_model -> delete([
			'commission_id' => $this->id
		]);
		$cg_model -> create_batch($sg_data, 0);

		return $this->id;
	}


	/**
	 * 审核客户结算单
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

		//反写出库单状态
		$so_model = new StockOut();
		$stock_list = explode(',', $data['stock_list']);

		foreach($stock_list as $stock_id){
			$temp = $so_model->read_by_id($stock_id);
			//如果出库单中包含已提现的单据，报错
			if($temp[0]['commission_status'] == 1){
				error(3901);
			}
		}

		$so_model->update(['commission_status'=>1,'commission_id'=>$this->id],
			['id'=>$stock_list]);

		return $this->id;
	}

	/**
	 * 查询单据明细
	 *
	 * @return mixed
     */
	public function my_read(){
		$res = $this->read_by_id();

		//按商品查询明细表
		$cg_model = new CommissionGlist();
		$sg_res = $cg_model->read_list_nopage([
			'commission_id'=>$this->id,
			'orderby'=>'gid^asc'
		]);

		$res2 = [];
		$totals = [];
		$amounts = [];

		//分组显示商品总数和金额
		foreach($sg_res as $key=>$val){

			$key_name = $val['gid'].'_'.$val['is_activity'];
			$res2[$key_name][] = $val;

			if(!isset($totals[$key_name])){
				$totals[$key_name] = 0;
			}
			if(!isset($amounts[$key_name])){
				$amounts[$key_name] = 0;
			}
			$totals[$key_name] += $val['total'];
			$amounts[$key_name] = price_add($amounts[$key_name], $val['amount_price']);
		}

		foreach($res2 as $key=>$val){
			foreach($val as $key2=>$val2){
				$key_name = $val2['gid'].'_'.$val2['is_activity'];
				$group_total = get_value($totals, $key_name);
				$group_amount = get_value($amounts, $key_name);
				$res2[$key][$key2]['group_total'] = $group_total;
				$res2[$key][$key2]['group_amount'] = $group_amount;
				$res2[$key][$key2]['box_total'] = sprintf('%.4f', $val2['total']/$val2['gspec']);
				$res2[$key][$key2]['group_box_total'] = sprintf('%.4f', $group_total/$val2['gspec']);
			}
		}

		//dict转成list
		$res[0]['commission_glist'] = dict2list($res2);

		return $res[0];
	}

	/**
	 * 读取单据信息
	 *
	 * @param $data
	 * @return array
     */
	public function read_stock($data){
		$so_model = new StockOut();
		$data['commission_status'] = '0';
		$data['settle_status'] = 1;
		//$data['status'] = 4;
		$data['type'] = 1;
		$data['settletime[>=]'] = $data['begin_date']. ' 00:00:00';
		$data['settletime[<=]'] = $data['end_date']. ' 23:59:59';
		unset($data['begin_date']);
		unset($data['end_date']);
		//查询已结算，未提成的单据
		$so_res = $so_model->read_list_nopage($data);

		$soids = [];
		foreach($so_res as $val){
			$soids[] = $val['id'];
		}
		if(!$soids){
			$soids = Null;
		}

		$need_data = '*';
		//找出所有包含赠品的单据
		$res0 = $this->app->db->select('b_stock_out_glist',$need_data,[
			'AND'=>[
				'stock_out_id'=>$soids,
				'unit_price'=>0
			]
		]);
		$stock_list0 = [];
		foreach($res0 as $val){
			$stock_list0[] = $val['stock_out_id'];
		}


		//$need_data = ['gid','gname','gcode','gbarcode','gunit','gspec','gbid','gtid','stock_out_id','total'];

		//提成的时候，赠品不能列入，必须是单价大于0的商品
		$res = $this->app->db->select('b_stock_out_glist',$need_data,[
			'AND'=>[
				'stock_out_id'=>$soids,
				'unit_price[>]'=>0
			],
			'ORDER'=>'gid ASC'
		]);

		foreach($res as $key=>$val){
			$res[$key]['unit_price'] = fen2yuan($val['unit_price']);
			$res[$key]['amount_price'] = fen2yuan($val['amount_price']);
			$res[$key]['stock_id'] = $val['stock_out_id'];

			//判断商品同单据是否包含赠品
			if(in_array($val['stock_out_id'], $stock_list0)){
				$res[$key]['is_activity'] = 1;
			}
			else{
				$res[$key]['is_activity'] = 0;
			}
		}

		$res2 = [];
		$totals = [];
		$amounts = [];
		//分组显示商品总数和金额
		foreach($res as $key=>$val){
			$key_name = $val['gid'].'_'.$val['is_activity'];
			$res2[$key_name][] = $val;

			if(!isset($totals[$key_name])){
				$totals[$key_name] = 0;
			}
			if(!isset($amounts[$key_name])){
				$amounts[$key_name] = 0;
			}
			$totals[$key_name] += $val['total'];
			$amounts[$key_name] = price_add($amounts[$key_name], $val['amount_price']);
		}

		foreach($res2 as $key=>$val){
			foreach($val as $key2=>$val2){
				$key_name = $val2['gid'].'_'.$val2['is_activity'];
				$group_total = get_value($totals, $key_name);
				$group_amount = get_value($amounts, $key_name);
				$res2[$key][$key2]['group_total'] = $group_total;
				$res2[$key][$key2]['group_amount'] = $group_amount;
				$res2[$key][$key2]['box_total'] = sprintf('%.4f', $val2['total']/$val2['gspec']);
				$res2[$key][$key2]['group_box_total'] = sprintf('%.4f', $group_total/$val2['gspec']);
			}
		}

		$suid = $data['suid'];
		//获取用户数返回
		$cs_model = new CustomerSalesman();
		$customer_count = $cs_model->count(['suid'=>$suid]);

		$result = [
			'data'=>dict2list($res2),
			'customer_count'=>$customer_count
		];
		return $result;
	}

	public function my_delete($data){

		$this->update_by_id($data);

		return True;

	}

	/**
	 * 冲正单号
	 *
	 * @return int 冲正单号
	 */
	public function my_flush(){

		$res = $this->read_by_id();

		$cg_model = new CommissionGlist();
		$cg_res = $cg_model->read_list_nopage([
			'commission_id'=>$this->id
		]);

		//先生成一个负单
		$data = $res[0];
		$data['amount'] = price_neg($data['amount']);
		$data['commission_amount'] = price_neg($data['commission_amount']);
		$data['commission_real_amount'] = price_neg($data['commission_real_amount']);
		unset($data['id']);
		$data['status'] = 11;

		Power::set_oper($data);
		Power::set_oper($data, 'cuid', 'cuname');
		$data['checktime'] = date('Y-m-d H:i:s');

		start_action();
		$negative_id = $this->create($data);

		//生成负单明细
		$cg_data = [];
		foreach($cg_res as $val){
			unset($val['id']);
			$val['total'] = 0-$val['total'];
			$val['amount_price'] = price_neg($val['amount_price']);
			$val['commission_amount'] = price_neg($val['commission_amount']);
			$val['commission_real_amount'] = price_neg($val['commission_real_amount']);
			$val['commission_id'] = $negative_id;
			$cg_data[] = $val;
		}
		$cg_model->create_batch($cg_data, 0);

		//修改原单为已冲正
		$db_set = [
			'negative_id'=>$negative_id,
			'status'=>10
		];
		$this->update_by_id($db_set);

		//原单的出库单修正
		$so_model = new StockOut();

		$db_set = [
			'commission_id'=>Null,
			'commission_status'=>0,
		];
		$so_model->update($db_set,['commission_id'=>$this->id]);

		return $negative_id;
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

	/**
	 * 业务员提成汇总报表
	 *
	 * @param $data
	 * @return array
     */
	public function form_salesman($data){
		$begin_date = get_value($data, 'begin_date');
		$end_date = get_value($data, 'end_date');
		$belong = get_value($data, 'belong');
		$suid =get_value($data, 'suid');
		$cid = get_value($data, 'cid');
		$ugid = get_value($data, 'ugid');

		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 200);
		$start_count = ($page - 1) * $page_num;

		$db_where = "checktime>='$begin_date 00:00:00' and checktime<='$end_date 23:59:59' and cid=$cid and `status`=2";
		$uid_list = [];
		//条件－自有员工还是外借员工
		if($belong) {
			$u_model = new User();
			$u_param = ['cid'=>$cid];
			$u_param['belong'] = $belong;
			$u_res = $u_model->read_list_nopage($u_param);
			foreach ($u_res as $val) {
				$uid_list[] = $val['id'];
			}
			if($uid_list){
				$db_where .= " and suid in (". implode(',',$uid_list). ")";
			}
			else{
				$db_where .= " and suid is null";
			}
		}

		if($ugid){
			$ug_model = new UserGroup();
			$ugids = $ug_model->get_ids_by_fid($ugid);
			$u_model = new User();
			$u_res = $u_model->read_list_nopage(['cid'=>$cid,'group_id'=>$ugids]);
			$uid_list = [];
			foreach($u_res as $val){
				$uid_list[] = $val['id'];
			}
			if($uid_list){
				$db_where .= " and suid in (".implode(',',$uid_list).")";
			}
			else{
				$db_where .= " and suid is null";
			}
		}

		if($suid){
			$db_where .= " and suid=$suid";
		}
		//总数和合计查询
		$count_sql = "select count(distinct(suid)) as val0,sum(amount) as val2,sum(commission_amount) as val3,".
			"sum(commission_real_amount) as val4 from b_commission where $db_where";
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$all_count = $count_res[0]['val0'];
		$add_up['amount'] = fen2yuan($count_res[0]['val2']);
		$add_up['commission_amount'] = fen2yuan($count_res[0]['val3']);
		$add_up['commission_real_amount'] = fen2yuan($count_res[0]['val4']);

		$all_page = intval($all_count/$page_num);
		if($all_count%$page_num!=0){
			$all_page ++;
		}
		//分页查询数据
		$sql = "select suid,suname,sum(amount) as val2,sum(commission_amount) as val3,sum(commission_real_amount) as val4".
			" from b_commission where $db_where group by suid order by val2 desc limit $start_count,$page_num";
		$res = $this->app->db->query($sql)->fetchAll();

		$result = [];
		foreach($res as $val){
			$result[] = [
				'suid'=>$val['suid'],
				'suname'=>$val['suname'],
				'amount'=>fen2yuan($val['val2']),
				'commission_amount'=>fen2yuan($val['val3']),
				'commission_real_amount'=>fen2yuan($val['val4']),
			];
		}

		return [
			'count'=>$all_count,
			'page_count'=>$all_page,
			'data'=>$result,
			'add_up'=>$add_up
		];

	}

	/**
	 * 业务员提成商品汇总报表
	 *
	 * @param $data
	 * @return array
	 */
	public function form_goods($data){
		$begin_date = get_value($data, 'begin_date');
		$end_date = get_value($data, 'end_date');
		$suid =get_value($data, 'suid');
		$cid = get_value($data, 'cid');
		$gid = get_value($data, 'gid');

		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 200);
		$start_count = ($page - 1) * $page_num;

		$db_where = "t1.checktime>='$begin_date 00:00:00' and t1.checktime<='$end_date 23:59:59' and t1.cid=$cid and t1.status=2";

		if($suid){
			$db_where .= " and t1.suid=$suid";
		}
		if($gid){
			$db_where .= " and t2.gid=$gid";
		}

		//总数和合计查询
		$count_sql = "select count(distinct(gid)) as val0,sum(val2) as val2,sum(val3) as val3,sum(val4) as val4 from (".
			"select gid,`commission_id`,sum(t2.amount_price) as val2,max(t2.commission_amount) as val3,max(t2.commission_real_amount) as val4
from b_commission t1 left join b_commission_glist t2 on t1.id=t2.commission_id where $db_where group by t2.gid,t2.commission_id,t2.is_activity) as tt1";
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$all_count = $count_res[0]['val0'];
		$add_up['amount'] = fen2yuan($count_res[0]['val2']);
		$add_up['commission_amount'] = fen2yuan($count_res[0]['val3']);
		$add_up['commission_real_amount'] = fen2yuan($count_res[0]['val4']);

		$all_page = intval($all_count/$page_num);
		if($all_count%$page_num!=0){
			$all_page ++;
		}

		//分页查询数据
		$sql = "select gid,gname,gcode,gbarcode,gspec,gunit,sum(val2) as val2,sum(val3) as val3,sum(val4) as val4 from (".
			"select gid,gname,`commission_id` ,gcode,gbarcode,gspec,gunit,sum(t2.amount_price) as val2,max(t2.commission_amount) as val3,max(t2.commission_real_amount) as val4
from b_commission t1 left join b_commission_glist t2 on t1.id=t2.commission_id where $db_where group by t2.gid,t2.commission_id,t2.is_activity) as tt1 group by tt1.gid order by val2 desc limit $start_count,$page_num";
		$res = $this->app->db->query($sql)->fetchAll();

		$result = [];
		foreach($res as $val){
			$result[] = [
				'gid'=>$val['gid'],
				'gname'=>$val['gname'],
				'gcode'=>$val['gcode'],
				'gbarcode'=>$val['gbarcode'],
				'gspec'=>$val['gspec'],
				'gunit'=>$val['gunit'],
				'amount'=>fen2yuan($val['val2']),
				'commission_amount'=>fen2yuan($val['val3']),
				'commission_real_amount'=>fen2yuan($val['val4']),
			];
		}

		return [
			'count'=>$all_count,
			'page_count'=>$all_page,
			'data'=>$result,
			'add_up'=>$add_up
		];

	}

}

