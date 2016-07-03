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

class SettleProxyGlist extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['settle_id','gid','gcode','gname','gbarcode','gunit','gspec','gtax_rate','last_rest_total',
		'last_rest_amount','current_sell_total','current_expect_total','proxy_amount','current_expect_amount','current_sell_amount',
		'current_real_total','current_real_amount','current_after_discount_amount','discount','current_rest_total','current_rest_amount'];

	//搜索字段
	protected $search_data = ['gname', 'gcode', 'gbarcode'];

	protected $order_data = ['gid','id'];

	protected $amount_data = ['last_rest_amount','current_expect_amount','current_real_amount','current_after_discount_amount',
		'current_rest_amount','current_sell_amount'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_settle_proxy_glist', $id);
	}

	public function get_tax_group($settle_id)
	{
		$settle_id = strval($settle_id);
		$sql = "select gtax_rate as tax_rate,sum(current_after_discount_amount) as amount";
		$sql .= " from `b_settle_proxy_glist`";
		$sql .= "where settle_id=" . $settle_id . " group by gtax_rate order by gtax_rate desc";

		$r_res = $this->app->db->query($sql)->fetchAll();
		$result = [];
		foreach ($r_res as $val) {
			$amount = fen2yuan($val['amount']);
			$tax_res = get_tax($amount, $val['tax_rate']);
			$result[] = [
				'tax_rate' => $val['tax_rate'],
				'amount_price' => $amount,
				'tax_price' => format_yuan($tax_res['tax_price'])
			];
		}
		return $result;
	}
}

