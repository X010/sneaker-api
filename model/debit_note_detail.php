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
class DebitNoteDetail extends Object{

	/**
	 * 数据库字段（只允许以下字段写入）
	 */
	protected $format_data = ['debit_id','account_id','account_name','amount_price','memo'];

	//需要分和元转换的金额字段
	protected $amount_data = ['amount_price'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('b_debit_note_detail', $id);
	}

	/**
	 * 通过单据号删除所有明细
	 *
	 * @param int $debit_id 收款单号
	 * @return bool
     */
	public function delete_by_noteid($debit_id){
		$this->delete(['debit_id'=>$debit_id]);
		return True;
	}
	
}

