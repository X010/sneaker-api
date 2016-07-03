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

class PaymentNoteDetail extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['payment_id','account_id','account_name','amount_price','memo'];

	//需要分和元转换的金额字段
	protected $amount_data = ['amount_price'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_payment_note_detail', $id);
	}

	public function delete_by_noteid($payment_id){
		$this->delete(['payment_id'=>$payment_id]);
		return True;
	}
	
}

