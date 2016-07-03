<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of customer
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */


class CustomerTmp extends Object{

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = ['*cid', 'suid', 'period', 'cname', 'province', 'country','city','street','address',
        'contractor','phone','account','password','ctype','areapro','areacity','areazone','gtids','status','urgent','memo'];

    protected $search_data = ['cname'];

    //可排序的字段
    protected $order_data = ['status','id'];

    /**
     * constructor
	 *
     * @param  int 	$id 	ID
     */
	public function __construct($id = NULL){
		parent::__construct('o_customer_tmp', $id);
	}

}




