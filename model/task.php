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

class Task extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['*cid','*cname','suid','suname','year','type','uid','uname','status','memo','val_all',
		'val1','val2','val3','val4','val5','val6','val7','val8','val9','val10','val11','val12'];

	//搜索字段
	protected $search_data = ['id'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_task', $id);
	}

	public function my_create($data){

		$goods_list = get_value($data, 'goods_list');
		if($goods_list){
			$goods_list = json_decode($data['goods_list'], True);
		}
		$add_up = json_decode($data['add_up'], True);

		$check_res = $this->has([
			'suid'=>$data['suid'],
			'year'=>$data['year'],
			'type'=>$data['type'],
			'status'=>1
		]);
		if($check_res){
			error(3210);
		}

		$data['val1'] = $add_up['val1'];
		$data['val2'] = $add_up['val2'];
		$data['val3'] = $add_up['val3'];
		$data['val4'] = $add_up['val4'];
		$data['val5'] = $add_up['val5'];
		$data['val6'] = $add_up['val6'];
		$data['val7'] = $add_up['val7'];
		$data['val8'] = $add_up['val8'];
		$data['val9'] = $add_up['val9'];
		$data['val10'] = $add_up['val10'];
		$data['val11'] = $add_up['val11'];
		$data['val12'] = $add_up['val12'];
		$data['val_all'] = $add_up['val1']+$add_up['val2']+$add_up['val3']+$add_up['val4']+$add_up['val5']+$add_up['val6']+
			$add_up['val7']+$add_up['val8']+$add_up['val9']+$add_up['val10']+$add_up['val11']+$add_up['val12'];

		start_action();
		$task_id = $this->create($data);

		if($goods_list){
			$gid_list = [];
			foreach($goods_list as $val){
				if($val['gid']){
					if(in_array($val['gid'], $gid_list)){
						error(3211);
					}
					$gid_list[] = $val['gid'];
				}
			}
			$cg_model = new CompanyGoods();
			$cg_res = $cg_model->read_list_nopage(['in_cid'=>$data['cid'],'gid'=>$gid_list]);
			$gid_dict = [];
			foreach($cg_res as $val){
				$gid_dict[$val['gid']] = $val;
			}

			$tg_model = new TaskGlist();
			$tg_data = [];
			foreach($goods_list as $val){
				$gid_temp = get_value($gid_dict, $val['gid'], []);
				$val_all = $val['val1']+$val['val2']+$val['val3']+$val['val4']+$val['val5']+$val['val6']+
					$val['val7']+$val['val8']+$val['val9']+$val['val10']+$val['val11']+$val['val12'];
				$tg_data[] = [
					'task_id' => $task_id,
					'gid' => $gid_temp['gid'],
					'gcode' => $gid_temp['gcode'],
					'gname' => $gid_temp['gname'],
					'gbarcode' => $gid_temp['gbarcode'],
					'gunit' => $gid_temp['gunit'],
					'gspec' => $gid_temp['gspec'],
					'val1' => $val['val1'],
					'val2' => $val['val2'],
					'val3' => $val['val3'],
					'val4' => $val['val4'],
					'val5' => $val['val5'],
					'val6' => $val['val6'],
					'val7' => $val['val7'],
					'val8' => $val['val8'],
					'val9' => $val['val9'],
					'val10' => $val['val10'],
					'val11' => $val['val11'],
					'val12' => $val['val12'],
					'val_all' => $val_all,
				];
			}
			$tg_model -> create_batch($tg_data, 0);
		}
		return $task_id;
	}

	public function my_update($data){

		$goods_list = get_value($data, 'goods_list');
		if($goods_list){
			$goods_list = json_decode($data['goods_list'], True);
		}
		$add_up = json_decode($data['add_up'], True);

		$check_res = $this->has([
			'suid'=>$data['suid'],
			'year'=>$data['year'],
			'type'=>$data['type'],
			'id[!]'=>$this->id,
			'status'=>1
		]);
		if($check_res){
			error(3210);
		}

		$data['val1'] = $add_up['val1'];
		$data['val2'] = $add_up['val2'];
		$data['val3'] = $add_up['val3'];
		$data['val4'] = $add_up['val4'];
		$data['val5'] = $add_up['val5'];
		$data['val6'] = $add_up['val6'];
		$data['val7'] = $add_up['val7'];
		$data['val8'] = $add_up['val8'];
		$data['val9'] = $add_up['val9'];
		$data['val10'] = $add_up['val10'];
		$data['val11'] = $add_up['val11'];
		$data['val12'] = $add_up['val12'];
		$data['val_all'] = $add_up['val1']+$add_up['val2']+$add_up['val3']+$add_up['val4']+$add_up['val5']+$add_up['val6']+
			$add_up['val7']+$add_up['val8']+$add_up['val9']+$add_up['val10']+$add_up['val11']+$add_up['val12'];

		start_action();
		$this->update_by_id($data);

		if($goods_list) {
			$gid_list = [];
			foreach ($goods_list as $val) {
				if ($val['gid']) {
					if (in_array($val['gid'], $gid_list)) {
						error(3211);
					}
					$gid_list[] = $val['gid'];
				}
			}
			$cg_model = new CompanyGoods();
			$cg_res = $cg_model->read_list_nopage(['in_cid' => $data['cid'], 'gid' => $gid_list]);
			$gid_dict = [];
			foreach ($cg_res as $val) {
				$gid_dict[$val['gid']] = $val;
			}

			$tg_model = new TaskGlist();
			$tg_data = [];
			foreach ($goods_list as $val) {
				$gid_temp = get_value($gid_dict, $val['gid'], []);
				$val_all = $val['val1'] + $val['val2'] + $val['val3'] + $val['val4'] + $val['val5'] + $val['val6'] +
					$val['val7'] + $val['val8'] + $val['val9'] + $val['val10'] + $val['val11'] + $val['val12'];
				$tg_data[] = [
					'task_id' => $this->id,
					'gid' => $gid_temp['gid'],
					'gcode' => $gid_temp['gcode'],
					'gname' => $gid_temp['gname'],
					'gbarcode' => $gid_temp['gbarcode'],
					'gunit' => $gid_temp['gunit'],
					'gspec' => $gid_temp['gspec'],
					'val1' => $val['val1'],
					'val2' => $val['val2'],
					'val3' => $val['val3'],
					'val4' => $val['val4'],
					'val5' => $val['val5'],
					'val6' => $val['val6'],
					'val7' => $val['val7'],
					'val8' => $val['val8'],
					'val9' => $val['val9'],
					'val10' => $val['val10'],
					'val11' => $val['val11'],
					'val12' => $val['val12'],
					'val_all' => $val_all,
				];
			}
			$tg_model->delete(['task_id' => $this->id]);
			$tg_model->create_batch($tg_data, 0);
		}
		return $this->id;
	}

	public function my_read(){
		$res = $this->read_by_id();

		$tg_model = new TaskGlist();
		$sg_res = $tg_model->read_list_nopage([
			'task_id'=>$this->id,
			'orderby'=>'id^asc'
		]);

		$res[0]['goods_list'] = $sg_res;

		return $res[0];
	}

	public function my_delete($data){

		$this->update_by_id($data);

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

	//业务员任务达成情况查询
	public function form_salesman_task($data){
		$ugid = get_value($data, 'ugid');
		$suid = get_value($data, 'suid');
		$cid = get_value($data, 'cid');
		$sids = get_value($data, 'sids');
		$date = get_value($data, 'date');
		$belong = get_value($data, 'belong');

		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 200);
		$start_count = ($page - 1) * $page_num;

		//获取财务结账日
		$c_model = new Company();
		$c_res = $c_model->read_one(['id'=>$cid]);
		$finance_date = $c_res['financedate'];

		//确定年份
		$my_time = strtotime($date);

		if(!$finance_date){
			//如果没有设置财务结账日，使用自然月法则
			$year = date('Y', $my_time);
			$month = date('m', $my_time);
			$month_first_day = "$year-$month-01";
		}
		else{
			//如果设置了，使用规定法则，大于基准日月份加一，超过12月年份加一
			$year = date('Y', $my_time);
			$month = date('m', $my_time);
			$day = date('d', $my_time);
			$finance_date_temp = str_pad($finance_date,2,'0',STR_PAD_LEFT);

			if($day > $finance_date){
				$month_first_day = "$year-$month-$finance_date_temp";
				$month++;
				if($month > 12){
					$month = 1;
					$year ++;
				}
			}
			else{
				$month_temp = $month-1;
				if($month_temp < 1){
					$month_temp = 1;
					$year_temp = $year-1;
				}
				else{
					$year_temp = $year;
				}
				$month_first_day = "$year_temp-$month_temp-$finance_date_temp";
			}
			//第一天是财务结账日的下一日
			$month_first_day = date('Y-m-d',strtotime($month_first_day)+24*3600);
		}

		//找出所有业务员
		$cs_where = " t1.cid=t2.cid and t1.ccid=t2.ccid and t1.cid=$cid and t2.cid=$cid and t1.first_order_time is not null";
		$cs_where2 = " t1.cid=t2.cid and t1.ccid=t2.ccid and t1.cid=$cid and t2.cid=$cid";

		$uid_list = [];
		if($belong || $sids){
			$u_model = new User();

			$u_param = ['cid'=>$cid];
			if($belong){
				$u_param['belong'] = $belong;
			}
			$u_res = $u_model->read_list_nopage($u_param);

			if($sids){
				$sid_list = explode(',',$sids);
				foreach($u_res as $val){
					$user_sids = explode(',', $val['sids']);
					if(array_intersect($sid_list, $user_sids)){
						$uid_list[] = $val['id'];
					}
				}
			}
			else{
				foreach($u_res as $val){
					$uid_list[] = $val['id'];
				}
			}

			if($suid){
				if(in_array($suid, $uid_list)){
					$uid_list = [$suid];
				}
				else{
					$uid_list = [-1];
				}
			}
		}
		else{
			if($suid){
				$uid_list = [$suid];
			}
		}

		if($uid_list){
			$old_uid_where = " and t2.suid in (".implode(',', $uid_list).")";
		}
		else{
			$old_uid_where = "";
		}
		$cs_where .= $old_uid_where;
		$cs_where2 .= $old_uid_where;

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
				$cs_where .= " and t2.suid in (".implode(',',$uid_list).")";
				$cs_where2 .= " and t2.suid in (".implode(',',$uid_list).")";
			}
			else{
				$cs_where .= " and t2.suid is null";
				$cs_where2 .= " and t2.suid is null";
			}
		}

		$count_sql = "select count(distinct(t2.suid)) as val0,count(*) as val1 from r_customer t1,r_customer_salesman t2 where $cs_where2";
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$all_count = $count_res[0]['val0'];
		$add_up['all_customer_count2'] = $count_res[0]['val1'];

		$all_page = intval($all_count/$page_num);
		if($all_count%$page_num!=0){
			$all_page ++;
		}

		$count_sql = "select count(distinct(t2.suid)) as val0,count(*) as val1 from r_customer t1,r_customer_salesman t2 where $cs_where";
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		//$all_count = $count_res[0]['val0'];
		$add_up['all_customer_count'] = $count_res[0]['val1'];

//		$all_page = intval($all_count/$page_num);
//		if($all_count%$page_num!=0){
//			$all_page ++;
//		}

		$count_sql = "select count(*) as val1 from r_customer t1,r_customer_salesman t2 where $cs_where and t1.first_order_time>='$date 00:00:00' and t1.first_order_time<='$date 23:59:59'";
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$add_up['day_customer_count'] = $count_res[0]['val1'];

		$count_sql = "select count(*) as val1 from r_customer t1,r_customer_salesman t2 where $cs_where and t1.first_order_time>='$month_first_day 00:00:00' and t1.first_order_time<='$date 23:59:59'";
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$add_up['period_customer_count'] = $count_res[0]['val1'];

		$salesman_sql = "select t2.suid,t2.suname,sum(case when t1.first_order_time is not null then 1 else 0 end) as val0,count(*) as val1 from r_customer t1,r_customer_salesman t2 where $cs_where2 group by t2.suid order by val0 desc limit $start_count,$page_num";
		$salesman_res = $this->app->db->query($salesman_sql)->fetchAll();
		$salesman_ids = [];
		$result = [];
		foreach($salesman_res as $val){
			$salesman_ids[] = $val['suid'];
			$result[$val['suid']] = [
				'suid'=>$val['suid'],
				'suname'=>$val['suname'],
				'all_customer_count'=>$val['val0'],
				'all_customer_count2'=>$val['val1'],
				'day_customer_count'=>0,
				'period_customer_count'=>0,
				'day_order_count'=>0,
				'day_order_amount'=>0,
				'period_order_count'=>0,
				'period_order_amount'=>0,
				'period_box_total'=>0,
				'task_total'=>0,
				'complete_rate'=>'0%',
				'amount_task_total'=>0,
				'amount_complete_rate'=>'0%',
			];
		}
		if($salesman_ids){
			$salesman_where = " and t2.suid in (".implode(',',$salesman_ids).")";
		}
		else{
			$salesman_where = " and t2.suid is null";
		}

		$salesman_sql = "select t2.suid,count(*) as val0 from r_customer t1,r_customer_salesman t2 where $cs_where $salesman_where and ".
			"t1.first_order_time>='$date 00:00:00' and t1.first_order_time<='$date 23:59:59' group by t2.suid order by val0 desc";
		$salesman_res = $this->app->db->query($salesman_sql)->fetchAll();
		foreach($salesman_res as $val){
			$result[$val['suid']]['day_customer_count'] = $val['val0'];
		}
		$salesman_sql = "select t2.suid,count(*) as val0 from r_customer t1,r_customer_salesman t2 where $cs_where $salesman_where and ".
			"t1.first_order_time>='$month_first_day 00:00:00' and t1.first_order_time<='$date 23:59:59' group by t2.suid order by val0 desc";
		$salesman_res = $this->app->db->query($salesman_sql)->fetchAll();
		foreach($salesman_res as $val){
			$result[$val['suid']]['period_customer_count'] = $val['val0'];
		}

		if($sids){
			$sid_where = " and out_sid in (". $sids.")";
		}
		else{
			$sid_where = "";
		}

		$order_count_sql = "select count(*) as val1,sum(amount) as val2,sum(box_total) as val3 from b_order t2 where out_cid=$cid $sid_where and".
			" `type`=1 and `status`=2 and checktime>='$date 00:00:00' and checktime<='$date 23:59:59' $old_uid_where";
		$order_count_res = $this->app->db->query($order_count_sql)->fetchAll();
		$add_up['day_order_count'] = $order_count_res[0]['val1'];
		$add_up['day_order_amount'] = fen2yuan($order_count_res[0]['val2']);

		$order_count_sql = "select count(*) as val1,sum(amount) as val2,sum(box_total) as val3 from b_order t2 where out_cid=$cid $sid_where and".
			" `type`=1 and `status`=2 and checktime>='$month_first_day 00:00:00' and checktime<='$date 23:59:59' $old_uid_where";
		$order_count_res = $this->app->db->query($order_count_sql)->fetchAll();
		$add_up['period_order_count'] = $order_count_res[0]['val1'];
		$add_up['period_order_amount'] = fen2yuan($order_count_res[0]['val2']);
		$add_up['period_box_total'] = round($order_count_res[0]['val3'],2);

		$order_sql = "select suid,count(*) as val1,sum(amount) as val2,sum(box_total) as val3 from b_order t2 where out_cid=$cid $sid_where and".
			" `type`=1 and `status`=2 and checktime>='$date 00:00:00' and checktime<='$date 23:59:59' $salesman_where group by suid";
		$order_res = $this->app->db->query($order_sql)->fetchAll();
		foreach($order_res as $val){
			$result[$val['suid']]['day_order_count'] = $val['val1'];
			$result[$val['suid']]['day_order_amount'] = fen2yuan($val['val2']);
		}

		$order_sql = "select suid,count(*) as val1,sum(amount) as val2,sum(box_total) as val3 from b_order t2 where out_cid=$cid $sid_where and".
			" `type`=1 and `status`=2 and checktime>='$month_first_day 00:00:00' and checktime<='$date 23:59:59' $salesman_where group by suid";
		$order_res = $this->app->db->query($order_sql)->fetchAll();
		foreach($order_res as $val){
			$result[$val['suid']]['period_order_count'] = $val['val1'];
			$result[$val['suid']]['period_order_amount'] = fen2yuan($val['val2']);
			$result[$val['suid']]['period_box_total'] = round($val['val3'],2);
		}

		//箱数任务完成率
		$get_val = 'val'.ltrim($month,'0');
		$task_count_sql = "select sum($get_val) as val1 from b_task t2 where `year`=$year and `status`=1 and `type`=1 and cid=$cid $old_uid_where";
		$task_count_res = $this->app->db->query($task_count_sql)->fetchAll();
		$add_up['task_total'] = $task_count_res[0]['val1'];
		if($add_up['task_total']){
			$add_up['complete_rate'] = num2per($add_up['period_box_total']/$add_up['task_total']);
		}
		else{
			$add_up['complete_rate'] = '0%';
		}

		$task_sql = "select * from b_task t2 where `year`=$year and `status`=1 and `type`=1 and cid=$cid $salesman_where";
		$task_res = $this->app->db->query($task_sql)->fetchAll();
		foreach($task_res as $val){
			$result[$val['suid']]['task_total'] = $val[$get_val];
			if($result[$val['suid']]['task_total']){
				$result[$val['suid']]['complete_rate'] = num2per($result[$val['suid']]['period_box_total']/$result[$val['suid']]['task_total']);
			}
		}

		//金额任务完成率
		$get_val = 'val'.ltrim($month,'0');
		$task_count_sql = "select sum($get_val) as val1 from b_task t2 where `year`=$year and `status`=1 and `type`=2 and cid=$cid $old_uid_where";
		$task_count_res = $this->app->db->query($task_count_sql)->fetchAll();
		$add_up['amount_task_total'] = $task_count_res[0]['val1'];
		if($add_up['amount_task_total']){
			$add_up['amount_complete_rate'] = num2per($add_up['period_order_amount']/$add_up['amount_task_total']);
		}
		else{
			$add_up['amount_complete_rate'] = '0%';
		}

		$task_sql = "select * from b_task t2 where `year`=$year and `status`=1 and `type`=2 and cid=$cid $salesman_where";
		$task_res = $this->app->db->query($task_sql)->fetchAll();
		foreach($task_res as $val){
			$result[$val['suid']]['amount_task_total'] = $val[$get_val];
			if($result[$val['suid']]['amount_task_total']){
				$result[$val['suid']]['amount_complete_rate'] = num2per($result[$val['suid']]['period_order_amount']/$result[$val['suid']]['amount_task_total']);
			}
		}

		$result = dict2list($result);
		return [
			'count'=>$all_count,
			'page_count'=>$all_page,
			'data'=>$result,
			'add_up'=>$add_up,
			'month_first_day'=>$month_first_day
		];

	}


	public function form_read_pro($data){
		$ugid = get_value($data, 'ugid');
		$cid = get_value($data, 'cid');
		$gid = get_value($data, 'gid');
		$suid = get_value($data, 'suid');
		$year = get_value($data, 'year');
		$belong = get_value($data, 'belong');
		$type = get_value($data, 'type', 1);
		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 200);
		$start_count = ($page - 1) * $page_num;

		$db_where = " t1.id=t2.task_id and t1.cid=$cid and t1.year=$year and t1.status=1 and t1.type=$type";
		$db_where2 = " cid=$cid and `year`=$year and `status`=1 and `type`=$type";
		if($suid){
			$db_where .= " and t1.suid=$suid";
			$db_where2 .= " and suid=$suid";
		}
		if($gid){
			$db_where .= " and t2.gid=$gid";
		}

		if($belong){
			$u_model = new User();
			$u_res = $u_model->read_list_nopage(['cid'=>$cid,'belong'=>$belong]);
			$uid_list = [];
			foreach($u_res as $val){
				$uid_list[] = $val['id'];
			}
			if($uid_list){
				$db_where .= " and t1.suid in (".implode(',',$uid_list).")";
				$db_where2 .= " and suid in (".implode(',',$uid_list).")";
			}
			else{
				$db_where .= " and t1.suid is null";
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
				$db_where .= " and t1.suid in (".implode(',',$uid_list).")";
				$db_where2 .= " and suid in (".implode(',',$uid_list).")";
			}
			else{
				$db_where .= " and t1.suid is null";
			}
		}

		$add_up = $add_up2 = Null;

		$count_sql = "select count(*) as val0,sum(t2.val1) as val1,sum(t2.val2) as val2,sum(t2.val3) as val3,sum(t2.val4) as val4,sum(t2.val5) as val5,".
			"sum(t2.val6) as val6,sum(t2.val7) as val7,sum(t2.val8) as val8,sum(t2.val9) as val9,sum(t2.val10) as val10,sum(t2.val11) as val11,".
			"sum(t2.val12) as val12,sum(t2.val_all) as val_all from b_task t1,b_task_glist t2 where $db_where";
		$count_res = $this->app->db->query($count_sql)->fetchAll();
		$all_count = $count_res[0]['val0'];
		$temp_data = $count_res[0];

		if($gid && !$suid){
			$add_up = [
				'val1'=>$temp_data['val1'],
				'val2'=>$temp_data['val2'],
				'val3'=>$temp_data['val3'],
				'val4'=>$temp_data['val4'],
				'val5'=>$temp_data['val5'],
				'val6'=>$temp_data['val6'],
				'val7'=>$temp_data['val7'],
				'val8'=>$temp_data['val8'],
				'val9'=>$temp_data['val9'],
				'val10'=>$temp_data['val10'],
				'val11'=>$temp_data['val11'],
				'val12'=>$temp_data['val12'],
				'val_all'=>$temp_data['val_all']
			];
		}

		if(!$gid){
			$count_sql = "select sum(val1) as val1,sum(val2) as val2,sum(val3) as val3,sum(val4) as val4,sum(val5) as val5,sum(val6) as val6,sum(val7) as val7,".
				"sum(val8) as val8,sum(val9) as val9,sum(val10) as val10,sum(val11) as val11,sum(val12) as val12,sum(val_all) as val_all from b_task where $db_where2";
			$count_res = $this->app->db->query($count_sql)->fetchAll();
			$temp_data = $count_res[0];
			$add_up2 = [
				'val1'=>$temp_data['val1'],
				'val2'=>$temp_data['val2'],
				'val3'=>$temp_data['val3'],
				'val4'=>$temp_data['val4'],
				'val5'=>$temp_data['val5'],
				'val6'=>$temp_data['val6'],
				'val7'=>$temp_data['val7'],
				'val8'=>$temp_data['val8'],
				'val9'=>$temp_data['val9'],
				'val10'=>$temp_data['val10'],
				'val11'=>$temp_data['val11'],
				'val12'=>$temp_data['val12'],
				'val_all'=>$temp_data['val_all']
			];
		}

		$all_page = intval($all_count/$page_num);
		if($all_count%$page_num!=0){
			$all_page ++;
		}

		$sql = "select t1.suid,t1.suname,t2.gid,t2.gname,t2.val1,t2.val2,t2.val3,t2.val4,t2.val5,t2.val6,".
			"t2.val7,t2.val8,t2.val9,t2.val10,t2.val11,t2.val12,t2.val_all from b_task t1,b_task_glist t2 where $db_where order by t1.suid asc limit $start_count,$page_num";
		$res = $this->app->db->query($sql)->fetchAll();
		$result = [];
		$last_val = Null;
		foreach($res as $val){
//			if($last_val && $last_val['suid'] != $val['suid']){
//				$my_suid = $last_val['suid'];
//				//增加一条总计
//				$sql2 = "select * from b_task where suid=$my_suid and year=$year and `status`=1";
//				$res2 = $this->app->db->query($sql2)->fetchAll();
//				$temp_data = $res2[0];
//				$result[] = [
//					'suid'=>$my_suid,
//					'suname'=>$last_val['suname'],
//					'gid'=>'-1',
//					'gname'=>'月度总计',
//					'val1'=>$temp_data['val1'],
//					'val2'=>$temp_data['val2'],
//					'val3'=>$temp_data['val3'],
//					'val4'=>$temp_data['val4'],
//					'val5'=>$temp_data['val5'],
//					'val6'=>$temp_data['val6'],
//					'val7'=>$temp_data['val7'],
//					'val8'=>$temp_data['val8'],
//					'val9'=>$temp_data['val9'],
//					'val10'=>$temp_data['val10'],
//					'val11'=>$temp_data['val11'],
//					'val12'=>$temp_data['val12'],
//					'val_all'=>$temp_data['val_all']
//				];
//			}
			$temp_data = $val;
			$result[] = [
				'suid'=>$temp_data['suid'],
				'suname'=>$temp_data['suname'],
				'gid'=>$temp_data['gid'],
				'gname'=>$temp_data['gname'],
				'val1'=>$temp_data['val1'],
				'val2'=>$temp_data['val2'],
				'val3'=>$temp_data['val3'],
				'val4'=>$temp_data['val4'],
				'val5'=>$temp_data['val5'],
				'val6'=>$temp_data['val6'],
				'val7'=>$temp_data['val7'],
				'val8'=>$temp_data['val8'],
				'val9'=>$temp_data['val9'],
				'val10'=>$temp_data['val10'],
				'val11'=>$temp_data['val11'],
				'val12'=>$temp_data['val12'],
				'val_all'=>$temp_data['val_all']
			];
			$last_val = $val;
		}

//		$my_suid = $last_val['suid'];
//		//增加一条总计
//		$sql2 = "select * from b_task where suid=$my_suid and year=$year and `status`=1";
//		$res2 = $this->app->db->query($sql2)->fetchAll();
//		$temp_data = $res2[0];
//		$result[] = [
//			'suid'=>$my_suid,
//			'suname'=>$last_val['suname'],
//			'gid'=>'-1',
//			'gname'=>'月度总计',
//			'val1'=>$temp_data['val1'],
//			'val2'=>$temp_data['val2'],
//			'val3'=>$temp_data['val3'],
//			'val4'=>$temp_data['val4'],
//			'val5'=>$temp_data['val5'],
//			'val6'=>$temp_data['val6'],
//			'val7'=>$temp_data['val7'],
//			'val8'=>$temp_data['val8'],
//			'val9'=>$temp_data['val9'],
//			'val10'=>$temp_data['val10'],
//			'val11'=>$temp_data['val11'],
//			'val12'=>$temp_data['val12'],
//			'val_all'=>$temp_data['val_all']
//		];

		return [
			'count'=>$all_count,
			'page_count'=>$all_page,
			'data'=>$result,
			'add_up'=>$add_up,
			'add_up2'=>$add_up2
		];

	}
	
}

