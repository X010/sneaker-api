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

class TempPrice extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['*cid','sid','gid','begintime','endtime','in_price','out_price1','out_prcie2','out_price3','out_price4'];

	//需要分和元转换的金额字段
	protected $amount_data = ['in_price','out_price1','out_prcie2','out_price3','out_price4'];


	protected $order_data = ['id'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('r_temp_price', $id);
	}

	public function get_temp_price($gid, $cid, $sid, $price_name = 'in_price'){
		$now = date('Y-m-d H:i:s');
		$res = $this->read_list([
			'gid' => $gid,
			'cid' => $cid,
			'begintime[<=]' => $now,
			'endtime[>=]' => $now,
			'orderby' => 'id^DESC'
		]);
		if(!$res['count']){
			return False;
		}
		$company_price = False;
		foreach($res['data'] as $val){
			if($val['sid'] == $sid){
				return $val[$price_name];
			}
			if($val['sid'] == -1){
				$company_price = $val[$price_name];
			}
		}
		return $company_price;
	}

	public function get_temp_prices($gids, $cid, $sid, $price_name = 'in_price'){
		$now = date('Y-m-d H:i:s');
		$res = $this->read_list_nopage([
			'gid' => $gids,
			'cid' => $cid,
			'begintime[<=]' => $now,
			'endtime[>=]' => $now,
			'orderby' => 'id^DESC'
		]);
		if(!$res){
			return [];
		}
		$result = [];
		$company_result = [];
		foreach($res as $val){
			$gid = $val['gid'];
			if($val['sid'] == $sid){
				$price = $val[$price_name];
				if(!in_array($gid, $result)){
					$result[$gid] = $price;
				}
			}
			if($val['sid'] == -1){
				$price = $val[$price_name];
				if(!in_array($gid, $company_result)){
					$company_result[$gid] = $price;
				}
			}
		}

		foreach($company_result as $gid=>$price){
			if(!in_array($gid, $result)){
				$result[$gid] = $price;
			}
		}

		return $result;
	}

	public function get_temp_list($status, $gid, $cid, $sid){
		$now = date('Y-m-d H:i:s');

		$g_res = $this->app->db->select('o_goods', '*', ['id'=>$gid]);
		if(!$g_res){
			error(1423);
		}

		if($status == 1){
			//生效中
			$res = $this->read_list([
				'gid' => $gid,
				'cid' => $cid,
				'begintime[<=]' => $now,
				'endtime[>=]' => $now,
				'orderby' => 'id^DESC'
			]);
		}
		elseif($status == 2){
			//未生效
			$res = $this->read_list([
				'gid' => $gid,
				'cid' => $cid,
				'begintime[>]' => $now,
				'orderby' => 'id^DESC'
			]);
		}
		elseif($status == 3){
			//已过期
			$res = $this->read_list([
				'gid' => $gid,
				'cid' => $cid,
				'endtime[<]' => $now,
				'orderby' => 'id^DESC'
			]);
		}

		if($res['count']){
			$new_data = $res['data'];
			$my_flag = 0;
			foreach($res['data'] as $key=>$val){
				if($val['sid'] != $sid && $val['sid'] != -1) {
					unset($new_data[$key]);
					continue;
				}
				if($val['sid'] == $sid){
					$my_flag = 1;
					$new_data[$key]['range'] = '当前仓库';
				}
				if($val['sid'] == -1){
					$new_data[$key]['range'] = '当前公司';
				}
				$new_data[$key]['gname'] = $g_res[0]['name'];
				$new_data[$key]['gcode'] = $g_res[0]['code'];
				$new_data[$key]['gbarcode'] = $g_res[0]['barcode'];

			}
			$new_data2 = [];
			foreach($new_data as $key=>$val){
				if($my_flag && $val['sid'] == -1) {
				}
				else{
					$new_data2[] = $val;
				}
			}

			$res['data'] = $new_data2;
			$res['count'] = count($res['data']);
		}
		return $res;

	}
	
}

