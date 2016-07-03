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
class FReserve extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['cid','sid','date','amount_begin','amount_end','total_begin','total_end'];

	//需要分和元转换的金额字段
	protected $amount_data = ['amount_begin','amount_end'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('f_reserve', $id);
	}

	public function my_read($data){
		$my_param = [
			'date'=>$data['date'],
			'sid'=>$data['sid']
		];
		$res = $this->read_one($my_param);
		$frd_model = new FReserveDetail();

		$param = [
			'sid'=>$data['sid'],
			'date'=>$data['date']
		];

		if(get_value($data, 'gtids')){
			$cgt_model = new CompanyGoodsType();
			$cid = $this->app->Sneaker->cid;
			$param['gtid'] = $cgt_model->get_ids_by_fids($data['gtids'], $cid);
		}

		//关键字
		if(get_value($data, 'search')){
			$param['search'] = $data['search'];
		}
		//分页
		if(get_value($data, 'page')){
			$param['page'] = $data['page'];
		}
		if(get_value($data, 'page_num')){
			$param['page_num'] = $data['page_num'];
		}

		$add_up = $frd_model->sum(['total_begin','total_end','amount_begin','amount_end'], $param);
		$res['total_begin'] = $add_up['total_begin'];
		$res['total_end'] = $add_up['total_end'];
		$res['amount_begin'] = $add_up['amount_begin'];
		$res['amount_end'] = $add_up['amount_end'];

		$frd_res = $frd_model->read_list($param);
		$res['goods_list'] = $frd_res['data'];
		$res['count'] = $frd_res['count'];
		$res['page_count'] = $frd_res['page_count'];

		if(get_value($data, 'download') == 'excel'){
			$excel_data = [];
			$excel_data[] = ['商品编码','商品名称','商品条码','单位','规格','分类','期初数量','期初箱数','期末数量','期末箱数','期初金额','期末金额'];
			foreach($res['goods_list'] as $val){
				$excel_data[] = [$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['gtname'],
					$val['total_begin'],round($val['total_begin']/$val['gspec'],2),$val['total_end'],round($val['total_end']/$val['gspec'],2),
					$val['amount_begin'],$val['amount_end']];
			}
			$excel_data[] = ['总计','','','','','',$res['total_begin'],'',$res['total_end'],'',$res['amount_begin'],$res['amount_end']];
			write_excel($excel_data, '库存日报表('.date('Y-m-d').')');
		}

		return $res;
	}

	//查看进销存日报
	public function my_erp($data){
		$frd_model = new FReserveDetail();
		$begin_date = get_value($data, 'begin_date');
		$end_date = get_value($data, 'end_date');
		$sid = get_value($data, 'sid');

		$param = [
			'sid'=>$sid,
		];

		if(get_value($data, 'gtids')){
			$cgt_model = new CompanyGoodsType();
			$cid = $this->app->Sneaker->cid;
			$param['gtid'] = $cgt_model->get_ids_by_fids($data['gtids'], $cid);
		}

		//关键字
		if(get_value($data, 'search')){
			$param['search'] = $data['search'];
		}

		//分页
		if(get_value($data, 'page')){
			$param['page'] = $data['page'];
		}
		if(get_value($data, 'page_num')){
			$param['page_num'] = $data['page_num'];
		}

		$param['date'] = $end_date;
		$frd_add_up_end = $frd_model->sum(['amount_end'], $param);
		$frd_res_end = $frd_model->read_list($param);
		$my_data_end = $frd_res_end['data'];

		$param['date'] = $begin_date;
		$frd_add_up_begin = $frd_model->sum(['amount_begin'], $param);

		$gid_list = [];
		foreach($my_data_end as $val){
			$gid_list[] = $val['gid'];
		}
		if($gid_list){
			$gid_where = " gid in (".implode(',', $gid_list).")";
			$param['gid'] = $gid_list;
		}
		else{
			$gid_where = " gid is null";
			$param['gid'] = 'null';
		}

		unset($param['page']);
		unset($param['page_num']);
		$frd_res_begin = $frd_model->read_list($param);
		$my_data_begin = $frd_res_begin['data'];
		$my_data_begin2 = [];
		foreach($my_data_begin as $val){
			$my_data_begin2[$val['gid']] = $val;
		}



		$fsi_model = new FStockIn();
		$fso_model = new FStockOut();
		$fa_model = new FAdjust();

		unset($param['date']);
		$param['date[>=]'] = $begin_date;
		$param['date[<=]'] = $end_date;
		$fsi_add_up = $fsi_model->sum(['buy_amount','return_amount','transfer_amount'], $param);
		$fso_add_up = $fso_model->sum(['sell_amount','return_amount','transfer_amount'], $param);
		$fa_add_up = $fa_model->sum(['overloss_amount','inventory_amount','return_amount','flush_amount','transfer_amount'], $param);



		$db_where = " sid=$sid and `date`>='$begin_date' and `date`<='$end_date' and ".$gid_where;
		$sql = "select gid,sum(buy_total) as buy_total,sum(return_total) as return_total,sum(transfer_total) as transfer_total,".
			"sum(buy_amount) as buy_amount,sum(return_amount) as return_amount,sum(transfer_amount) as transfer_amount from `f_stock_in`".
			" where ".$db_where." group by gid";
		$fsi_res = $this->app->db->query($sql)->fetchAll();
		$fsi_res2 = [];
		foreach($fsi_res as $val){
			$fsi_res2[$val['gid']] = $val;
		}

		$sql = "select gid,sum(sell_total) as sell_total,sum(return_total) as return_total,sum(transfer_total) as transfer_total,".
			"sum(sell_amount) as sell_amount,sum(return_amount) as return_amount,sum(transfer_amount) as transfer_amount from `f_stock_out`".
			" where ".$db_where." group by gid";
		$fso_res = $this->app->db->query($sql)->fetchAll();
		$fso_res2 = [];
		foreach($fso_res as $val){
			$fso_res2[$val['gid']] = $val;
		}

		$sql = "select gid,sum(overloss_total) as overloss_total,sum(inventory_total) as inventory_total,sum(overloss_amount) as overloss_amount,".
			"sum(inventory_amount) as inventory_amount,sum(return_amount) as return_amount,sum(flush_amount) as flush_amount,sum(transfer_amount) as transfer_amount from `f_adjust`".
			" where ".$db_where." group by gid";
		$fa_res = $this->app->db->query($sql)->fetchAll();
		$fa_res2 = [];
		foreach($fa_res as $val){
			$fa_res2[$val['gid']] = $val;
		}

		foreach($my_data_end as $key=>$val){
			$gid = $val['gid'];
			$fsi_temp = get_value($fsi_res2, $gid, []);
			$fso_temp = get_value($fso_res2, $gid, []);
			$fa_temp = get_value($fa_res2, $gid, []);
			$data_begin_temp = get_value($my_data_begin2, $gid, []);

			$my_data_end[$key]['amount_begin'] = get_value($data_begin_temp, 'amount_begin', 0);
			$my_data_end[$key]['total_begin'] = get_value($data_begin_temp, 'total_begin', 0);
			$my_data_end[$key]['buy_total'] = get_value($fsi_temp, 'buy_total', 0);
			$my_data_end[$key]['buy_return_total'] = get_value($fsi_temp, 'return_total', 0);
			$my_data_end[$key]['sell_total'] = get_value($fso_temp, 'sell_total', 0);
			$my_data_end[$key]['sell_return_total'] = get_value($fso_temp, 'return_total', 0);
			$my_data_end[$key]['transfer_in_total'] = get_value($fsi_temp, 'transfer_total', 0);
			$my_data_end[$key]['transfer_out_total'] = get_value($fso_temp, 'transfer_total', 0);
			$my_data_end[$key]['overloss_total'] = get_value($fa_temp, 'overloss_total', 0);
			$my_data_end[$key]['inventory_total'] = get_value($fa_temp, 'inventory_total', 0);
			$my_data_end[$key]['buy_amount'] = fen2yuan(get_value($fsi_temp, 'buy_amount', 0));
			$my_data_end[$key]['buy_return_amount'] = fen2yuan(get_value($fsi_temp, 'return_amount', 0));
			$my_data_end[$key]['sell_amount'] = fen2yuan(get_value($fso_temp, 'sell_amount', 0));
			$my_data_end[$key]['sell_return_amount'] = fen2yuan(get_value($fso_temp, 'return_amount', 0));
			$my_data_end[$key]['transfer_in_amount'] = fen2yuan(get_value($fsi_temp, 'transfer_amount', 0));
			$my_data_end[$key]['transfer_out_amount'] = fen2yuan(get_value($fso_temp, 'transfer_amount', 0));
			$my_data_end[$key]['overloss_amount'] = fen2yuan(get_value($fa_temp, 'overloss_amount', 0));
			$my_data_end[$key]['inventory_amount'] = fen2yuan(get_value($fa_temp, 'inventory_amount', 0));
			$my_data_end[$key]['adjust_amount'] = fen2yuan(get_value($fa_temp, 'return_amount', 0)+
				get_value($fa_temp, 'flush_amount', 0)+get_value($fa_temp, 'transfer_amount', 0));
		}
		$res['data'] = $my_data_end;
		$res['count'] = $frd_res_end['count'];
		$res['page_count'] = $frd_res_end['page_count'];
		$res['add_up'] = [
			'amount_begin' => get_value($frd_add_up_begin, 'amount_begin'),
			'amount_end' => get_value($frd_add_up_end, 'amount_end'),
			'buy_amount' => get_value($fsi_add_up, 'buy_amount'),
			'buy_return_amount' => get_value($fsi_add_up, 'return_amount'),
			'transfer_in_amount' => get_value($fsi_add_up, 'transfer_amount'),
			'sell_amount' => get_value($fso_add_up, 'sell_amount'),
			'sell_return_amount' => get_value($fso_add_up, 'return_amount'),
			'transfer_out_amount' => get_value($fso_add_up, 'transfer_amount'),
			'overloss_amount' => get_value($fa_add_up, 'overloss_amount'),
			'inventory_amount' => get_value($fa_add_up, 'inventory_amount'),
			'adjust_amount' => get_value($fa_add_up, 'return_amount')+
				get_value($fa_add_up, 'flush_amount')+get_value($fa_add_up, 'transfer_amount'),
		];

		if(get_value($data, 'download') == 'excel'){
			$excel_data = [];
			$excel_data[] = ['商品编码','商品名称','商品条码','单位','规格','分类','调整金额','期初数量','期初箱数','期初金额','期末数量',
				'期末箱数','期末金额','采购数量','采购箱数','采购金额','采购退货数量','采购退货箱数','采购退货金额','销售数量','销售箱数',
				'销售金额','销售退货数量','销售退货箱数','销售退货金额','调入数量','调入箱数','调入金额','调出数量','调出箱数','调出金额',
				'损益数量','损益箱数','损益金额','盘点盈亏数量','盘点盈亏箱数','盘点盈亏金额'];

			foreach($res['data'] as $val){
				$excel_data[] = [$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['gtname'],
					$val['adjust_amount'],$val['total_begin'],round($val['total_begin']/$val['gspec'],2),$val['amount_begin'],
					$val['total_end'],round($val['total_end']/$val['gspec'],2),$val['amount_end'],$val['buy_total'],
					round($val['buy_total']/$val['gspec'],2),$val['buy_amount'],$val['buy_return_total'],round($val['buy_return_total']/$val['gspec'],2),
					$val['buy_return_amount'],$val['sell_total'],round($val['sell_total']/$val['gspec'],2),$val['sell_amount'],
					$val['sell_return_total'],round($val['sell_return_total']/$val['gspec'],2),$val['sell_return_amount'],
					$val['transfer_in_total'],round($val['transfer_in_total']/$val['gspec'],2),$val['transfer_in_amount'],
					$val['transfer_out_total'],round($val['transfer_out_total']/$val['gspec'],2),$val['transfer_out_amount'],
					$val['overloss_total'],round($val['overloss_total']/$val['gspec'],2),$val['overloss_amount'],
					$val['inventory_total'],round($val['inventory_total']/$val['gspec'],2),$val['inventory_amount']
				];
			}
			$excel_data[] = ['总计','','','','','',$res['add_up']['adjust_amount'],'','',$res['add_up']['amount_begin'],
				'','',$res['add_up']['amount_end'],'','',$res['add_up']['buy_amount'],'','',$res['add_up']['buy_return_amount'],
				'','',$res['add_up']['sell_amount'],'','',$res['add_up']['sell_return_amount'],'','',$res['add_up']['transfer_in_amount'],
				'','',$res['add_up']['transfer_out_amount'],'','',$res['add_up']['overloss_amount'],'','',$res['add_up']['inventory_amount']
			];
			write_excel($excel_data, '进销存日报表('.date('Y-m-d').')');
		}

		return $res;
	}

	//查看进销存日报
	public function my_erp_detail($data){
		$frd_model = new FReserveDetail();
		$begin_date = get_value($data, 'begin_date');
		$end_date = get_value($data, 'end_date');
		$sid = get_value($data, 'sid');
		$gid = get_value($data, 'gid');

		$param = [
			'sid'=>$sid,
		];

		//分页
		if(get_value($data, 'page')){
			$param['page'] = $data['page'];
		}
		if(get_value($data, 'page_num')){
			$param['page_num'] = $data['page_num'];
		}
		$param['gid'] = $gid;
		//$param['date'] = $end_date;
		$param['date[>=]'] = $begin_date;
		$param['date[<=]'] = $end_date;
		$param['orderby'] = 'date^desc';
		//$frd_add_up_end = $frd_model->sum(['amount_end'], $param);
		$frd_res_end = $frd_model->read_list($param);
		$my_data_end = $frd_res_end['data'];

		//$param['date'] = $begin_date;
		//$frd_add_up_begin = $frd_model->sum(['amount_begin'], $param);

		unset($param['page']);
		unset($param['page_num']);
		$frd_res_begin = $frd_model->read_list($param);
		$my_data_begin = $frd_res_begin['data'];
		$my_data_begin2 = [];
		foreach($my_data_begin as $val){
			$my_data_begin2[$val['date']] = $val;
		}

		$gid_where = " gid=$gid";

		$fsi_model = new FStockIn();
		$fso_model = new FStockOut();
		$fa_model = new FAdjust();

		//unset($param['date']);
		//$param['date[>=]'] = $begin_date;
		//$param['date[<=]'] = $end_date;
		$fsi_add_up = $fsi_model->sum(['buy_amount','return_amount','transfer_amount'], $param);
		$fso_add_up = $fso_model->sum(['sell_amount','return_amount','transfer_amount'], $param);
		$fa_add_up = $fa_model->sum(['overloss_amount','inventory_amount','return_amount','flush_amount','transfer_amount'], $param);

		$db_where = " sid=$sid and `date`>='$begin_date' and `date`<='$end_date' and ".$gid_where;
		$sql = "select `date`,sum(buy_total) as buy_total,sum(return_total) as return_total,sum(transfer_total) as transfer_total,".
			"sum(buy_amount) as buy_amount,sum(return_amount) as return_amount,sum(transfer_amount) as transfer_amount from `f_stock_in`".
			" where ".$db_where." group by `date`";
		$fsi_res = $this->app->db->query($sql)->fetchAll();
		$fsi_res2 = [];
		foreach($fsi_res as $val){
			$fsi_res2[$val['date']] = $val;
		}

		$sql = "select `date`,sum(sell_total) as sell_total,sum(return_total) as return_total,sum(transfer_total) as transfer_total,".
			"sum(sell_amount) as sell_amount,sum(return_amount) as return_amount,sum(transfer_amount) as transfer_amount from `f_stock_out`".
			" where ".$db_where." group by `date`";
		$fso_res = $this->app->db->query($sql)->fetchAll();
		$fso_res2 = [];
		foreach($fso_res as $val){
			$fso_res2[$val['date']] = $val;
		}

		$sql = "select `date`,sum(overloss_total) as overloss_total,sum(inventory_total) as inventory_total,sum(overloss_amount) as overloss_amount,".
			"sum(inventory_amount) as inventory_amount,sum(return_amount) as return_amount,sum(flush_amount) as flush_amount,sum(transfer_amount) as transfer_amount from `f_adjust`".
			" where ".$db_where." group by `date`";
		$fa_res = $this->app->db->query($sql)->fetchAll();
		$fa_res2 = [];
		foreach($fa_res as $val){
			$fa_res2[$val['date']] = $val;
		}

		foreach($my_data_end as $key=>$val){
			$date = $val['date'];
			$fsi_temp = get_value($fsi_res2, $date, []);
			$fso_temp = get_value($fso_res2, $date, []);
			$fa_temp = get_value($fa_res2, $date, []);
			$data_begin_temp = get_value($my_data_begin2, $date, []);

			$my_data_end[$key]['amount_begin'] = get_value($data_begin_temp, 'amount_begin', 0);
			$my_data_end[$key]['total_begin'] = get_value($data_begin_temp, 'total_begin', 0);
			$my_data_end[$key]['buy_total'] = get_value($fsi_temp, 'buy_total', 0);
			$my_data_end[$key]['buy_return_total'] = get_value($fsi_temp, 'return_total', 0);
			$my_data_end[$key]['sell_total'] = get_value($fso_temp, 'sell_total', 0);
			$my_data_end[$key]['sell_return_total'] = get_value($fso_temp, 'return_total', 0);
			$my_data_end[$key]['transfer_in_total'] = get_value($fsi_temp, 'transfer_total', 0);
			$my_data_end[$key]['transfer_out_total'] = get_value($fso_temp, 'transfer_total', 0);
			$my_data_end[$key]['overloss_total'] = get_value($fa_temp, 'overloss_total', 0);
			$my_data_end[$key]['inventory_total'] = get_value($fa_temp, 'inventory_total', 0);
			$my_data_end[$key]['buy_amount'] = fen2yuan(get_value($fsi_temp, 'buy_amount', 0));
			$my_data_end[$key]['buy_return_amount'] = fen2yuan(get_value($fsi_temp, 'return_amount', 0));
			$my_data_end[$key]['sell_amount'] = fen2yuan(get_value($fso_temp, 'sell_amount', 0));
			$my_data_end[$key]['sell_return_amount'] = fen2yuan(get_value($fso_temp, 'return_amount', 0));
			$my_data_end[$key]['transfer_in_amount'] = fen2yuan(get_value($fsi_temp, 'transfer_amount', 0));
			$my_data_end[$key]['transfer_out_amount'] = fen2yuan(get_value($fso_temp, 'transfer_amount', 0));
			$my_data_end[$key]['overloss_amount'] = fen2yuan(get_value($fa_temp, 'overloss_amount', 0));
			$my_data_end[$key]['inventory_amount'] = fen2yuan(get_value($fa_temp, 'inventory_amount', 0));
			$my_data_end[$key]['adjust_amount'] = fen2yuan(get_value($fa_temp, 'return_amount', 0)+
				get_value($fa_temp, 'flush_amount', 0)+get_value($fa_temp, 'transfer_amount', 0));
		}
		$res['data'] = $my_data_end;
		$res['count'] = $frd_res_end['count'];
		$res['page_count'] = $frd_res_end['page_count'];
		$res['add_up'] = [
			//'amount_begin' => get_value($frd_add_up_begin, 'amount_begin'),
			//'amount_end' => get_value($frd_add_up_end, 'amount_end'),
			'buy_amount' => get_value($fsi_add_up, 'buy_amount'),
			'buy_return_amount' => get_value($fsi_add_up, 'return_amount'),
			'transfer_in_amount' => get_value($fsi_add_up, 'transfer_amount'),
			'sell_amount' => get_value($fso_add_up, 'sell_amount'),
			'sell_return_amount' => get_value($fso_add_up, 'return_amount'),
			'transfer_out_amount' => get_value($fso_add_up, 'transfer_amount'),
			'overloss_amount' => get_value($fa_add_up, 'overloss_amount'),
			'inventory_amount' => get_value($fa_add_up, 'inventory_amount'),
			'adjust_amount' => get_value($fa_add_up, 'return_amount')+
				get_value($fa_add_up, 'flush_amount')+get_value($fa_add_up, 'transfer_amount'),
		];

		if(get_value($data, 'download') == 'excel'){
			$excel_data = [];
			$excel_data[] = ['日期','商品编码','商品名称','商品条码','单位','规格','分类','调整金额','期初数量','期初箱数','期初金额','期末数量',
				'期末箱数','期末金额','采购数量','采购箱数','采购金额','采购退货数量','采购退货箱数','采购退货金额','销售数量','销售箱数',
				'销售金额','销售退货数量','销售退货箱数','销售退货金额','调入数量','调入箱数','调入金额','调出数量','调出箱数','调出金额',
				'损益数量','损益箱数','损益金额','盘点盈亏数量','盘点盈亏箱数','盘点盈亏金额'];

			foreach($res['data'] as $val){
				$excel_data[] = [$val['date'],$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['gtname'],
					$val['adjust_amount'],$val['total_begin'],round($val['total_begin']/$val['gspec'],2),$val['amount_begin'],
					$val['total_end'],round($val['total_end']/$val['gspec'],2),$val['amount_end'],$val['buy_total'],
					round($val['buy_total']/$val['gspec'],2),$val['buy_amount'],$val['buy_return_total'],round($val['buy_return_total']/$val['gspec'],2),
					$val['buy_return_amount'],$val['sell_total'],round($val['sell_total']/$val['gspec'],2),$val['sell_amount'],
					$val['sell_return_total'],round($val['sell_return_total']/$val['gspec'],2),$val['sell_return_amount'],
					$val['transfer_in_total'],round($val['transfer_in_total']/$val['gspec'],2),$val['transfer_in_amount'],
					$val['transfer_out_total'],round($val['transfer_out_total']/$val['gspec'],2),$val['transfer_out_amount'],
					$val['overloss_total'],round($val['overloss_total']/$val['gspec'],2),$val['overloss_amount'],
					$val['inventory_total'],round($val['inventory_total']/$val['gspec'],2),$val['inventory_amount']
				];
			}
			$excel_data[] = ['总计','','','','','','',$res['add_up']['adjust_amount'],'','','',
				'','','','','',$res['add_up']['buy_amount'],'','',$res['add_up']['buy_return_amount'],
				'','',$res['add_up']['sell_amount'],'','',$res['add_up']['sell_return_amount'],'','',$res['add_up']['transfer_in_amount'],
				'','',$res['add_up']['transfer_out_amount'],'','',$res['add_up']['overloss_amount'],'','',$res['add_up']['inventory_amount']
			];
			write_excel($excel_data, '进销存日报表-单品明细('.date('Y-m-d').')');
		}

		return $res;
	}


	public function my_book($data){
		$sid = get_value($data, 'sid');
		$gid = get_value($data, 'gid');
		$begindate = get_value($data, 'begin_date');
		$enddate = get_value($data, 'end_date');
		$cid = get_value($data, 'cid');

		//big_type:1-订单 2-入库单 3-出库单 4-调价单 5-临时调价单

		$result = [];

		//订单
		$sql = "select t1.id as val0,t1.type as val1,t1.cuname as val2,t1.uname as val3,t1.checktime as val4,t2.amount_price as val5,".
			"t2.total as val6 from b_order t1,b_order_glist t2 where t1.id=t2.order_id and t2.gid=$gid and ".
			"t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59' and t1.in_cid=$cid and t1.status=2";
		if($sid){
			$sql .= " and t1.in_sid=$sid";
		}
		$so_res = $this->app->db->query($sql)->fetchAll();
		$type_list =[
			'1'=>'采购订单',
			'2'=>'退货订单',
			'3'=>'调拨订单'
		];
		foreach($so_res as $val){
			$result[] = [
				'big_type'=>1,
				'small_type'=>$val['val1'],
				'small_type_name'=>$type_list[$val['val1']],
				'id'=>$val['val0'],
				'cuname'=>$val['val2'],
				'uname'=>$val['val3'],
				'checktime'=>$val['val4'],
				'amount'=>fen2yuan($val['val5']),
				'total'=>$val['val6']
			];
		}

		//入库单
		$sql = "select t1.id as val0,t1.type as val1,t1.cuname as val2,t1.uname as val3,t1.checktime as val4,t2.amount_price as val5,".
			"t2.total as val6 from b_stock_in t1,b_stock_in_glist t2 where t1.id=t2.stock_in_id and t2.gid=$gid and ".
			"t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59' and t1.cid=$cid and t1.status=2";
		if($sid){
			$sql .= " and t1.sid=$sid";
		}
		$so_res = $this->app->db->query($sql)->fetchAll();
		$type_list =[
			'1'=>'采购入库',
			'2'=>'退货入库',
			'3'=>'调拨入库',
			'4'=>'报溢入库',
			'5'=>'盘盈入库'
		];
		foreach($so_res as $val){
			$result[] = [
				'big_type'=>2,
				'small_type'=>$val['val1'],
				'small_type_name'=>$type_list[$val['val1']],
				'id'=>$val['val0'],
				'cuname'=>$val['val2'],
				'uname'=>$val['val3'],
				'checktime'=>$val['val4'],
				'amount'=>fen2yuan($val['val5']),
				'total'=>$val['val6']
			];
		}

		//出库单
		$sql = "select t1.id as val0,t1.type as val1,t1.cuname as val2,t1.uname as val3,t1.checktime as val4,t2.amount_price as val5,".
			"t2.total as val6 from b_stock_out t1,b_stock_out_glist t2 where t1.id=t2.stock_out_id and t2.gid=$gid and ".
			"t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59' and t1.cid=$cid and t1.status=4";
		if($sid){
			$sql .= " and t1.sid=$sid";
		}
		$so_res = $this->app->db->query($sql)->fetchAll();
		$type_list =[
			'1'=>'销售出库',
			'2'=>'退货出库',
			'3'=>'调拨出库',
			'4'=>'报损出库',
			'5'=>'盘亏出库'
		];
		foreach($so_res as $val){
			$result[] = [
				'big_type'=>3,
				'small_type'=>$val['val1'],
				'small_type_name'=>$type_list[$val['val1']],
				'id'=>$val['val0'],
				'cuname'=>$val['val2'],
				'uname'=>$val['val3'],
				'checktime'=>$val['val4'],
				'amount'=>fen2yuan($val['val5']),
				'total'=>$val['val6']
			];
		}

		//调价单
		$sql = "select t1.id as val0,t1.type as val1,t1.cuname as val2,t1.uname as val3,t1.checktime as val4".
			" from b_price t1,b_price_glist t2 where t1.id=t2.price_id and t2.gid=$gid and ".
			"t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59' and t1.cid=$cid and t1.status=2";
		if($sid){
			$sql .= " and t1.sids like '%,$sid,%'";
		}
		$so_res = $this->app->db->query($sql)->fetchAll();
		$type_list =[
			'1'=>'进货调价',
			'2'=>'出货调价'
		];
		foreach($so_res as $val){
			$result[] = [
				'big_type'=>4,
				'small_type'=>$val['val1'],
				'small_type_name'=>$type_list[$val['val1']],
				'id'=>$val['val0'],
				'cuname'=>$val['val2'],
				'uname'=>$val['val3'],
				'checktime'=>$val['val4'],
				'amount'=>'-',
				'total'=>'-'
			];
		}

		//临时调价单
		$sql = "select t1.id as val0,t1.type as val1,t1.cuname as val2,t1.uname as val3,t1.checktime as val4".
			" from b_price_temp t1,b_price_temp_glist t2 where t1.id=t2.price_id and t2.gid=$gid and ".
			"t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59' and t1.cid=$cid and t1.status=2";
		if($sid){
			$sql .= " and t1.sids like '%,$sid,%'";
		}
		$so_res = $this->app->db->query($sql)->fetchAll();
		$type_list =[
			'1'=>'进货调价',
			'2'=>'出货调价'
		];
		foreach($so_res as $val){
			$result[] = [
				'big_type'=>5,
				'small_type'=>$val['val1'],
				'small_type_name'=>$type_list[$val['val1']],
				'id'=>$val['val0'],
				'cuname'=>$val['val2'],
				'uname'=>$val['val3'],
				'checktime'=>$val['val4'],
				'amount'=>'-',
				'total'=>'-'
			];
		}
		usort($result,"my_sort");

		return $result;

	}

//	//查看台账
//	public function my_book($data){
//		$frd_model = new FReserveDetail();
//
//		$param = [
//			'sid'=>$data['sid'],
//			'gid'=>$data['gid']
//		];
//
//		//起止日期
//		if(get_value($data, 'begindate')){
//			$param['date[>=]'] = $data['begindate'];
//		}
//		if(get_value($data, 'enddate')){
//			$param['date[<=]'] = $data['enddate'];
//		}
//
//		//分页
//		if(get_value($data, 'page')){
//			$param['page'] = $data['page'];
//		}
//		if(get_value($data, 'page_num')){
//			$param['page_num'] = $data['page_num'];
//		}
//
//		$frd_res = $frd_model->read_list($param);
//		$my_data = $frd_res['data'];
//
//		$fsi_model = new FStockIn();
//		$fso_model = new FStockOut();
//		$fa_model = new FAdjust();
//
//		$fsi_res2 = [];
//		$fsi_res = $fsi_model->read_list($param);
//		foreach($fsi_res['data'] as $val){
//			$fsi_res2[$val['date']] = $val;
//		}
//		$fso_res2 = [];
//		$fso_res = $fso_model->read_list($param);
//		foreach($fso_res['data'] as $val){
//			$fso_res2[$val['date']] = $val;
//		}
//		$fa_res2 = [];
//		$fa_res = $fa_model->read_list($param);
//		foreach($fa_res['data'] as $val){
//			$fa_res2[$val['date']] = $val;
//		}
//
//		foreach($my_data as $key=>$val){
//			$date = $val['date'];
//			$fsi_temp = get_value($fsi_res2, $date, []);
//			$fso_temp = get_value($fso_res2, $date, []);
//			$fa_temp = get_value($fa_res2, $date, []);
//
//			$my_data[$key]['buy_total'] = get_value($fsi_temp, 'buy_total', 0);
//			$my_data[$key]['buy_return_total'] = get_value($fsi_temp, 'return_total', 0);
//			$my_data[$key]['sell_total'] = get_value($fso_temp, 'sell_total', 0);
//			$my_data[$key]['sell_return_total'] = get_value($fso_temp, 'return_total', 0);
//			$my_data[$key]['transfer_in_total'] = get_value($fsi_temp, 'transfer_total', 0);
//			$my_data[$key]['transfer_out_total'] = get_value($fso_temp, 'transfer_total', 0);
//			$my_data[$key]['overloss_total'] = get_value($fa_temp, 'overloss_total', 0);
//			$my_data[$key]['inventory_total'] = get_value($fa_temp, 'inventory_total', 0);
//			$my_data[$key]['buy_amount'] = get_value($fsi_temp, 'buy_amount', 0);
//			$my_data[$key]['buy_return_amount'] = get_value($fsi_temp, 'return_amount', 0);
//			$my_data[$key]['sell_amount'] = get_value($fso_temp, 'sell_amount', 0);
//			$my_data[$key]['sell_return_amount'] = get_value($fso_temp, 'return_amount', 0);
//			$my_data[$key]['transfer_in_amount'] = get_value($fsi_temp, 'transfer_amount', 0);
//			$my_data[$key]['transfer_out_amount'] = get_value($fso_temp, 'transfer_amount', 0);
//			$my_data[$key]['overloss_amount'] = get_value($fa_temp, 'overloss_amount', 0);
//			$my_data[$key]['inventory_amount'] = get_value($fa_temp, 'inventory_amount', 0);
//			$my_data[$key]['adjust_amount'] = get_value($fa_temp, 'return_amount', 0)+
//				get_value($fa_temp, 'flush_amount', 0)+get_value($fa_temp, 'transfer_amount', 0);
//		}
//		$res['data'] = $my_data;
//		$res['count'] = $frd_res['count'];
//		$res['page_count'] = $frd_res['page_count'];
//		return $res;
//	}

}

function my_sort($a, $b){
	$ac = get_value($a, 'checktime');
	$bc = get_value($b, 'checktime');
	if($ac == $bc) return 0;
	return ($ac < $bc)?-1:1;
}
